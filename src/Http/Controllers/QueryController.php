<?php

namespace Gmf\Sys\Http\Controllers;

use Gmf\Sys\Builder;
use Gmf\Sys\DataBase\DataQuery;
use Gmf\Sys\Models;
use Illuminate\Http\Request;

class QueryController extends Controller {
	public function index(Request $request) {
		$query = Models\Query::select('id', 'name', 'comment', 'memo');
		$loadDetail = $request->input('d');
		if ($request->input('q')) {
			$query->where('name', 'like', '%' . $request->input('q'), '%');
		}
		if ($loadDetail) {
			$query->with(['fields' => function ($query) {
				$query->select('name', 'comment', 'sequence', 'hide', 'query_id');
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
		$data = $this->buildQueryCase($request, $queryID);
		return $this->toJson($data);
	}
	private function buildQueryCase(Request $request, string $queryID) {
		$queryCase = new Builder;
		$query = Models\Query::with('fields', 'entity');
		$query->where('id', $queryID)->orWhere('name', $queryID);
		$model = $query->first();
		if (!$model) {
			return $this->toError('not find query');
		}
		$queryInfo = new Builder;
		$queryInfo->id($model->id)->name($model->name)->memo($model->memo)->comment($model->comment)
			->entity_id($model->entity->id)
			->entity_name($model->entity->name)
			->entity_comment($model->entity->comment);

		$queryCase->query($queryInfo);

		$fields = [];
		if (count($model->fields) > 0) {
			foreach ($model->fields as $f) {
				$field = new Builder;
				$field->name($f->name);
				$fields[] = $field;
			}
		} else {
			$entityFields = Models\EntityField::where('entity_id', $model->entity->id)->where('collection', '0')->get();
			foreach ($entityFields as $f) {
				$field = new Builder;
				$field->name($f->name);
				$fields[] = $field;
			}
		}
		$queryCase->fields($fields);
		return $queryCase;
	}
	public function query(Request $request, string $queryID) {
		$pageSize = $request->input('size', 10);

		$queryCase = $this->buildQueryCase($request, $queryID);
		$data = [];
		$error = false;
		// try {
		$queryBuilder = DataQuery::create($queryCase->query->entity_name);

		foreach ($queryCase->fields as $f) {
			$queryBuilder->addSelect($f->name);
		}
		$query = $queryBuilder->build();

		$data = $query->paginate($pageSize);
		// } catch (Exception $e) {
		// 	$error = $e;
		// }
		if ($error) {
			return $this->toError($error, function ($data) use ($queryBuilder) {
				$data->schema($queryBuilder->getSchema());
			});
		} else {
			return $this->toJson($data, function ($data) use ($queryBuilder) {
				$data->schema($queryBuilder->getSchema());
			});
		}

	}
}
