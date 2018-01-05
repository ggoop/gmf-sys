<?php

namespace Gmf\Sys\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class MdFieldType extends Resource {
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request
	 * @return array
	 */
	public function toArray($request) {
		if (empty($this->id)) {
			return false;
		}
		$rtn = [];
		Common::toField($this, $rtn, ['id', 'name', 'comment', 'type']);
		$typeName = '';
		switch ($this->type) {
		case 'string':
			$typeName = '字符';
			break;
		case 'number':
			$typeName = '数值';
			break;
		case 'integer':
			$typeName = '整数';
			break;
		case 'date':
			$typeName = '日期';
			break;
		case 'entity':
			$typeName = '实体';
			break;
		case 'enum':
			$typeName = '枚举';
			break;
		}
		if (!empty($typeName)) {
			$rtn['type_name'] = $typeName;
		}
		return $rtn;
	}
}
