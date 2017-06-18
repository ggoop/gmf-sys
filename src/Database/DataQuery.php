<?php

namespace Gmf\Sys\Database;
use Exception;
use Gmf\Sys\Builder;
use Gmf\Sys\Models;
use Illuminate\Support\Facades\DB;

class DataQuery {
	public static $defaultMainAlias = 'a';

	protected $selects = [];
	protected $orders = [];
	protected $wheres = [];

	protected $entities = [];
	protected $mainEntity;
	protected $query;

	public static function create($name, array $parameters = []) {
		return new DataQuery($name, $parameters);
	}
	public function __construct($name, array $parameters = []) {
		$this->mainEntity = new Builder(array_merge(compact('name'), $parameters));
	}
	public function addSelect(string $name, string $alias = '', string $comment = '', array $parameters = []) {
		$this->selects[] = $select = new Builder(array_merge(compact('name', 'alias', 'comment'), $parameters));
		return $select;
	}
	public function addOrder(string $name, $direction = 'asc') {
		$this->orders[] = $order = new Builder(compact('name', 'direction'));
		return $order;
	}
	public function addWhere(string $name, $operator = null, $value = null) {
		$this->wheres[] = $where = new Builder(compact('name', 'operator', 'value'));
		return $where;
	}
	public function build() {
		$this->buildMainEntity();
		$this->buildJoins();
		return $this->query;
	}
	public function getSchema() {
		$schema = new Builder();
		$fields = [];
		foreach ($this->selects as $key => $value) {
			$fields[] = array_only($value->toArray(), ['name', 'alias', 'comment', 'type_id', 'type_type', 'path']);
		}
		$schema->fields($fields);
		return $schema;
	}
	private function buildMainEntity() {
		$this->mainEntity = $this->getEntity($this->mainEntity->name);
		$this->query = DB::table($this->mainEntity->table_name . ' as ' . $this->mainEntity->alias);
	}
	private function buildJoins() {
		foreach ($this->selects as $key => $value) {
			$this->parseField($value);
			$this->query->addSelect($value->dbFieldName . ' as ' . $value->alias);
		}
		foreach ($this->orders as $key => $value) {
			$this->parseField($value);
			$this->query->orderBy($value->dbFieldName, $value->direction);
		}
		foreach ($this->wheres as $key => $value) {
			$this->parseField($value);
			$this->query->where($value->dbFieldName, $value->operator, $value->value);
		}
		foreach ($this->entities as $key => $value) {
			if (empty($value->path) || empty($value->join)) {
				continue;
			}
			$this->query->join(
				$value->table_name . ' as ' . $value->alias,
				$value->join->first,
				$value->join->operator,
				$value->join->second,
				$value->join->type
			);
		}
	}
	private function getEntity($id, $path = '') {
		$ent = Models\Entity::with('fields')->where('id', $id)
			->orwhere('name', $id)
			->first();
		if (empty($ent)) {
			throw new Exception($id . " main entity is null", 1);
		}
		$b = new Builder;
		$b->id($ent->id)
			->table_name($ent->table_name)
			->name($ent->name)
			->comment($ent->comment)
			->type($ent->type);
		$b->alias(static::$defaultMainAlias . count($this->entities));
		$b->path($path);

		$parts = [];
		foreach ($ent->fields as $key => $value) {
			$f = new Builder($value->toArray());
			$f->tableAlias($b->alias);
			$f->dbFieldName($f->tableAlias . '.' . $f->field_name);
			$parts[$value->name] = $f;
		}
		$b->fields($parts);

		if (!empty($path)) {
			$parts = explode('.', $path);
			$joinFieldName = array_pop($parts);
			$parentPath = implode('.', $parts);
			$parentEnt = $this->entities[$parentPath];

			$joinField = $this->getField($parentEnt, $joinFieldName);

			$join = new Builder();
			$join->type('left')->operator('=');
			$join->first($joinField->tableAlias . '.' . $joinField->localKey);
			$join->second($b->alias . '.' . $joinField->foreignKey);
			$b->join($join);
		}
		$this->entities[$path] = $b;
		return $b;
	}
	private function getField($entity, $name) {
		if (!empty($entity->fields[$name])) {
			return $entity->fields[$name];
		}
		foreach ($entity->fields as $key => $value) {
			if ($value->name === $name) {
				return $value;
			}
			if ($value->fieldName === $name) {
				return $value;
			}
		}
		return null;
	}
	private function parseField($field) {
		$path = '';
		$part = '';
		$mdField = null;
		$mdEntity = null;
		$comments = [];

		$parts = explode('.', $field->name);

		$mdEntity = $this->mainEntity;
		while (count($parts) > 1) {
			$part = array_shift($parts);
			if (!empty($path)) {
				$path .= '.';
			}
			$path .= $part;

			$mdField = $this->getField($mdEntity, $part);
			if (empty($mdField)) {
				break;
			}
			$comments[] = $mdField->comment;

			if (empty($this->entities[$path])) {
				$this->getEntity($mdField->type_id, $path);
			}
			$mdEntity = $this->entities[$path];
		}
		$part = array_shift($parts);
		$mdField = $this->getField($mdEntity, $part);
		$comments[] = $mdField->comment;

		$field->tableAlias($mdEntity->alias)->fieldName($mdField->fieldName);

		if (empty($field->alias)) {
			$field->alias(str_replace('.', '_', $field->name));
		}
		if (empty($field->comment)) {
			$field->comment(implode('.', $comments));
		}
		$field->path($field->name);
		$field->name($mdField->name);
		$field->type_id($mdField->type_id)->type_type($mdField->type_type);
		$field->dbFieldName($field->tableAlias . '.' . $field->fieldName);
	}
}
