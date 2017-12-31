<?php

namespace Gmf\Sys\Activities;
use Gmf\Sys\Models\Activity;

class BaseActivity {
	public function removeBy($user, $causer, $indentifier) {
		Activity::where('user_id', $user->id)
			->where('causer_id', $causer->id)
			->where('causer_type', get_class($causer))
			->where('indentifier', $indentifier)
			->where('type', class_basename(get_class($this)))
			->delete();
	}
	public function addActivity($user, $causer, $indentifier, $content) {
		$datas = [
			'user_id' => $user->id,
			'causer_id' => $causer->id,
			'causer_type' => get_class($causer),
			'indentifier' => $indentifier,
			'type' => class_basename(get_class($this)),
		];
		if (is_object($content) || is_array($content)) {
			$datas['content'] = serialize($content);
		} else {
			$datas['content'] = $content;
		}
		Activity::create($datas);
	}
}
