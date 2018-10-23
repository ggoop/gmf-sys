<?php

namespace Gmf\Sys\Libs;
use Closure;
use Gmf\Sys\Builder;
use Gmf\Sys\Models\Profile;

class Common {
	/**
	 * 获取参数配置信息
	 * @param  string $name 参数名称
	 * @return [type]       [description]
	 */
	public static function getProfileValue($name = '') {
		return Profile::getValue($name);
	}
	/**
	 * 取排名的前top数据，并对其它数据进行合并
	 * @param  Collection   $arrary    [description]
	 * @param  integer      $top       [description]
	 * @param  Array|array  $sumFields [description]
	 * @param  Closure|null $callback  function(Builder $b){}
	 * @return [type]                  [description]
	 */
	public static function takeOtherSum(Collection $arrary, $top = 5, Array $sumFields = [], Closure $callback = null) {

		$topData = $arrary->take($top);
		//other
		$other = $arrary->slice($top);
		if ($other->count()) {
			$b = new Builder;
			$callback && $callback($b);

			$other->each(function ($item, $key) use ($b, $sumFields) {
				foreach ($sumFields as $key => $value) {
					$b->{$value} = $b->{$value}+$item->{$value};
				}
			});
			$topData->push($b);
		}
		return $topData;
	}
	public static function trimAll($str) {
		$qian = array(" ", "　", "\t", "\n", "\r");
		$str = str_replace($qian, '', $str);
		return $str;
	}
	public static function EncryptDES($data, $key) {
		$cipher = "DES-ECB";
		$key_size = 8;
		if (in_array($cipher, openssl_get_cipher_methods())) {
			$ivlen = openssl_cipher_iv_length($cipher);

			$iv = openssl_random_pseudo_bytes($ivlen);
			$key = substr(strtoupper(md5($key)), 0, $key_size);
			$data = openssl_encrypt($data, $cipher, $key, $options = 0, $iv);
		}
		return $data;
	}
	public static function DecryptDES($data, $key) {
		$cipher = "DES-ECB";
		$key_size = 8;
		try {
			if (in_array($cipher, openssl_get_cipher_methods())) {
				$ivlen = openssl_cipher_iv_length($cipher);
				$iv = openssl_random_pseudo_bytes($ivlen);
				$key = substr(strtoupper(md5($key)), 0, $key_size);
				$data = openssl_decrypt($data, $cipher, $key, $options = 0, $iv);
				return $data;
			}
			return $data;
		} catch (\Exception $ex) {

		}
		return $data;
  }
  
  public static function EncryptAES($data, $key) {
    $cipher = "aes-128-cbc";
		if (in_array($cipher, openssl_get_cipher_methods())) {
			$ivlen = openssl_cipher_iv_length($cipher);
			$iv = openssl_random_pseudo_bytes($ivlen);
			$data = openssl_encrypt($data, $cipher, $key, $options = 0, $iv);
		}
		return base64_encode($data);
  }
  public static function DecryptAES($data, $key) {
    $data=base64_decode($data);
    $cipher = "aes-128-cbc";
    if (in_array($cipher, openssl_get_cipher_methods())) {
      $ivlen = openssl_cipher_iv_length($cipher);
      $iv = openssl_random_pseudo_bytes($ivlen);
      $data = openssl_decrypt($data, $cipher, $key, $options = 0, $iv);
      return $data;
    }
    return $data;
  }
	public static function listDirs($dir) {
		$alldirs = array();
		$dirs = glob($dir . '/*', GLOB_ONLYDIR);
		if (count($dirs) > 0) {
			foreach ($dirs as $d) {
				$alldirs[] = $d;
			}
		}
		foreach ($dirs as $dir) {
			static::listDirs($dir);
		}
		return $alldirs;
	}
	public static function listFiles($dir, $pattern = '*') {
		$dirs = static::listDirs($dir);
		$dirs[] = $dir;
		$files = [];
		foreach ($dirs as $k => $v) {
			$files = array_merge($files, glob($v . DIRECTORY_SEPARATOR . $pattern));
		}
		return $files;
	}
}
