<?php

namespace Gmf\Sys\Http\Controllers;

use Gmf\Sys\Builder;
use Gmf\Sys\Database\DataQuery;
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
		$queryInfo->id($model->id)->name($model->name)->memo($model->memo)->comment($model->comment);
		if ($model->entity) {
			$queryInfo->entity_id($model->entity->id)
				->entity_name($model->entity->name)
				->entity_comment($model->entity->comment);
		}
		$queryCase->query($queryInfo);

		$fields = [];
		if (count($model->fields) > 0) {
			foreach ($model->fields as $f) {
				$field = new Builder;
				$field->name($f->name);
				$field->hide(intval($f->hide));
				$fields[] = $field;
			}
		} else if ($model->entity) {
			$entityFields = Models\EntityField::where('entity_id', $model->entity->id)->where('collection', '0')->get();
			foreach ($entityFields as $f) {
				if ($f->name == 'created_at' || $f->name == 'updated_at' || $f->name == 'deleted_at') {
					continue;
				}

				$field = new Builder;
				$field->name($f->name);
				$fields[] = $field;
			}
		}
		$queryCase->fields($fields);
		//匹配项
		$matchItems = explode(";", $model->matchs);
		$queryCase->matchs($matchItems);

		$queryCase->filter($model->filter);
		//条件
		$wheres = [];
		if ($request->has('wheres')) {
			$indatas = $request->wheres;
			foreach ($indatas as $key => $value) {
				if (!$value || empty($value['name']) || empty($value['value'])) {
					continue;
				}

				$field = new Builder;
				$field->name($value['name']);
				if (!empty($value['value'])) {
					$field->value($value['value']);
				}
				if (!empty($value['operator'])) {
					$field->operator($value['operator']);
				}
				$wheres[] = $field;
			}
		}
		$queryCase->wheres($wheres);
		//排序
		$orders = [];
		if ($request->has('orders')) {
			$indatas = $request->orders;
			foreach ($indatas as $key => $value) {
				if (!$value || empty($value['name'])) {
					continue;
				}
				$field = new Builder;
				$field->name($value['name']);
				if (!empty($value['direction'])) {
					$field->direction($value['direction']);
				}
				$orders[] = $field;
			}
		}
		$queryCase->orders($orders);
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
			$queryBuilder->addSelect($f->name, '', '', $f->toArray());
		}
		foreach ($queryCase->wheres as $f) {
			if (!empty($f->operator)) {
				$queryBuilder->addWhere($f->name, $f->operator, $f->value);
			} else {
				$queryBuilder->addWhere($f->name, $f->value);
			}
		}
		foreach ($queryCase->orders as $f) {
			if (!empty($f->direction)) {
				$queryBuilder->addOrder($f->name, $f->direction);
			} else {
				$queryBuilder->addOrder($f->name);
			}
		}
		$query = $queryBuilder->build();

		if ($request->custFilter) {
			$query->whereRaw($request->custFilter);
		}
		if (!empty($request->q)) {
			$query->where(function ($query) use ($request, $queryCase, $queryBuilder) {
				foreach ($queryCase->matchs as $f) {
					if (empty($f)) {
						continue;
					}

					$field = $queryBuilder->parseField($f);
					if ($field) {
						$query->orWhere($field->dbFieldName, 'like', '%' . $request->q . '%');
					}
				}
			});
		}

		$data = $query->paginate($pageSize);
		// } catch (Exception $e) {
		// 	$error = $e;
		// }
		$schema = $queryBuilder->getSchema();
		$schema->setAttributes($queryCase->query->toArray());
		if ($error) {
			return $this->toError($error, function ($data) use ($schema) {
				$data->schema($schema);
			});
		} else {
			return $this->toJson($data, function ($data) use ($schema) {
				$data->schema($schema);
			});
		}

	}
}
