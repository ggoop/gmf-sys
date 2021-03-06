<?php

namespace Gmf\Sys\Http\Controllers;

use Gmf\Sys\Models;
use Gmf\Sys\Query\EntityQuery;
use Gmf\Sys\Query\QueryCase;
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
		$qc = QueryCase::create();
		$caseID = $request->input('case_id');
		$queryInfo = $qc->getQueryInfo($request, $queryID, $caseID);
		return $this->toJson($queryInfo);
	}
	public function getCases(Request $request, string $queryID) {
		$query = Models\QueryCase::select('id', 'name');
		$query->where('query_id', $queryID);
		$data = $query->get();

		return $this->toJson($data);
	}

	public function query(Request $request, string $queryID = '') {
		$pageSize = $request->input('size', 10);

		$qc = QueryCase::create();
		$qc->fromQuery($request, $queryID);

		$data = [];
		$error = false;
		// try {
		$entityQuery = EntityQuery::create($qc->query->entity_name);
		if ($request->input('distinct')) {
			$entityQuery->distinct();
		}

		foreach ($qc->fields as $f) {
			if (empty($f)) {
				continue;
			}
			$entityQuery->addSelect($f->name, '', '', $f->toArray());
		}

		foreach ($qc->wheres as $f) {
			if (!empty($f->type) && $f->type === 'item') {
				$entityQuery->addWhere($f->name, $f->operator, $f->value);
			} else {
				$entityQuery->addWheres($f);
			}
		}
		foreach ($qc->orders as $f) {
			if (!empty($f->direction)) {
				$entityQuery->addOrder($f->name, $f->direction);
			} else {
				$entityQuery->addOrder($f->name);
			}
		}
		$query = $entityQuery->build();
		//var_dump($entityQuery->toSql());
		if ($request->custFilter) {
			$query->whereRaw($request->custFilter);
		}
		if ($qc->filter) {
			$query->whereRaw($qc->filter);
		}
		if (!empty($request->q)) {
			$query->where(function ($query) use ($request, $qc, $entityQuery) {
				foreach ($qc->matchs as $f) {
					if (empty($f)) {
						continue;
					}
					$field = $entityQuery->parseField($f);
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
		$schema = $entityQuery->getSchema();
		//$schema->setAttributes($qc->query->toArray());
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
