<?php

namespace Gmf\Sys\Notifications;

use Gmf\Sys\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class MailChannel implements ShouldQueue {
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
		$toUsers = $this->data['to_user'];
		if (!is_array($toUsers)) {
			$toUsers = [$toUsers];
		}
		$mailAbled = Mail::to($toUsers);
		if (!empty($this->data['to_cc'])) {
			$mailAbled->cc($this->data['to_cc']);
		}
		if (!empty($this->data['to_bcc'])) {
			$mailAbled->bcc($this->data['to_bcc']);
		}
		if (is_array($this->data['content'])) {
			$mailAbled->send(new MailNodify($this->data['content']));
		} else {
			$mailAbled->send($this->data['content']);
		}
	}
	public function failed(\Exception $exception) {
		Notification::markAsError($this->nid, '失败', $exception);
	}
}