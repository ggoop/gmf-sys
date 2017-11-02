<?php

namespace Gmf\Sys\Database;
use DB;
use Gmf\Sys\Models;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Fluent;
use Schema;

class MDInstaller {

	protected $columns = [];
	protected $commands = [];
	protected $mainEntity;

	public function __construct($mainEntity, $columns, $commands) {
		$this->mainEntity = $mainEntity;
		$this->columns = $columns;
		$this->commands = $commands;
	}
	public function install($onlyMetadata = false) {
		$exception = DB::transaction(function () use ($onlyMetadata) {
			MDInit::init();
			$this->installMD();
		});
	}

	private function buildDBCommand(Blueprint $table, $command) {
		if (empty($command->name)) {
			return;
		}
		if (in_array($command->name, ['primary', 'unique', 'index', 'foreign'])) {
			$params = array_except($command->toArray(), ['name', 'columns']);
			$cmd = $table->{$command->name}($command->columns);
			foreach ($params as $key => $value) {
				if (!empty($value)) {
					$cmd->{$key} = $value;
				}
			}
		}
	}
	private function buildDBColumn(Blueprint $table, $column, $options = []) {
		if (!empty($column->collection) || empty($column->field_name)) {
			return;
		}
		$parameters = array_only($column->toArray(), ['length', 'autoIncrement', 'comment', 'nullable', 'primary', 'index', 'unique']);
		if (isset($column->default_value)) {
			$parameters['default'] = $column->default_value;
		}
		if (isset($column->precision)) {
			$parameters['total'] = $column->precision;
		}
		if (isset($column->scale)) {
			$parameters['places'] = $column->scale;
		}
		if ($options && count($options)) {
			$parameters = array_merge($parameters, $options);
		}
		if (!empty($parameters['change'])) {
			$parameters = array_only($parameters, ['change', 'autoIncrement', 'length', 'default', 'nullable', 'total', 'places']);
		}

		$type = $column->type;
		$name = $column->name;
		if ($type === 'entity' || $type === 'enum') {
			$parameters = array_merge(['length' => 100], $parameters);
			$type = 'string';
			$name = $column->field_name;
		}
		if (!empty($parameters['change'])) {
			if (in_array($type, ['timestamp', 'enum', 'mediumInteger', 'tinyInteger', 'json', 'softDeletes', 'uuid'])) {
				return;
			}
		}
		return $table->addColumn($type, $name, $parameters);
	}
	private function getShortName($name) {
		$parts = explode('.', $name);
		$len = count($parts);
		return $len > 0 ? $parts[$len - 1] : $name;
	}
	private function installMD() {
		$this->formatColumns();
		if (empty($this->mainEntity->table_name) && !empty($this->mainEntity->tableName)) {
			$this->mainEntity->table_name($this->mainEntity->tableName);
		}
		$query = Models\Entity::Where('name', $this->mainEntity->name);
		if (!empty($this->mainEntity->id)) {
			$query->orWhere('id', $this->mainEntity->id);
		}
		$oldMD = $query->first();

		if ($this->mainEntity->type === 'entity') {
			if ($oldMD && $oldMD->table_name != $this->mainEntity->table_name) {
				Schema::rename($oldMD->table_name, $this->mainEntity->table_name);
			}
			if (!Schema::hasTable($this->mainEntity->table_name)) {
				$this->createDBTable();
			} else {
				$this->updateDBTable($oldMD);
			}
		}
		$this->updateMD($oldMD);
	}
	private function createDBTable() {
		Schema::create($this->mainEntity->table_name, function (Blueprint $table) {
			foreach ($this->columns as $column) {
				$this->buildDBColumn($table, $column);
			}
			foreach ($this->commands as $command) {
				$this->buildDBCommand($table, $command);
			}
		});
	}
	private function updateDBTable($old) {
		$oldFields = $old->fields;
		$colNames = [];
		//删除
		$flag = false;
		foreach ($oldFields as $oldColumn) {
			$flag = false;
			foreach ($this->columns as $column) {
				if ($oldColumn->name == $column->name) {
					$flag = true;
					break;
				}
			}
			if (!$flag) {
				$colNames[] = $oldColumn->field_name ?: $oldColumn->name;
			}
		}
		if ($colNames && count($colNames)) {
			Schema::table($this->mainEntity->table_name, function (Blueprint $table) use ($colNames) {
				foreach ($colNames as $column) {
					if (Schema::hasColumn($this->mainEntity->table_name, $column)) {
						$table->dropColumn($column);
					}
				}
			});
		}
		//增加
		$colNames = [];
		foreach ($this->columns as $column) {
			$flag = false;
			foreach ($oldFields as $oldColumn) {
				if ($oldColumn->name == $column->name) {
					$flag = true;
					break;
				}
			}
			if (!$flag) {
				$colNames[] = $column;
			}
		}
		if ($colNames && count($colNames)) {
			Schema::table($this->mainEntity->table_name, function (Blueprint $table) use ($colNames) {
				foreach ($colNames as $column) {
					if (!Schema::hasColumn($this->mainEntity->table_name, $column->field_name)) {
						$this->buildDBColumn($table, $column);
					}
				}
			});
		}
		//修改
		$colNames = [];
		$dbChanges = [];
		$reNames = [];
		foreach ($this->columns as $column) {
			foreach ($oldFields as $oldColumn) {
				if ($oldColumn->name != $column->name) {
					continue;
				}
				if ($oldColumn->field_name != $column->field_name) {
					$reNames[$oldColumn->field_name] = $column->field_name;
					$colNames[] = $column;
					break;
				}
				if ($oldColumn->default_value != $column->default_value
					|| $oldColumn->nullable != $column->nullable
					|| $oldColumn->length != $column->length
					|| $oldColumn->scale != $column->scale
					|| $oldColumn->precision != $column->precision) {
					$dbChanges[] = $column;
					$colNames[] = $column;
					break;
				}
				if ($oldColumn->comment != $column->comment
					|| $oldColumn->foreign_key != $column->foreign_key
					|| $oldColumn->local_key != $column->local_key
					|| $oldColumn->former != $column->former
					|| $oldColumn->format != $column->format) {
					$colNames[] = $column;
					break;
				}
			}
		}

		if ($reNames && count($reNames)) {
			Schema::table($this->mainEntity->table_name, function (Blueprint $table) use ($reNames) {
				foreach ($reNames as $old => $new) {
					if (Schema::hasColumn($this->mainEntity->table_name, $old)) {
						$table->renameColumn($old, $new);
					}
				}
			});
		}
		if ($dbChanges && count($dbChanges)) {
			Schema::table($this->mainEntity->table_name, function (Blueprint $table) use ($dbChanges) {
				foreach ($dbChanges as $column) {
					if (Schema::hasColumn($this->mainEntity->table_name, $column->field_name)) {
						$this->buildDBColumn($table, $column, ['change' => true]);
					}
				}
			});
		}
	}
	private function updateMD($old) {

		Models\Entity::build(function ($b) {
			$b->setAttributes($this->mainEntity->toArray());
		});
		if ($old && !empty($old->fields) && count($old->fields)) {
			$oldFields = $old->fields;
			//删除的
			$delNames = [];
			$noChanges = [];
			$isExists = false;
			$isChange = false;
			foreach ($oldFields as $oldColumn) {
				$isExists = false;
				$isChange = true;
				foreach ($this->columns as $column) {
					if ($oldColumn->name == $column->name) {
						$isExists = true;

						if ($oldColumn->field_name == $column->field_name
							&& $oldColumn->default_value == $column->default_value
							&& $oldColumn->nullable == $column->nullable
							&& $oldColumn->length == $column->length
							&& $oldColumn->scale == $column->scale
							&& $oldColumn->precision == $column->precision
							&& $oldColumn->comment == $column->comment
							&& $oldColumn->foreign_key == $column->foreign_key
							&& $oldColumn->local_key == $column->local_key
							&& $oldColumn->former == $column->former
							&& $oldColumn->format == $column->format) {
							$isChange = false;
						}
						break;
					}
				}
				if (!$isExists) {
					$delNames[] = $oldColumn->id;
				}
				if ($isExists && !$isChange) {
					$noChanges[] = $oldColumn->name;
				}
			}
			if ($delNames && count($delNames)) {
				Models\EntityField::destroy($delNames);
			}
			foreach ($this->columns as $column) {
				if (!in_array($column->name, $noChanges)) {
					Models\EntityField::build(function ($b) use ($column) {
						$b->setAttributes($column->toArray());
					});
				}
			}
		} else {
			foreach ($this->columns as $column) {
				Models\EntityField::build(function ($b) use ($column) {
					$b->setAttributes($column->toArray());
				});
			}
		}
	}
	private function formatColumns() {
		$s = 0;
		$mdType = $this->mainEntity->type;
		$columns = [];

		foreach ($this->columns as $item) {
			$item->sequence = $s++;
			$item->entity_id = $this->mainEntity->id;

			if (isset($item->default)) {
				$item->default_value($item->default);
			}
			if (isset($item->total)) {
				$item->precision($item->total);
			}
			if (isset($item->places)) {
				$item->scale($item->places);
			}
			if (empty($item->field_name) && !empty($item->fieldName)) {
				$item->field_name($item->fieldName);
			}
			if ($mdType == 'entity' && $item->type === 'entity' && empty($item->field_name)) {
				$item->field_name($item->name . '_id');
			}
			if ($mdType == 'entity' && $item->type === 'enum' && empty($item->field_name)) {
				$item->field_name($item->name . '_enum');
			}
			if ($mdType == 'entity' && empty($item->field_name) && (empty($item->collection) || !$item->collection)) {
				$item->field_name($item->name);
			}
			if ($mdType == 'entity' && empty($item->field_name)) {
				$item->field_name($item->name);
			}
			if (empty($item->comment)) {
				$item->comment = $item->name;
			}
			$ct = $this->getColumnType($item);
			$item->type_id($ct->id);
			$item->type_type($ct->type);
			$item->type_enum($ct->enum);
			if (empty($item->local_key) && $item->type === 'entity') {
				if (!empty($item->collection) && $item->collection) {
					$item->local_key('id');
				} else {
					$item->local_key($item->name . '_id');
				}
			}
			if (empty($item->foreign_key) && $item->type === 'entity') {
				if (!empty($item->collection) && $item->collection) {
					$item->foreign_key = $this->getShortName($this->mainEntity->name) . '_id';
				} else {
					$item->foreign_key = 'id';
				}
			}
			$columns[] = $item;
		}
		return $columns;
	}
	private function getColumnType($column) {
		$ct = new Fluent;
		if (!empty($column->refType)) {
			$fieldType = Models\Entity::where('name', $column->refType)->first();
			if (!empty($fieldType)) {
				$ct->id($fieldType->id)->type($fieldType->name)->enum($fieldType->type);
				return $ct;
			}

		} else {
			$fieldType = Models\Entity::where('name', $column->type)->first();
			if (!empty($fieldType)) {
				$ct->id($fieldType->id)->type($fieldType->name)->enum($fieldType->type);
				return $ct;
			}
		}
		if (!empty($column->refType)) {
			$ct->id($column->refType)->type($column->refType)->enum($column->type);
		} else {
			$ct->id($column->type)->type($column->type)->enum($column->type);
		}
		return $ct;
	}
}
