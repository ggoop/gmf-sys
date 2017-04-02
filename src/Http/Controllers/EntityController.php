<?php

namespace Gmf\Sys\Http\Controllers;

use Gmf\Sys\Models;
use Illuminate\Http\Request;

class EntityController extends Controller {
	public function index(Request $request) {
		$query = Models\Entity::select('id', 'name', 'comment', 'tableName', 'type');
		$loadDetail = $request->input('d');
		if ($loadDetail) {
			$query->with([
				'fields' => function ($query) {
					$query->select('entity_id', 'id', 'name', 'comment', 'type_id', 'type_type', 'collection', 'dValue', 'sequence');
				},
				'fields.type' => function ($query) {
					$query->select('id', 'name', 'comment');
				}]);
		}
		if ($request->input('q')) {
			$query->where('name', 'like', '%' . $request->input('q') . '%');
		}
		if ($request->input('t')) {
			$query->where('type', $request->input('t'));
		}
		$data = $query->get();
		if ($data && $loadDetail) {
			foreach ($data as $d) {
				$d->fields && $d->fields->makeHidden(['entity_id', 'type_id', 'type_type']);
			}
		}
		return $this->toJson($data);
	}
	public function show(Request $request, string $entityId) {
		$query = Models\Entity::select('id', 'name', 'comment', 'tableName', 'type');
		$query->with([
			'fields' => function ($query) {
				$query->select('entity_id', 'id', 'name', 'comment', 'type_id', 'type_type', 'collection', 'dValue', 'sequence');
			},
			'fields.type' => function ($query) {
				$query->select('id', 'name', 'comment');
			}]);
		$data = $query->where('id', $entityId)->orWhere('name', $entityId)->first();
		if ($data) {
			$data->fields && $data->fields->makeHidden(['entity_id', 'type_id', 'type_type']);
		}
		return $this->toJson($data);
	}
	public function getEnum(Request $request, string $enum = '') {
		$query = Models\Entity::select('id', 'name', 'comment');
		$query->with([
			'fields' => function ($query) {
				$query->select('entity_id', 'name', 'comment');
			}]);
		$data = $query->where('id', $enum)->orWhere('name', $enum)->first();
		if ($data) {
			$data->fields && $data->fields->makeHidden(['entity_id']);
		}
		return $this->toJson($data);
	}
}
