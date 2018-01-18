<?php

namespace Gmf\Sys\Activities;
use Gmf\Sys\Models\Activity;

class BaseActivity {
	public function removeBy($user, $what, $indentifier) {
		Activity::where('user_id', $user->id)
			->where('what_id', $what->id)
			->where('what_type', get_class($what))
			->where('indentifier', $indentifier)
			->where('type', class_basename(get_class($this)))
			->delete();
	}
	public function addActivity($user, $causer, $what, $indentifier, $content) {
		$datas = [
			'user_id' => $user->id,
			'what_id' => $what->id,
			'what_type' => get_class($what),
			'indentifier' => $indentifier,
			'type' => class_basename(get_class($this)),
		];
		if ($causer) {
			$datas['causer_id'] = $causer->id;
			$datas['causer_type'] = get_class($causer);
		}
		if (is_object($content) || is_array($content)) {
			$datas['content'] = serialize($content);
		} else {
			$datas['content'] = $content;
		}
		Activity::create($datas);
	}
}
