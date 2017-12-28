<?php

namespace Gmf\Sys\Notifications;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordEmail extends Notification {
	/**
	 * The password reset token.
	 *
	 * @var string
	 */
	public $token;

	/**
	 * Create a notification instance.
	 *
	 * @param  string  $token
	 * @return void
	 */
	public function __construct($token) {
		$this->token = $token;
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
		return (new MailMessage)->subject('HUB帐户密码重置')
			->line('您在' . config('app.name') . '使用了密码重置功能, 本次请求的邮件验证码是:')
			->action($this->token, '')
			->line('此邮件30分钟内有效，如果你忽略这条信息，密码将不进行更改')
			->salutation('祝使用愉快！')
			->level('success')
			->markdown('gmf::notifications.auth.email');
	}
}
