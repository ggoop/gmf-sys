<?php

namespace Gmf\Sys\Notifications;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyMail extends Notification {
	/**
	 * The password reset vcode.
	 *
	 * @var string
	 */
	public $vcode;

	/**
	 * Create a notification instance.
	 *
	 * @param  string  $vcode
	 * @return void
	 */
	public function __construct($vcode) {
		$this->vcode = $vcode;
	}

	/**
	 * Get the notification's channels.
	 *
	 * @param  mixed  $notifiable
	 * @return array|string
	 */
	public function via($notifiable) {
		return ['mail'];
	}

	/**
	 * Build the mail representation of the notification.
	 *
	 * @param  mixed  $notifiable
	 * @return \Illuminate\Notifications\Messages\MailMessage
	 */
	public function toMail($notifiable) {
		return (new MailMessage)->subject(config('app.name') . '帐户邮件认证')
			->line('您正在申请' . config('app.name') . '的邮件认证服务，本次请求的邮件验证码是：')
			->action($this->vcode->token, '')
			->line('此验证码5分钟内有效，如非本人操作，请忽略该邮件。')
			->salutation('祝使用愉快!')
			->level('success')
			->markdown('gmf::notifications.auth.email');
	}
}
