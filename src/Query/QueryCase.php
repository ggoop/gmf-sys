<?php

namespace Gmf\Sys\Query;
use Exception;
use Gmf\Sys\Builder;
use Gmf\Sys\Models;
use Illuminate\Http\Request;

class QueryCase {
	public $wheres = [];
	public $orders = [];
	public $fields = [];
	public $matchs = [];
	public $filter;
	public $query;

	public static function create($datas = null) {
		return new QueryCase($datas);
	}
	public function __construct($datas = null) {
	}
	public static function formatRequest(Request $request) {
		$parse = new QueryCase();
		$parse->fromRequest($request);
		return $parse;
	}

	public function getQueryInfo(string $queryID) {
		$query = Models\Query::with('fields', 'entity', 'orders');
		$query->where('id', $queryID)->orWhere('name', $queryID);
		$model = $query->first();
		if (empty($model)) {
			throw new Exception("not find query");
		}
		$queryInfo = new Builder;
		$queryInfo->id($model->id)
			->name($model->name)->memo($model->memo)->comment($model->comment)
			->wheres([])->orders([])->fields([]);

		if ($model->entity) {
			$queryInfo->entity_id($model->entity->id)
				->entity_name($model->entity->name)
				->entity_comment($model->entity->comment);
		}
		//栏目
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
		$queryInfo->fields($fields);

		//匹配项
		$matchItems = [];
		if ($model->matchs) {
			$matchItems = explode(";", $model->matchs);
		}
		$queryInfo->matchs($matchItems);

		//其它过滤项
		$queryInfo->filter($model->filter);

		return $queryInfo;
	}
	public function fromRequest(Request $request) {
		$temps = $request->input('wheres');
		if ($temps) {
			$parse = Filter::create();
			$this->wheres = $parse->parse($temps);
		}
		$temps = $request->input('orders');
		if ($temps) {
			$parse = Order::create();
			$this->orders = $parse->parse($temps);
		}
	}
	public function fromQuery(string $queryID, Request $request = null) {

		$this->query = $this->getQueryInfo($queryID);
		//栏目

		$this->fields = $this->query->fields;
		//匹配项
		$this->matchs = $this->query->matchs;
		//其它过滤项
		$this->filter = $this->query->filter;
		//优先使用请求条件
		if (!empty($request)) {
			$temps = $request->input('wheres');
			if ($temps) {
				$parse = Filter::create();
				$this->wheres[] = $parse->parse($temps);
			}
		}
		//优先使用请求排序
		if (!empty($request)) {
			$temps = $request->input('orders');
			if ($temps) {
				$parse = Order::create();
				$this->orders = $parse->parse($temps);
			}
		}
		if (empty($this->orders) && count($this->orders) == 0) {
			$this->orders = $this->query->orders;
		}
	}

	/**
	 * [attachWhere description]
	 * @param  [type] $query     [description]
	 * @param  [type] $caseWhere {name,value,operator}
	 * @param  string $boolean   [description]
	 * @param  string $column    [description]
	 * @return [type]            [description]
	 */
	public static function attachWhere($query, $caseWhere, $column = '', $boolean = 'and') {
		if (empty($query) || empty($caseWhere)) {
			return $query;
		}
		if (empty($boolean)) {
			$boolean = 'and';
		}

		if (empty($column)) {
			$column = $caseWhere->name;
		}
		$value = $caseWhere->value;
		if (($caseWhere->operator == 'equal' || $caseWhere->operator == '=') && !empty($value)) {
			if ($boolean === 'or') {
				$query->orWhere($column, $value);
			} else {
				$query->where($column, $value);
			}

		}
		if (($caseWhere->operator == 'not_equal' || $caseWhere->operator == '!=' || $caseWhere->operator == '<>') && !empty($value)) {
			if ($boolean === 'or') {
				$query->orWhere($column, '!=', $value);
			} else {
				$query->where($column, '!=', $value);
			}

		}
		if (($caseWhere->operator == 'greater_than' || $caseWhere->operator == '>') && !empty($value)) {
			if ($boolean === 'or') {
				$query->orWhere($column, '>', $value);
			} else {
				$query->where($column, '>', $value);
			}
		}
		if (($caseWhere->operator == 'less_than' || $caseWhere->operator == '<') && !empty($value)) {
			if ($boolean === 'or') {
				$query->orWhere($column, '<', $value);
			} else {
				$query->where($column, '<', $value);
			}
		}
		if (($caseWhere->operator == 'greater_than_equal' || $caseWhere->operator == '>=') && !empty($value)) {
			if ($boolean === 'or') {
				$query->orWhere($column, '>=', $value);
			} else {
				$query->where($column, '>=', $value);
			}
		}
		if (($caseWhere->operator == 'less_than_equal' || $caseWhere->operator == '<=') && !empty($value)) {
			if ($boolean === 'or') {
				$query->orWhere($column, '<=', $value);
			} else {
				$query->where($column, '<=', $value);
			}
		}
		if ($caseWhere->operator == 'between' && !empty($value) && is_array($value)) {
			if ($boolean === 'or') {
				$query->orWhereBetween($column, $value);
			} else {
				$query->whereBetween($column, $value);
			}

		}
		if ($caseWhere->operator == 'not_between' && !empty($value) && is_array($value)) {
			if ($boolean === 'or') {
				$query->orWhereNotBetween($column, $value);
			} else {
				$query->whereNotBetween($column, $value);
			}
		}
		if ($caseWhere->operator == 'in' && !empty($value) && is_array($value)) {

			if ($boolean === 'or') {
				$query->orWhereIn($column, $value);
			} else {
				$query->whereIn($column, $value);
			}
		}
		if ($caseWhere->operator == 'not_in' && !empty($value) && is_array($value)) {

			if ($boolean === 'or') {
				$query->orWhereNotIn($column, $value);
			} else {
				$query->whereNotIn($column, $value);
			}
		}
		if ($caseWhere->operator == 'null') {

			if ($boolean === 'or') {
				$query->orWhereNull($column);
			} else {
				$query->whereNull($column);
			}
		}
		if ($caseWhere->operator == 'not_null') {

			if ($boolean === 'or') {
				$query->orWhereNotNull($column);
			} else {
				$query->whereNotNull($column);
			}
		}
		if ($caseWhere->operator == 'like' && !empty($value)) {
			if ($boolean === 'or') {
				$query->orWhere($column, 'like', '%' . $value . '%');
			} else {
				$query->where($column, 'like', '%' . $value . '%');
			}
		}
		if ($caseWhere->operator == 'left_like' && !empty($value)) {
			if ($boolean === 'or') {
				$query->orWhere($column, 'like', '%' . $value);
			} else {
				$query->where($column, 'like', '%' . $value);
			}

		}
		if ($caseWhere->operator == 'right_like' && !empty($value)) {
			if ($boolean === 'or') {
				$query->orWhere($column, 'like', $value . '%');
			} else {
				$query->where($column, 'like', $value . '%');
			}
		}
		if ($caseWhere->operator == 'not_like' && !empty($value)) {
			if ($boolean === 'or') {
				$query->orWhere($column, 'not like', '%' . $value . '%');
			} else {
				$query->where($column, 'not like', '%' . $value . '%');
			}
		}
		return $query;
	}
}