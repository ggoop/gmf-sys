<?php

namespace Gmf\Sys\Query;
use DB;
use Exception;
use GAuth;
use Gmf\Sys\Builder;
use Gmf\Sys\Models;

class EntityQuery {
  public static $defaultMainAlias = 'a';

  protected $selects = [];
  protected $orders = [];
  protected $wheres = [];

  protected $entities = [];

  protected $mainEntity;
  protected $query;
  protected $distinct = false;

  public static function create($name, array $parameters = []) {
    return new EntityQuery($name, $parameters);
  }
  public function __construct($name, array $parameters = []) {
    $this->mainEntity = new Builder(array_merge(compact('name'), $parameters));
  }
  public function addSelect(string $name, string $alias = '', string $comment = '', array $parameters = []) {
    $parameters = array_except($parameters, ['name', 'alias', 'comment', 'type_id', 'type_type', 'path']);
    $this->selects[] = $select = new Builder(array_merge(compact('name', 'alias', 'comment'), $parameters));
    return $select;
  }
  public function distinct() {
    $this->distinct = true;
    return $this;
  }
  public function addOrder(string $name, $direction = 'asc') {
    $this->orders[] = $order = new Builder(compact('name', 'direction'));
    return $order;
  }
  public function addWheres($filters) {
    $this->wheres[] = $filters;
  }
  public function addWhere(string $name, $operator = null, $value = null) {
    $where = new Builder(compact('name', 'operator', 'value'));
    $where->type('item');
    $this->wheres[] = $where;
    return $where;
  }
  public function build() {
    $this->buildMainEntity();
    $this->attachAuth();
    $this->buildJoins();
    if ($this->distinct) {
      $this->query->distinct();
    }

    return $this->query;
  }
  private function attachAuth() {
    $uids = GAuth::ids();
    $entID = GAuth::entId();
    if (!empty($uids) && !empty($entID)) {
      if (DB::table('gmf_sys_authority_roles as r')
        ->join('gmf_sys_authority_role_users as u', 'r.id', '=', 'u.role_id')
        ->where('r.code', 'gmf.role.sys.super')
        ->whereIn('u.user_id', GAuth::ids())->exists()) {
        return;
      }
      $items = DB::table('gmf_sys_authority_role_users as u')
        ->join('gmf_sys_authority_role_entities as e', 'u.role_id', '=', 'e.role_id')
        ->select('e.field_name', 'e.data_id')
        ->where('e.entity_id', $this->mainEntity->id)
        ->whereIn('u.user_id', $uids)
        ->where('u.ent_id', $entID)
        ->where('e.ent_id', $entID)
        ->whereNotNull('e.data_id')
        ->get();
      if ($items->count() > 0) {
        $items = $items->mapToGroups(function ($item, $key) {
          return [$item->field_name ?: 'id' => $item->data_id];
        })->toArray();
        foreach ($items as $f => $v) {
          $this->addWhere($f, 'in', $v);
        }
      }
    }

  }
  public function getQuery() {
    if (empty($this->query)) {
      $this->build();
    }
    return $this->query;
  }
  public function toSql() {
    if (empty($this->query)) {
      $this->build();
    }
    return $this->query->toSql();
  }
  public function getBindings() {
    if (empty($this->query)) {
      $this->build();
    }
    return $this->query->getBindings();
  }

  public function getSchema() {
    $schema = new Builder();
    $fields = [];
    foreach ($this->selects as $key => $value) {
      $fields[] = array_only($value->toArray(), ['name', 'alias', 'comment', 'type_id', 'type_type', 'path', 'hide']);
    }
    $schema->fields($fields);
    $schema->comment($this->mainEntity->comment);
    return $schema;
  }
  private function buildMainEntity() {
    $this->mainEntity = $this->getEntity($this->mainEntity->name);
    $this->query = DB::table($this->mainEntity->table_name . ' as ' . $this->mainEntity->alias);
  }
  private function buildWheres($items, $query, $boolean = 'and') {
    if (is_array($items)) {
      foreach ($items as $key => $value) {
        if (is_array($value)) {
          $this->buildWheres($value, $query, $boolean);
          continue;
        }
        if (empty($value->type)) {
          continue;
        }
        if ($value->type === 'item') {
          $this->buildWhereItem($value, $query, $boolean);
        } else if ($value->type === 'boolean') {
          $this->buildWhereBoolean($value, $query, $boolean);
        }
      }
    } else if (is_object($items)) {
      if (empty($items->type)) {
        return;
      }
      if ($items->type === 'item') {
        $this->buildWhereItem($items, $query, $boolean);
      } else if ($items->type === 'boolean') {
        $this->buildWhereBoolean($items, $query, $boolean);
      }
    }
  }
  private function buildWhereBoolean($item, $query, $boolean) {
    if (empty($item->items)) {
      return;
    }
    $query->where(function ($query) use ($item) {
      foreach ($item->items as $key => $value) {
        $this->buildWheres($value, $query, $item->boolean);
      }
    });
  }
  private function buildWhereItem($item, $query, $boolean) {
    if ($this->parseField($item)) {
      QueryCase::attachWhere($query, $item, $item->dbFieldName, $boolean);
    }

  }
  private function buildJoins() {
    foreach ($this->selects as $key => $value) {
      if ($this->parseField($value)) {
        $this->query->addSelect($value->dbFieldName . ' as ' . $value->alias);
      }
    }
    foreach ($this->orders as $key => $value) {
      if ($this->parseField($value)) {
        $this->query->orderBy($value->dbFieldName, $value->direction);
      }

    }
    $this->buildWheres($this->wheres, $this->query);

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
      $f->field_name = $f->field_name ?: $f->name;
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
      $join->first($joinField->tableAlias . '.' . $joinField->local_key);
      $join->second($b->alias . '.' . $joinField->foreign_key);
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
      if ($value->field_name === $name) {
        return $value;
      }
    }
    return null;
  }
  public function parseField($field) {
    if (is_string($field)) {
      $field = new Builder(['name' => $field]);
    }
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

    if (empty($mdField)) {
      return false;
    }

    $comments[] = $mdField->comment;

    $field->tableAlias($mdEntity->alias)->field_name($mdField->field_name);

    if (empty($field->alias)) {
      $field->alias(str_replace('.', '_', $field->name));
    }
    if (empty($field->comment)) {
      $field->comment(implode('.', $comments));
    }
    $field->path($field->name);
    $field->name($mdField->name);
    $field->type_id($mdField->type_id)->type_type($mdField->type_type);
    $field->dbFieldName($field->tableAlias . '.' . $field->field_name);
    $field->is_parsed(true);
    return $field;
  }
}
