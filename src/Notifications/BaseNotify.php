<?php

namespace Gmf\Sys\Notifications;
use Gmf\Sys\Models\Notification;
use Gmf\Sys\Notifications\MailChannel;
use Gmf\Sys\Notifications\SmsChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BaseNotify implements ShouldQueue {
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
	protected $fm_user;
	protected $data;
	public function via() {
		return [];
	}
	public function notify($fm_user, $data) {
		$this->fm_user = $fm_user;
		$this->data = $data;
		$this->handle();
	}
	public function handle() {
		$channels = $this->via();
		foreach ($channels as $k => $channel) {
			$data = false;
			$method = str_replace('Channel', '', class_basename($channel));
			if ($method == 'database') {
				$data = $this->toArray($this->fm_user, $this->data);
			} else {
				$data = $this->{camel_case('to_' . $method)}($this->fm_user, $this->data);
			}
			if (!empty($data) && !empty($data['to_user'])) {
				$this->sendToNotify($data['to_user'], $data, $channel);
			}
		}
	}
	private function sendToNotify($toUsers, $data, $channel) {
		$nid = [];
		if (is_array($toUsers)) {
			foreach ($toUsers as $key => $to) {
				$nid[] = $this->storeNotify($to, $data, $channel);
			}
		} else {
			$nid[] = $this->storeNotify($toUsers, $data, $channel);
		}

		if ($channel == 'database') {
			Notification::markAsArrived($nid);
		} else if ($channel == 'mail') {
			MailChannel::dispatch($data, $nid);
		} else if ($channel == 'sms') {
			SmsChannel::dispatch($data, $nid);
		} else {
			$channel::dispatch($data, $nid);
		}
	}
	protected function storeNotifies($toUsers, $data, $channel = 'database') {
		if (is_array($toUsers)) {
			foreach ($toUsers as $key => $toUser) {
				$this->storeNotify($toUser, $data, $channel);
			}
		} else {
			$this->storeNotify($toUsers, $data, $channel);
		}
	}
	private function storeNotify($toUser, $data, $channel = 'database') {
		$datas = ['user_id' => $toUser->id, 'via' => $channel];
		if (!empty($data['type'])) {
			$datas['type'] = $data['type'];
		}
		if (!empty($data['fm_user'])) {
			$datas['fm_user_id'] = $data['fm_user']->id;
		}
		if (!empty($data['src'])) {
			$datas['src_id'] = $data['src']->id;
			$datas['src_type'] = get_class($data['src']);
		}
		if (!empty($data['content'])) {
			if (is_object($data['content']) || is_array($data['content'])) {
				$datas['content'] = serialize($data['content']);
			} else {
				$datas['content'] = $data['content'];
			}
		}
		return Notification::create($datas)->id;
	}
}
