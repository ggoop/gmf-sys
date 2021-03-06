<?php

namespace Gmf\Sys\Notifications;

use Gmf\Sys\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SmsChannel implements ShouldQueue {
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	protected $data;
	protected $nid;
	public $tries = 1;

	/**
	 * 创建一个新的任务实例。
	 *
	 * @param  Podcast  $podcast
	 * @return void
	 */
	public function __construct($data, $nid) {
		$this->data = $data;
		$this->nid = $nid;
	}

	/**
	 * 运行任务。
	 *
	 * @param  AudioProcessor  $processor
	 * @return void
	 */
	public function handle() {
		if (empty($this->data['content'])) {
			throw new \Exception("缺少内容");
		}
		if (empty($this->data['to_user'])) {
			throw new \Exception("缺少参数,to_user");
		}
		if (empty($this->data['content']['sign']) || empty($this->data['content']['tempCode']) || empty($this->data['content']['data'])) {
			throw new \Exception("缺少参数 content.sign||content.tempCode||content.data");
		}
		$toUsers = $this->data['to_user'];
		if (!is_array($toUsers)) {
			$toUsers = [$toUsers];
		}
		foreach ($toUsers as $key => $to) {
			if (!empty($to)) {
				$this->sendSms($to->routeNotificationForNexmo(), $this->data['content']['sign'], $this->data['content']['tempCode'], $this->data['content']['data']);
			}
		}
	}
	public function failed(\Exception $exception) {
		Notification::markAsError($this->nid, '失败', $exception);
	}
	private function sendSms($number, $sign, $tempCode, $data) {
		if (empty($number)) {
			return;
		}

		$params = array();
		// *** 需用户填写部分 ***

		// fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
		$accessKeyId = env('ALIYUN_API_ACCESS_KEY');
		$accessKeySecret = env('ALIYUN_API_ACCESS_SECRET');

		// fixme 必填: 短信接收号码
		$params["PhoneNumbers"] = $number;

		// fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
		$params["SignName"] = $sign;

		// fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
		$params["TemplateCode"] = $tempCode;

		// fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
		$params['TemplateParam'] = $data;

		// fixme 可选: 设置发送短信流水号
		//$params['OutId'] = "12345";

		// fixme 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
		//$params['SmsUpExtendCode'] = "1234567";

		// *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
		if (!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
			$params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
		}

		// 此处可能会抛出异常，注意catch
		$content = $this->request(
			$accessKeyId,
			$accessKeySecret,
			"dysmsapi.aliyuncs.com",
			array_merge($params, array(
				"RegionId" => "cn-hangzhou",
				"Action" => "SendSms",
				"Version" => "2017-05-25",
			))
		);
		return $content;
	}
/**
 * 生成签名并发起请求
 *
 * @param $accessKeyId string AccessKeyId (https://ak-console.aliyun.com/)
 * @param $accessKeySecret string AccessKeySecret
 * @param $domain string API接口所在域名
 * @param $params array API具体参数
 * @param $security boolean 使用https
 * @return bool|\stdClass 返回API接口调用结果，当发生错误时返回false
 */
	public function request($accessKeyId, $accessKeySecret, $domain, $params, $security = false) {
		$apiParams = array_merge(array(
			"SignatureMethod" => "HMAC-SHA1",
			"SignatureNonce" => uniqid(mt_rand(0, 0xffff), true),
			"SignatureVersion" => "1.0",
			"AccessKeyId" => $accessKeyId,
			"Timestamp" => gmdate("Y-m-d\TH:i:s\Z"),
			"Format" => "JSON",
		), $params);
		ksort($apiParams);

		$sortedQueryStringTmp = "";
		foreach ($apiParams as $key => $value) {
			$sortedQueryStringTmp .= "&" . $this->encode($key) . "=" . $this->encode($value);
		}

		$stringToSign = "GET&%2F&" . $this->encode(substr($sortedQueryStringTmp, 1));

		$sign = base64_encode(hash_hmac("sha1", $stringToSign, $accessKeySecret . "&", true));

		$signature = $this->encode($sign);

		$url = ($security ? 'https' : 'http') . "://{$domain}/?Signature={$signature}{$sortedQueryStringTmp}";

		try {
			$content = $this->fetchContent($url);
			return json_decode($content);
		} catch (\Exception $e) {
			return false;
		}
	}

	private function encode($str) {
		$res = urlencode($str);
		$res = preg_replace("/\+/", "%20", $res);
		$res = preg_replace("/\*/", "%2A", $res);
		$res = preg_replace("/%7E/", "~", $res);
		return $res;
	}

	private function fetchContent($url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"x-sdk-client" => "php/2.0.0",
		));

		if (substr($url, 0, 5) == 'https') {
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		}

		$rtn = curl_exec($ch);

		if ($rtn === false) {
			trigger_error("[CURL_" . curl_errno($ch) . "]: " . curl_error($ch), E_USER_ERROR);
		}
		curl_close($ch);

		return $rtn;
	}
}