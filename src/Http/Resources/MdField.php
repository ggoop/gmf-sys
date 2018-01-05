<?php

namespace Gmf\Sys\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class MdField extends Resource {
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
		$rtn = [
			'type' => new MdFieldType($this->type),
		];
		Common::toField($this, $rtn, ['id', 'entity_id', 'name', 'comment', 'field_name', 'collection', 'default_value', 'sequence']);

		return $rtn;
	}
}
