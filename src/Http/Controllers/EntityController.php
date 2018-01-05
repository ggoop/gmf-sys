<?php

namespace Gmf\Sys\Http\Controllers;

use DB;
use GAuth;
use Gmf\Sys\Builder;
use Gmf\Sys\Http\Resources;
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
			foreach ($entity->fields as $fk => $fv) {
				if ($fv->name === 'ent') {
					$entity->is_ent = true;
					break;
				}
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
		if ($entity->is_ent) {
			$query->where('ent_id', GAuth::entId());
		}
		if ($request->filter) {
			$query->whereRaw($request->filter);
		}
		return $query;
	}
	public function index(Request $request) {
		$query = Models\Entity::where('id', '!=', '');
		if ($request->input('d')) {
			$query->with('fields.type');
		}
		if ($pv = $request->input('q')) {
			$query->where(function ($query) use ($pv) {
				$query->orWhere('name', 'like', '%' . $pv . '%')
					->orWhere('comment', 'like', '%' . $pv . '%')
					->orWhere('id', $pv);
			});
		} else {
			$query->where('name', 'not like', '%gmf%');
		}
		if ($pv = $request->input('t')) {
			$query->whereIn('type', $pv);
		} else {
			$query->whereIn('type', ['enum', 'entity']);
		}
		return $this->toJson(Resources\MdEntity::collection($query->paginate($request->input('size', 10))));
	}
	public function show(Request $request, string $entityId) {
		$query = Models\Entity::with('fields.type');
		$query->where(function ($query) use ($entityId) {
			$query->orWhere('name', $entityId)
				->orWhere('id', $entityId);
		});
		return $this->toJson(new Resources\MdEntity($query->first()));
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
