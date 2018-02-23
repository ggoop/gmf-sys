<?php

namespace Gmf\Sys\Query;
use Exception;
use GAuth;
use Gmf\Sys\Builder;
use Gmf\Sys\Models;
use Illuminate\Http\Request;

class QueryCase {
	public $wheres = [];
	public $orders = [];
	public $fields = [];
	public $matchs = [];
	public $context = [];
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

	public function getQueryInfo(Request $request = null, string $queryID = '', $caseID = '') {
		if (empty($queryID)) {
			return false;
		}
		$caseModel = false;
		$queryInfo = new Builder;
		$queryInfo->wheres([])->orders([])->fields([]);

		$query = Models\Query::with('fields', 'entity', 'orders', 'wheres', 'component');
		$query->where('id', $queryID)->orWhere('name', $queryID);
		$model = $query->first();
		$mainEntity = false;
		if (empty($model)) {
			$mainEntity = Models\Entity::where('id', $queryID)->orWhere('name', $queryID)->first();
			if (empty($mainEntity)) {
				throw new \Exception("not find query");
			}
		} else {
			$mainEntity = $model->entity;
		}
		if ($model) {
			$queryInfo->query_id($model->id)
				->query_type_enum($model->type_enum)
				->query_name($model->name)
				->query_memo($model->memo)
				->query_comment($model->comment ?: $model->name)
				->size($model->size);
		}
		if ($model && $model->component) {
			$component = new Builder;
			$component->id($model->component->id)
				->code($model->component->code)
				->name($model->component->name);
			$queryInfo->component($component);
		}
		if ($model && $model->entity) {
			$queryInfo->entity_id($model->entity->id)->entity_name($model->entity->name)->entity_comment($model->entity->comment);
		} else if ($mainEntity) {
			$queryInfo->entity_id($mainEntity->id)->entity_name($mainEntity->name)->entity_comment($mainEntity->comment);
		}
		if ($caseID) {
			$caseModel = Models\QueryCase::where('query_id', $model->id)->where('id', $caseID)->first();
			$caseModel->data = json_decode($caseModel->data);
		}
		if ($caseModel) {
			$queryInfo->case_name($caseModel->name)->case_id($caseModel->id);
		} else {
			$queryInfo->case_name('')->case_id('');
		}
		$fields = [];
		$wheres = [];
		$orders = [];
		if ($caseModel) {
			if ($caseModel->data && $caseModel->data->fields) {
				$fields = $caseModel->data->fields;
			}
			if ($caseModel->data && $caseModel->data->wheres) {
				$wheres = $caseModel->data->wheres;
			}
			if ($caseModel->data && $caseModel->data->orders) {
				$orders = $caseModel->data->orders;
			}
		} else if ($model) {
			//fields
			if (count($model->fields) > 0) {
				foreach ($model->fields as $f) {
					$field = new Builder;
					$field->name($f->name);
					$field->comment($f->comment ?: $f->name);
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
					$field->comment($f->comment ?: $f->name);
					$fields[] = $field;
				}
			}
			//wheres
			if (count($model->wheres) > 0) {
				foreach ($model->wheres as $f) {
					$field = new Builder;
					$field->name($f->name);
					$field->comment($f->comment ?: $f->name);
					$field->hide(intval($f->hide));
					$field->value($f->value);
					$field->operator_enum($f->operator_enum);
					$field->type_enum($f->type_enum);

					$field->ref_id($f->ref_id);
					$field->ref_values($f->ref_values);
					$field->ref_filter($f->ref_filter);
					$wheres[] = $field;
				}
			}
			//orders
			if (count($model->orders) > 0) {
				foreach ($model->orders as $f) {
					$field = new Builder;
					$field->name($f->name);
					$field->comment($f->comment ?: $f->name);
					$field->direction($f->direction);
					$orders[] = $field;
				}
			}
		}
		$queryInfo->fields($fields);
		$queryInfo->wheres($wheres);
		$queryInfo->orders($orders);

		//匹配项
		$matchItems = [];
		if ($model && $model->matchs) {
			$matchItems = explode(";", $model->matchs);
		}
		$queryInfo->matchs($matchItems);

		//其它过滤项
		if ($model) {
			$queryInfo->filter($model->filter);
		}
		return $queryInfo;
	}
	public function fromRequest(Request $request) {
		$this->parseContext($request);
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
		$temps = $request->input('fields');
		if ($temps) {
			$parse = Field::create();
			$this->fields = $parse->parse($temps);
		}
	}

