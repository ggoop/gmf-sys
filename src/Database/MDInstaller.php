<?php

namespace Gmf\Sys\Database;
use DB;
use Gmf\Sys\Models;
use Illuminate\Database\Schema\Blueprint;
use Schema;

class MDInstaller {

	protected $guard;
	public function __construct($mainEntity, $columns, $commands) {
		$this->guard = new MDGuard([
			'entity' => $mainEntity,
			'fields' => $columns,
			'commands' => $commands,
		]);
	}
	public function install($onlyMetadata = false) {
		$entity = $this->guard->getMDEntity();
		$exception = DB::connection($entity->connection)->transaction(function () use ($onlyMetadata) {
			MDInit::init();
			$this->installMD();
		});
	}

	private function buildDBColumn(Blueprint $table, $column, $options = []) {
		$fields = ['comment', 'length', 'autoIncrement', 'nullable', 'primary', 'index', 'unique', 'default', 'total', 'places'];
		$changeFields = ['change', 'autoIncrement', 'length', 'default', 'nullable', 'total', 'places'];
		$parameters = array_only($column->toArray(), $fields);

		if ($options && count($options)) {
			$parameters = array_merge($parameters, $options);
		}
		if (isset($options['change'])) {
			$parameters = array_only($parameters, $changeFields);
		}
		$type = $column->type;
		$name = $column->name;
		if (isset($parameters['change'])) {
			if (in_array($type, ['timestamp', 'enum', 'mediumInteger', 'tinyInteger', 'json', 'softDeletes', 'uuid'])) {
				return;
			}
		}
		return $table->addColumn($type, $name, $parameters);
	}
	private function installMD() {
		$entity = $this->guard->getMDEntity();
		$query = Models\Entity::Where('name', $entity->name);
		if (!empty($entity->id)) {
			$query->orWhere('id', $entity->id);
		}
		$oldMD = $query->first();

		if ($entity->type === 'entity') {
			if ($oldMD
				&& $oldMD->table_name != $entity->table_name
				&& Schema::connection($entity->connection)->hasTable($oldMD->table_name)) {
				Schema::connection($entity->connection)->rename($oldMD->table_name, $entity->table_name);
			}
			if (!Schema::connection($entity->connection)->hasTable($entity->table_name)) {
				$this->createDBTable();
			} else if ($oldMD) {
				$this->updateDBTable($oldMD);
			}
		}
		$this->updateMD($oldMD);
	}
	private function createDBTable() {
		$table = $this->guard->getDBTable();
		if (!$table) {
			return;
		}
		Schema::connection($table->connection)->create($table->table_name, function (Blueprint $table) {
			$fields = $this->guard->getDBFields();
			foreach ($fields as $column) {
				$this->buildDBColumn($table, $column);
			}
		});
	}
	private function updateDBTable($old) {
		$oldFields = $old->fields;
		$newTable = $this->guard->getDBTable();
		$newFields = $this->guard->getMDFields();
		if (!$newTable) {
			return;
		}

		$colNames = [];
		//删除
		$flag = false;
		foreach ($oldFields as $oldColumn) {
			$flag = false;
			foreach ($newFields as $column) {
				//调整为自动增长时，需要先删除
				if ($oldColumn->name == $column->name
					&& $column->default_value == 'autoIncrement'
					&& in_array($column->type_type, ['bigInteger', 'integer', 'mediumInteger', 'smallInteger'])
					&& $column->type_type != $oldColumn->type_type) {
					break;
				}
				if ($oldColumn->name == $column->name) {
					$flag = true;
					break;
				}
			}
			if (!$flag) {
				$colNames[] = $oldColumn->field_name;
			}
		}
		if ($colNames && count($colNames)) {
			Schema::connection($newTable->connection)->table($newTable->table_name, function (Blueprint $table) use ($newTable, $colNames) {
				foreach ($colNames as $column) {
					if (Schema::connection($newTable->connection)->hasColumn($newTable->table_name, $column)) {
						$table->dropColumn($column);
					}
				}
			});
		}
		$oldDbColumns = Schema::connection($newTable->connection)->getColumnListing($newTable->table_name);

		//增加
		$colNames = [];
		foreach ($newFields as $column) {
			$flag = false;
			foreach ($oldFields as $oldColumn) {
				if ($oldColumn->name == $column->name) {
					$flag = true;
					break;
				}
			}
			//模型没有，或者数据库里没有，就自动增加
			if (!$flag || ($oldDbColumns && !in_array($column->field_name, $oldDbColumns))) {
				$colNames[] = $column;
			}
		}
		if ($colNames && count($colNames)) {
			Schema::connection($newTable->connection)->table($newTable->table_name, function (Blueprint $table) use ($newTable, $colNames) {
				foreach ($colNames as $column) {
					if (!Schema::connection($newTable->connection)->hasColumn($newTable->table_name, $column->field_name)) {
						$this->buildDBColumn($table, $this->guard->getDBField($column->name));
					}
				}
			});
		}
		//修改,表名，
		$reNames = [];
		foreach ($newFields as $column) {
			foreach ($oldFields as $oldColumn) {
				if ($oldColumn->name != $column->name) {
					continue;
				}
				if ($oldColumn->field_name != $column->field_name) {
					$reNames[$oldColumn->field_name] = $column->field_name;
					break;
				}
			}
		}
		if ($reNames && count($reNames)) {
			Schema::connection($newTable->connection)->table($newTable->table_name, function (Blueprint $table) use ($newTable, $reNames) {
				foreach ($reNames as $old => $new) {
					if (Schema::connection($newTable->connection)->hasColumn($newTable->table_name, $old)) {
						$table->renameColumn($old, $new);
					}
				}
			});
		}
		//修改类型
		$colNames = [];
		$dbChanges = [];

		foreach ($newFields as $column) {
			foreach ($oldFields as $oldColumn) {
				if ($oldColumn->name != $column->name) {
					continue;
				}
				if ($oldColumn->default_value != $column->default_value
					|| $oldColumn->nullable != $column->nullable
					|| $oldColumn->length != $column->length
					|| $oldColumn->type_type != $column->type_type
					|| $oldColumn->scale != $column->scale
					|| $oldColumn->precision != $column->precision) {
					$dbChanges[] = $column;
					$colNames[] = $column;
					break;
				}
			}
		}
		if ($dbChanges && count($dbChanges)) {
			Schema::connection($newTable->connection)->table($newTable->table_name, function (Blueprint $table) use ($newTable, $dbChanges) {
				foreach ($dbChanges as $column) {
					if (Schema::connection($newTable->connection)->hasColumn($newTable->table_name, $column->field_name)) {
						$this->buildDBColumn($table, $this->guard->getDBField($column->name), ['change' => true]);
					} else {
						$this->buildDBColumn($table, $this->guard->getDBField($column->name));
					}
				}
			});
		}
	}
	private function updateMD($old) {
		$entity = $this->guard->getMDEntity();
		$entity = Models\Entity::build(function ($b) use ($entity) {
			$b->setAttributes($entity->toArray());
		});
		$this->guard->setEntityId($entity->id);

		$newFields = $this->guard->getMDFields();

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
				foreach ($newFields as $column) {
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
							&& $oldColumn->type_type == $column->type_type
							&& $oldColumn->type_enum == $column->type_enum) {
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
			foreach ($newFields as $column) {
				if (!in_array($column->name, $noChanges)) {
					Models\EntityField::build(function ($b) use ($column) {
						$b->setAttributes($column->toArray());
					});
				}
			}
		} else {
			foreach ($newFields as $column) {
				Models\EntityField::build(function ($b) use ($column) {
					$b->setAttributes($column->toArray());
				});
			}
		}
	}
}
