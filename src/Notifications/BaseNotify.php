<?php

namespace Gmf\Sys\Notifications;
use Gmf\Sys\Models\Notification;

class BaseNotify {
	public function createNotify($type, $fm_user, $to_users, $src, $content) {
		if (!$to_users || count($to_users) == 0) {
			return;
		}
		foreach ($to_users as $to_user) {
			if ($fm_user->id == $to_user->id) {
				continue;
			}
			$datas = [
				'user_id' => $to_user->id,
				'fm_user_id' => $fm_user->id,
				'src_id' => $src->id,
				'src_type' => get_class($src),
				'type' => $type,
			];
			if (is_object($content) || is_array($content)) {
				$datas['content'] = serialize($content);
			} else {
				$datas['content'] = $content;
			}
			Notification::create($datas);
		}
	}
}
