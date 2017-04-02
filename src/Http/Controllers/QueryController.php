<?php

namespace Gmf\Sys\Http\Controllers;

use Gmf\Sys\Models;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QueryController extends Controller {
	public function index(Request $request) {
		$query = Models\Query::select('id', 'name', 'memo');
		$loadDetail = $request->input('d');
		if ($request->input('q')) {
			$query->where('name', 'like', '%' . $request->input('q'), '%');
		}
		if ($loadDetail) {
			$query->with(['fields' => function ($query) {
				$query->select('path', 'name', 'sequence', 'hide', 'query_id');
			}]);
		}
		$data = $query->get();
		if ($data && $loadDetail) {
			foreach ($data as $d) {
				$d->fields && $d->fields->makeHidden(['query_id']);
			}
		}
		return $this->toJson($data);
	}
	public function show(Request $request, string $queryID) {
		$query = Models\Query::select('id', 'name', 'memo')->with(['fields' => function ($query) {
			$query->select('path', 'name', 'sequence', 'hide', 'query_id');
		}]);
		$data = $query->where('id', $queryID)->orWhere('code', $queryID)->first();
		if ($data) {
			$data->fields && $data->fields->makeHidden(['query_id']);
		}
		return $this->toJson($data);
	}
	public function queryData(Request $request, string $queryID) {
		$query = Models\Query::select('id', 'name', 'memo', 'entity_id')->with(['fields' => function ($query) {
			$query->select('path', 'name', 'query_id');
		}, 'entity' => function ($query) {
			$query->select('tableName', 'id');
		}]);
		$query->where('id', $queryID)->orWhere('code', $queryID);
		$model = $query->first();
		if (!$model) {
			return $this->toError('not find query');
		}

		$model->fields && $model->fields->makeHidden(['query_id']);
		$model->makeHidden(['entity', 'entity_id']);
		$data = [];
		$error = false;
		try {

			$query = DB::table($model->entity->tableName);
			foreach ($model->fields as $f) {
				$query->addSelect($f->path);
			}
			$data = $query->get();
		} catch (\Exception $e) {
			$error = $e;
		}
		if ($error) {
			return $this->toError($error, function ($data) use ($model) {
				$data->schema($model);
			});
		} else {
			return $this->toJson($data, function ($data) use ($model) {
				$data->schema($model);
			});
		}

	}
}
