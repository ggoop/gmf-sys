<?php

namespace Gmf\Sys\Database;
use Gmf\Sys\Builder;
use Illuminate\Support\Fluent;

class MDGuard {
	protected $entity;
	protected $fields;
	public function __construct($md) {
		if (is_array($md)) {
			$this->entity = $md['entity'];
			$this->fields = $md['fields'];
		} else if ($md instanceof Fluent) {
			$this->entity = $md->entity;
			$this->fields = $md->fields;
		}
	}
	public function setEntityId($id) {
		$this->entity->id($id);
	}
	public function getDBTable() {
		if ($this->type() !== 'entity') {
			return false;
		}
		$table = new Builder;
		$table->table_name($this->entity->tableName)->connection($this->entity->connection);
		return $table;
	}
	public function getDBFields() {
		$items = [];
		foreach ($this->fields as $key => $field) {
			if (!empty($field->collection) && $field->collection) {
				continue;
			}
			$items[] = $this->toDBField($field);
		}
		return $items;
	}
	private function toDBField($field) {
		$item = new Builder;
		$item->setAttributes(array_only($field->toArray(), [
			'type', 'name', 'comment', 'length', 'autoIncrement'
			, 'nullable', 'primary', 'index', 'unique', 'default', 'total', 'places',
		]));
		if ($field->type === 'entity' || $field->type === 'enum') {
			$item->type('string')->length(100);
		}
		if ($field->type === 'entity') {
			$item->name($field->name . '_id');
		} else if ($field->type === 'enum') {
			$item->name($field->name . '_enum');
		}
		return $item;
	}
	public function getDBField($name) {
		foreach ($this->fields as $key => $field) {
			if (!empty($field->collection) && $field->collection) {
				continue;
			}
			if ($name === $field->name) {
				return $this->toDBField($field);
			}
		}
		return false;
	}
	public function getMDEntity() {
		$item = new Builder;
		$item->setAttributes(array_only(
			$this->entity->toArray(), ['id', 'name', 'comment', 'type', 'connection']
		));
		$item->table_name($this->entity->tableName);
		return $item;
	}

	public function getMDFields() {
		$items = [];
		$s = 0;
		foreach ($this->fields as $key => $field) {
			$item = new Builder;
			$item->setAttributes(array_only($field->toArray(), [
				'id', 'name', 'comment', 'collection',
				'length', 'nullable', 'sequence', 'format', 'former',
				'local_key', 'foreign_key',
			]));
			$item->sequence($s++);
			$item->type_enum($field->type);
			if (!empty($field->refType)) {
				$item->type_type($field->refType);
			} else {
				$item->type_type($field->type);
			}

			if (!empty($this->entity->id)) {
				$item->entity_id($this->entity->id);
			}
			if ($item->type_enum === 'entity' || $item->type_enum === 'enum') {
				$item->length(100);
			}
			if ($item->type_enum === 'entity') {
				$item->field_name($field->name . '_id');
			} else if ($item->type_enum === 'enum') {
				$item->field_name($field->name . '_enum');
			} else {
				$item->field_name($field->name);
			}
			if (isset($field->default)) {
				$item->default_value($field->default);
			}
			if (!empty($field->autoIncrement)) {
				$item->default_value('autoIncrement');
			}
			if (isset($field->total)) {
				$item->precision($field->total);
			}
			if (isset($field->places)) {
				$item->scale($field->places);
			}
			if (empty($item->local_key) && $item->type_enum === 'entity') {
				if (!empty($item->collection) && $item->collection) {
					$item->local_key('id');
				} else {
					$item->local_key($item->name . '_id');
				}
			}
			if (empty($item->foreign_key) && $item->type_enum === 'entity') {
				if (!empty($item->collection) && $item->collection) {
					$item->foreign_key = $this->getShortName($this->getMDEntity()->name) . '_id';
				} else {
					$item->foreign_key = 'id';
				}
			}
			$items[] = $item;
		}
		return $items;
	}
	private function getShortName($name) {
		$parts = explode('.', $name);
		$len = count($parts);
		return $len > 0 ? $parts[$len - 1] : $name;
	}
	/**
	entity,enum
	 */
	public function type() {
		return $this->entity->type;
	}
}