	public function fromQuery(Request $request = null, string $queryID = '') {
		$this->parseContext($request);
		$this->query = $this->getQueryInfo($request, $queryID);
		if (!empty($this->query)) {
			//栏目
			$this->fields = $this->query->fields;
			//匹配项
			$this->matchs = $this->query->matchs;
			//其它过滤项
			$this->filter = $this->query->filter;
		}
		if (!empty($this->filter)) {
			foreach ($this->context as $key => $value) {
				$this->filter = str_replace('#{' . $key . '}#', "'" . $value . "'", $this->filter);
			}
		}
		//优先使用方案栏目
		if (!empty($request)) {
			$temps = $request->input('fields');
			if ($temps) {
				$parse = Field::create();
				$this->fields = $parse->parse($temps);
			}
		}
		if (!empty($this->query) && (empty($this->fields) || count($this->fields) == 0)) {
			$this->fields = $this->query->fields;
		}
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
		if (!empty($this->query) && (empty($this->orders) || count($this->orders) == 0)) {
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
	public static function attachWhere($query, $caseWhere, $column = '') {
		if (empty($query) || empty($caseWhere)) {
			return $query;
		}

		if (empty($column)) {
			$column = $caseWhere->name;
		}
		$value = $caseWhere->value;
		if (in_array($caseWhere->operator, ['missing', 'null'])) {
			$query->whereNull($column, $caseWhere->boolean);
		}
		if (in_array($caseWhere->operator, ['exists', 'not_null'])) {
			$query->whereNotNull($column, $caseWhere->boolean);
		}
		if (in_array($caseWhere->operator, ['=', 'term', 'equal']) && !empty($value)) {
			$query->where($column, '=', $value, $caseWhere->boolean);
		}
		if (in_array($caseWhere->operator, ['!=', '<>', 'not_term', 'not_equal']) && !empty($value)) {
			$query->where($column, '!=', $value, $caseWhere->boolean);
		}
		if (in_array($caseWhere->operator, ['gt', '>', 'greater_than']) && !empty($value)) {
			$query->where($column, '>', $value, $caseWhere->boolean);
		}
		if (in_array($caseWhere->operator, ['gte', '>=', 'greater_than_equal']) && !empty($value)) {
			$query->where($column, '>=', $value, $caseWhere->boolean);
		}
		if (in_array($caseWhere->operator, ['lt', '<', 'less_than']) && !empty($value)) {
			$query->where($column, '<', $value, $caseWhere->boolean);
		}
		if (in_array($caseWhere->operator, ['lte', '<=', 'less_than_equal']) && !empty($value)) {
			$query->where($column, '<=', $value, $caseWhere->boolean);
		}
		if (in_array($caseWhere->operator, ['match', 'like']) && !empty($value)) {
			$query->where($column, 'like', '%' . $value . '%', $caseWhere->boolean);
		}
		if (in_array($caseWhere->operator, ['not_match', 'not_like']) && !empty($value)) {
			$query->where($column, 'not like', $value, $caseWhere->boolean);
		}
		if (in_array($caseWhere->operator, ['left_match', 'left_like']) && !empty($value)) {
			$query->where($column, 'like', $value . '%', $caseWhere->boolean);
		}
		if (in_array($caseWhere->operator, ['right_match', 'right_like']) && !empty($value)) {
			$query->where($column, 'like', '%' . $value, $caseWhere->boolean);
		}
		if (in_array($caseWhere->operator, ['in', 'terms', 'not_in', 'not_terms']) && !empty($value)) {
			$query->whereIn($column, $value, $caseWhere->boolean, starts_with($caseWhere->operator, 'not'));
		}
		if (in_array($caseWhere->operator, ['between', 'not_between']) && !empty($value)) {
			$query->whereBetween($column, $value, $caseWhere->boolean, starts_with($caseWhere->operator, 'not'));
		}
		return $query;
	}

	protected function parseContext(Request $request = null) {
		$context = [];
		if ($request) {
			$context['entId'] = GAuth::entId();
			$context['userId'] = GAuth::userId();
		}
		$this->context = $context;
		return $this->context;
	}
}