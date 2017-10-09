<?php

namespace Gmf\Sys\Http\Controllers;

use DB;
use Gmf\Sys\Builder;
use Gmf\Sys\Models;
use Illuminate\Http\Request;

class EntityController extends Controller {
	/**
	 * 获取数据颁信息
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function pager(Request $request) {
		$currentId = $request->input('id', '');
		$entity = false;
		$result = new Builder(['firstId' => '', 'lastId' => '', 'prevId' => '', 'nextId' => '', 'total_items' => 0]);
		if ($request->input('entity')) {
			$entity = Models\Entity::with('fields')->where('name', $request->input('entity', ''))->first();
		}
		if ($entity) {
			if (empty($request->order)) {
				$request->order = 'created_at';
			}
			//total_items
			$query = $this->pager_build_query($request, $entity);
			$result->total_items($query->count());

			//first
			$query = $this->pager_build_query($request, $entity);
			$query->orderBy($request->order);
			$query->orderBy('id');
			$result->firstId($query->value('id'));

			//last
			$query = $this->pager_build_query($request, $entity);
			$query->orderBy($request->order, 'desc');
			$query->orderBy('id', 'desc');
			$result->lastId($query->value('id'));

			if (!empty($currentId)) {
				$query = $this->pager_build_query($request, $entity);
				$query->where('id', $currentId);
				$currentItem = $query->first();
				if ($currentItem && !empty($currentItem->{$request->order})) {
					//prevId
					$query = $this->pager_build_query($request, $entity);
					$query->where($request->order, '<=', $currentItem->{$request->order});
					$query->orderBy($request->order, 'desc');
					$query->orderBy('id', 'desc');
					$query->where('id', '!=', $currentId);
					$result->prevId($query->value('id'));
				}
				if ($currentItem && !empty($currentItem->{$request->order})) {
					//nextId
					$query = $this->pager_build_query($request, $entity);
					$query->where($request->order, '>=', $currentItem->{$request->order});
					$query->orderBy($request->order);
					$query->orderBy('id');
					$query->where('id', '!=', $currentId);
					$result->nextId($query->value('id'));
				}
			}
		}

		if ($result->firstId == $currentId) {
			$result->firstId('');
		}
		if ($result->lastId == $currentId) {
			$result->lastId('');
		}
		if ($result->prevId == $currentId) {
			$result->prevId('');
		}
		if ($result->nextId == $currentId) {
			$result->nextId('');
		}
		return $this->toJson($result);
	}
	private function pager_build_query(Request $request, $entity) {
		$query = DB::table($entity->table_name);
		if (!empty($request->wheres) && count($request->wheres) && is_array($request->wheres)) {
			foreach ($request->wheres as $key => $value) {
				$query->where($key, $value);
			}
		}
		if ($request->filter) {
			$query->whereRaw($request->filter);
		}
		return $query;
	}
	public function index(Request $request) {
		$query = Models\Entity::select('id', 'name', 'comment', 'table_name', 'type');
		$loadDetail = $request->input('d');
		if ($loadDetail) {
			$query->with([
				'fields' => function ($query) {
					$query->select('entity_id', 'id', 'name', 'comment', 'type_id', 'type_type', 'collection', 'default_value', 'sequence');
				},
				'fields.type' => function ($query) {
					$query->select('id', 'name', 'comment', 'type');
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
		$query = Models\Entity::select('id', 'name', 'comment', 'table_name', 'type');
		$query->with([
			'fields' => function ($query) {
				$query->select('entity_id', 'id', 'name', 'comment', 'type_id', 'type_type', 'collection', 'default_value', 'sequence');
			},
			'fields.type' => function ($query) {
				$query->select('id', 'name', 'comment', 'type');
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
	public function getAllEnums(Request $request) {
		$query = Models\Entity::select('id', 'name', 'comment');
		$query->with([
			'fields' => function ($query) {
				$query->select('entity_id', 'name', 'comment');
			}]);
		$data = $query->where('type', 'enum')->get();
		if ($data) {
			$data = $data->map(function ($item) {
				$item->fields->makeHidden(['entity_id']);
				return $item;
			})->all();
		}
		return $this->toJson($data);
	}
}
