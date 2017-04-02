<?php

namespace Gmf\Sys;
use Illuminate\Support\Fluent;

class Builder extends Fluent {
	public function build(Builder $builder = null) {
		if (!empty($builder)) {
			$builder->build();
		}
	}
	public function setAttributes($attributes = []) {
		foreach ($attributes as $key => $value) {
			$this->attributes[$key] = $value;
		}
	}
}