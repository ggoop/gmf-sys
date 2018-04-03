<?php

namespace Gmf\Sys\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailNodify extends Mailable {
	use Queueable, SerializesModels;

	/**
	 * order 实例。
	 *
	 * @var Order
	 */
	public $content;

	/**
	 * 创建一个新消息实例。
	 *
	 * @return void
	 */
	public function __construct($content) {
		$this->content = $content;
	}

	/**
	 * 构建消息。
	 *
	 * @return $this
	 */
	public function build() {
		$rend = $this->subject($this->content['subject']);
		if (!empty($this->content['markdown'])) {
			$rend->markdown($this->content['markdown'], $this->content['data']);
		}
		return $rend;
	}
}