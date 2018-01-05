<?php

namespace Gmf\Sys\Bp;
use GAuth;
use Gmf\Sys\Builder;
use Gmf\Sys\Models;

class Scode {
	public function generate($type, $content = []) {
		$data = [
			'user_id' => GAuth::id(),
			'client_id' => GAuth::clientId(),
			'type' => $type,
			'content' => serialize($content),
		];
		$code = Models\Scode::create($data);
		$b = new Builder;
		$b->id($code->id)->code($code->code);
		return $b;
	}
	public function parse($id) {
		$c = Models\Scode::find($id);
		if (empty($c)) {
			return false;
		}
		return unserialize($c->content);
	}
}