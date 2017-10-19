<?php

namespace Gmf\Sys\Database;
use DB;
use Gmf\Sys\Models;
use Gmf\Sys\Uuid;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Fluent;
use Schema;

class MDInstaller {
	public static $defaultStringLength = 250;

	protected $columns = [];
	protected $commands = [];
	protected $mainEntity;
	public static $mdEntityName = 'gmf_sys_entities';

	public function __construct($mainEntity, $columns, $commands) {
		$this->mainEntity = $mainEntity;
		$this->columns = $columns;
		$this->commands = $commands;
	}
	public function install($onlyMetadata = false) {
		$exception = DB::transaction(function () use ($onlyMetadata) {
			$this->initContext();
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
		if (!empty($column->collection)) {
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
		if (empty($this->mainEntity->table_name) && !empty($this->mainEntity->tableName)) {
			$this->mainEntity->table_name($this->mainEntity->tableName);
		}
		$query = Models\Entity::Where('name', $this->mainEntity->name);
		if (!empty($this->mainEntity->id)) {
			$query->orWhere('id', $this->mainEntity->id);
		}
		$old = $query->first();
		if (empty($old)) {
			$this->createMD();
		} else {
			$this->updateMD($old);
		}
	}
	private function createMD() {
		$data = $this->mainEntity->toArray();
		$md = Models\Entity::create($data);
		$this->mainEntity->id = $md->id;

		$columns = $this->formatColumns();
		foreach ($columns as $column) {
			$data = $column->toArray();
			Models\EntityField::updateOrCreate(['entity_id' => $column->entity_id, 'name' => $column->name], $data);
		}
		if ($this->mainEntity->type === 'entity') {
			Schema::create($this->mainEntity->tableName, function (Blueprint $table) use ($columns) {
				foreach ($columns as $column) {
					$this->buildDBColumn($table, $column);
				}
				foreach ($this->commands as $command) {
					$this->buildDBCommand($table, $command);
				}
			});
		}
	}
	private function updateMD($old) {
		$this->mainEntity->id = $old->id;
		$data = $this->mainEntity->toArray();
		$data = array_only($data, ['name', 'comment', 'table_name', 'type']);
		Models\Entity::where('id', $this->mainEntity->id)->update($data);

		if ($this->mainEntity->type == 'entity') {
			if ($old->table_name != $this->mainEntity->table_name) {
				Schema::rename($old->table_name, $this->mainEntity->table_name);
			}
		}
		$this->updateMDField();
	}
	private function updateMDField() {
		$columns = $this->formatColumns();

		$mdType = $this->mainEntity->type;

		$oldFields = Models\EntityField::where('entity_id', $this->mainEntity->id)->get();
		//删除
		$colNames = [];
		$colIds = [];
		$flag = false;
		foreach ($oldFields as $oldColumn) {
			$flag = false;
			foreach ($columns as $column) {
				if ($oldColumn->name == $column->name) {
					$flag = true;
					break;
				}
			}
			if (!$flag) {
				$colNames[] = $oldColumn->field_name ?: $oldColumn->name;
				$colIds[] = $oldColumn->id;
			}
		}
		if ($mdType == 'entity' && $colNames && count($colNames)) {
			Schema::table($this->mainEntity->table_name, function (Blueprint $table) use ($colNames) {
				$table->dropColumn($colNames);
			});
		}
		if ($colIds && count($colIds)) {
			Models\EntityField::destroy($colIds);
		}

		//新增加
		$colNames = [];
		foreach ($columns as $column) {
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
		if ($mdType == 'entity' && $colNames && count($colNames)) {
			Schema::table($this->mainEntity->table_name, function (Blueprint $table) use ($colNames) {
				foreach ($colNames as $column) {
					$this->buildDBColumn($table, $column);
				}
			});
		}
		foreach ($colNames as $column) {
			$data = $column->toArray();
			Models\EntityField::updateOrCreate(['entity_id' => $column->entity_id, 'name' => $column->name], $data);
		}
		//修改
		$colNames = [];
		$dbChanges = [];
		$reNames = [];
		foreach ($columns as $column) {
			foreach ($oldFields as $oldColumn) {
				if ($oldColumn->name != $column->name) {
					continue;
				}
				if ($mdType == 'entity' && $oldColumn->field_name != $column->field_name) {
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

		if ($mdType == 'entity' && $reNames && count($reNames)) {
			Schema::table($this->mainEntity->table_name, function (Blueprint $table) use ($reNames) {
				foreach ($reNames as $old => $new) {
					$table->renameColumn($old, $new);
				}
			});
		}
		if ($mdType == 'entity' && $dbChanges && count($dbChanges)) {

			Schema::table($this->mainEntity->table_name, function (Blueprint $table) use ($dbChanges) {
				foreach ($dbChanges as $column) {
					$this->buildDBColumn($table, $column, ['change' => true]);
				}
			});
		}

		if ($colNames && count($colNames)) {
			foreach ($colNames as $column) {
				$data = $column->toArray();
				Models\EntityField::updateOrCreate(['entity_id' => $column->entity_id, 'name' => $column->name], $data);
			}
		}
	}
	private function formatColumns() {
		$s = 0;
		$mdType = $this->mainEntity->type;
		$columns = [];

		foreach ($this->columns as $column) {
			$item = new Fluent(array_only($column->toArray(), ['id', 'name', 'comment', 'field_name', 'type', 'length', 'collection', 'nullable', 'autoIncrement', 'unsigned', 'index', 'primary', 'unique']));
			$item->sequence = $s++;
			$item->entity_id = $this->mainEntity->id;
			if (empty($item->id)) {
				$item->id = Uuid::generate(1, 'gmf', Uuid::NS_DNS, "");
			}
			if (isset($column->default)) {
				$item->default_value($column->default);
			}
			if (isset($column->total)) {
				$item->precision($column->total);
			}
			if (isset($column->places)) {
				$item->scale($column->places);
			}
			if (empty($item->field_name) && !empty($column->fieldName)) {
				$item->field_name($column->fieldName);
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
			$ct = $this->getColumnType($column);
			$item->type_id($ct->id);
			$item->type_type($ct->type);
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
				$ct->id($fieldType->id)->type($fieldType->name);
				return $ct;
			}

		} else {
			$fieldType = Models\Entity::where('name', $column->type)->first();
			if (!empty($fieldType)) {
				$ct->id($fieldType->id)->type($fieldType->name);
				return $ct;
			}
		}
		if (!empty($column->refType)) {
			$ct->id($column->refType)->type($column->refType);
		} else {
			$ct->id($column->type)->type($column->type);
		}
		return $ct;
	}
	private function initContext() {
		if (!Schema::hasTable(static::$mdEntityName)) {
			Schema::create('gmf_sys_entities', function (Blueprint $table) {
				$table->string('id', 100)->primary();
				$table->string('name', 250)->index()->comment('名称');
				$table->string('comment')->nullable()->comment('描述');
				$table->string('table_name')->nullable()->comment('集合名称');
				$table->string('type')->comment('类型');
				$table->timestamps();
			});
			Schema::create('gmf_sys_entity_fields', function (Blueprint $table) {
				$table->string('id', 100)->primary();
				$table->string('entity_id', 100);
				$table->string('name', 250)->index()->comment('名称');
				$table->string('field_name', 100)->nullable()->comment('字段名称');
				$table->string('comment')->nullable()->comment('描述');
				$table->string('type_id', 100)->nullable()->comment('数据类型');
				$table->string('type_type', 200)->nullable()->comment('数据类型');
				$table->boolean('collection')->default('0')->comment('是否集合');
				$table->integer('sequence')->default('0')->comment('顺序');
				$table->string('default_value')->nullable()->comment('默认值');
				$table->boolean('nullable')->default('1')->comment('可空');
				$table->integer('length')->nullable()->comment('长度');
				$table->integer('scale')->nullable()->comment('基数');
				$table->integer('precision')->nullable()->comment('精度');
				$table->string('format')->nullable()->comment('格式化');
				$table->string('former')->nullable()->comment('构成者');
				$table->string('foreign_key')->nullable()->comment('外键');
				$table->string('local_key')->nullable()->comment('本方建');

				$table->timestamps();

				$table->foreign('entity_id')->references('id')->on('gmf_sys_entities')->onDelete('cascade');
			});
			$this->seedMD();
		}
	}
	private function seedMD() {
		$this->seedBaseEntityMD();
		$this->seedSysTypeEnumMD();
		$this->seedSysEntityMD();
	}
	private function seedBaseEntityMD() {
		$id = ['string', 'number', 'integer', 'bigInteger', 'float', 'boolean', 'date', 'object', 'entity', 'enum', 'timestamp', 'text', 'decimal'];
		Models\Entity::whereIn('id', $id)->delete();
		Models\Entity::create(['id' => '4ed0e57009b611e7b2bbbfa514fdeb8d', 'name' => 'string', 'comment' => '字符', 'type' => 'string']);
		Models\Entity::create(['id' => '4ed0e76009b611e7825fcd17c27385aa', 'name' => 'number', 'comment' => '数值', 'type' => 'number']);
		Models\Entity::create(['id' => '4ed0e7e009b611e7aa8a6ded049c186f', 'name' => 'integer', 'comment' => '整数', 'type' => 'integer']);
		Models\Entity::create(['id' => '4ed0e85009b611e7b0e4e94c422d56dd', 'name' => 'bigInteger', 'comment' => '长整数', 'type' => 'bigInteger']);
		Models\Entity::create(['id' => '4ed0e8a009b611e79be9a9e8d4997010', 'name' => 'float', 'comment' => '浮点数', 'type' => 'float']);
		Models\Entity::create(['id' => '4ed0e8f009b611e78ef127fdddb27dab', 'name' => 'boolean', 'comment' => '布尔', 'type' => 'boolean']);
		Models\Entity::create(['id' => '4ed0e94009b611e78a41c9fc87e23627', 'name' => 'date', 'comment' => '日期', 'type' => 'date']);
		Models\Entity::create(['id' => '37cfb5e0187611e79a6c7b8596e6a936', 'name' => 'timestamp', 'comment' => '时间戳', 'type' => 'timestamp']);
		Models\Entity::create(['id' => '4ed0e99009b611e7b216d7856f61a5f4', 'name' => 'object', 'comment' => '对象', 'type' => 'object']);
		Models\Entity::create(['id' => '4ed0e9e009b611e7b65f21229f6ab51d', 'name' => 'entity', 'comment' => '实体', 'type' => 'entity']);
		Models\Entity::create(['id' => '4ed0ea4009b611e796f7a196aabad97c', 'name' => 'enum', 'comment' => '枚举', 'type' => 'enum']);

		Models\Entity::create(['id' => '392bee00187911e7bb094174849e0866', 'name' => 'text', 'comment' => '文本', 'type' => 'text']);
		Models\Entity::create(['id' => '392bebb0187911e7b790eb28cb3253c6', 'name' => 'decimal', 'comment' => '十进制', 'type' => 'decimal']);

		Models\Entity::create(['id' => 'cb6672a018a611e7bff313c35726f18b', 'name' => 'dateTime', 'comment' => '日期时间', 'type' => 'dateTime']);
		Models\Entity::create(['id' => 'cb66753018a611e78a38372cae478b7e', 'name' => 'json', 'comment' => '对象', 'type' => 'json']);

	}
	private function seedSysTypeEnumMD() {
		//gmf.sys.type.enum
		$id = '024f542009b711e78d418395e17293c8';
		$s = 0;
		Models\Entity::where('id', $id)->delete();
		Models\Entity::create(['id' => $id, 'name' => 'gmf.sys.type.enum', 'comment' => '数据类型', 'type' => 'enum']);

		Models\EntityField::where('entity_id', $id)->delete();
		Models\EntityField::create(['entity_id' => $id, 'id' => 'ab7b629009b611e7a0fbcf9a026a7d6f', 'name' => 'string', 'comment' => '字符', 'type_type' => 'string', 'type_id' => '4ed0e57009b611e7b2bbbfa514fdeb8d', 'sequence' => $s++]);
		Models\EntityField::create(['entity_id' => $id, 'id' => 'ab7b649009b611e7a106cb503dee0d58', 'name' => 'number', 'comment' => '数值', 'type_type' => 'string', 'type_id' => '4ed0e57009b611e7b2bbbfa514fdeb8d', 'sequence' => $s++]);
		Models\EntityField::create(['entity_id' => $id, 'id' => 'ab7b651009b611e7be0d5385c4a2464f', 'name' => 'integer', 'comment' => '整数', 'type_type' => 'string', 'type_id' => '4ed0e57009b611e7b2bbbfa514fdeb8d', 'sequence' => $s++]);
		Models\EntityField::create(['entity_id' => $id, 'id' => 'ab7b658009b611e7927d57482a0bba41', 'name' => 'bigInteger', 'comment' => '长整数', 'type_type' => 'string', 'type_id' => '4ed0e57009b611e7b2bbbfa514fdeb8d', 'sequence' => $s++]);
		Models\EntityField::create(['entity_id' => $id, 'id' => 'ab7b65e009b611e7b22da958df3ae1d5', 'name' => 'boolean', 'comment' => '布尔', 'type_type' => 'string', 'type_id' => '4ed0e57009b611e7b2bbbfa514fdeb8d', 'sequence' => $s++]);
		Models\EntityField::create(['entity_id' => $id, 'id' => 'ab7b664009b611e7ba99193d372e4f9c', 'name' => 'date', 'comment' => '日期', 'type_type' => 'string', 'type_id' => '4ed0e57009b611e7b2bbbfa514fdeb8d', 'sequence' => $s++]);
		Models\EntityField::create(['entity_id' => $id, 'id' => 'ab7b669009b611e7a8e5b7b7dbbdfe31', 'name' => 'object', 'comment' => '对象', 'type_type' => 'string', 'type_id' => '4ed0e57009b611e7b2bbbfa514fdeb8d', 'sequence' => $s++]);
		Models\EntityField::create(['entity_id' => $id, 'id' => 'ab7b66f009b611e7a7185dbf4c634a59', 'name' => 'entity', 'comment' => '实体', 'type_type' => 'string', 'type_id' => '4ed0e57009b611e7b2bbbfa514fdeb8d', 'sequence' => $s++]);
		Models\EntityField::create(['entity_id' => $id, 'id' => 'ab7b674009b611e78338a9e39d1d7882', 'name' => 'enum', 'comment' => '枚举', 'type_type' => 'string', 'type_id' => '4ed0e57009b611e7b2bbbfa514fdeb8d', 'sequence' => $s++]);

	}
	private function seedSysEntityMD() {

		//gmf_sys_entity
		$id = 'f532f4a009b611e78197f91f9f35de3a';
		$s = 0;
		Models\Entity::where('id', $id)->delete();
		Models\Entity::create(['id' => $id, 'name' => 'gmf.sys.entity', 'comment' => '实体', 'table_name' => 'gmf_sys_entities', 'type' => 'entity']);

		Models\EntityField::where('entity_id', $id)->delete();
		Models\EntityField::create(['entity_id' => $id, 'id' => '2bb9a8f009b711e79da5a3c6c31086ee', 'name' => 'id', 'comment' => 'ID', 'type_type' => 'string', 'type_id' => '4ed0e57009b611e7b2bbbfa514fdeb8d', 'sequence' => $s++]);
		Models\EntityField::create(['entity_id' => $id, 'id' => '2bb9ab4009b711e78586895885fcac14', 'name' => 'name', 'comment' => '名称', 'type_type' => 'string', 'type_id' => '4ed0e57009b611e7b2bbbfa514fdeb8d', 'sequence' => $s++]);
		Models\EntityField::create(['entity_id' => $id, 'id' => '2bb9aba009b711e7b848e54ec581b1ca', 'name' => 'comment', 'comment' => '描述', 'type_type' => 'string', 'type_id' => '4ed0e57009b611e7b2bbbfa514fdeb8d', 'sequence' => $s++]);
		Models\EntityField::create(['entity_id' => $id, 'id' => '2bb9abf009b711e78a0799e6922803b9', 'name' => 'table_name', 'comment' => '集合名称', 'type_type' => 'string', 'type_id' => '4ed0e57009b611e7b2bbbfa514fdeb8d', 'sequence' => $s++]);
		Models\EntityField::create(['entity_id' => $id, 'id' => '2bb9ac4009b711e7bfb959b13d8e317f', 'name' => 'type', 'comment' => '类型', 'type_type' => 'gmf.sys.type.enum', 'type_id' => '024f542009b711e78d418395e17293c8', 'sequence' => $s++]);
		Models\EntityField::create(['entity_id' => $id, 'id' => '2bb9ac9009b711e79bc2179587e6a8b1', 'name' => 'fields', 'comment' => '集合', 'type_type' => 'gmf.sys.entity.field', 'type_id' => '6d28bb8009b711e78e9151efd7044098', 'collection' => '1', 'sequence' => $s++]);

		//gmf.sys.entity.field
		$id = '6d28bb8009b711e78e9151efd7044098';
		$s = 0;
		Models\Entity::where('id', $id)->delete();
		Models\Entity::create(['id' => $id, 'name' => 'gmf.sys.entity.field', 'comment' => '属性', 'table_name' => 'gmf_sys_entity_field', 'type' => 'entity']);

		Models\EntityField::where('entity_id', $id)->delete();
		Models\EntityField::create(['entity_id' => $id, 'id' => '75c0351009b811e7904909c360b924c2', 'name' => 'id', 'comment' => 'ID', 'type_type' => 'string', 'type_id' => '4ed0e57009b611e7b2bbbfa514fdeb8d', 'sequence' => $s++]);
		Models\EntityField::create(['entity_id' => $id, 'id' => '75c0374009b811e79e330f5163ac495b', 'name' => 'entity', 'comment' => '实体', 'type_type' => 'gmf.sys.entity', 'type_id' => 'f532f4a009b611e78197f91f9f35de3a', 'sequence' => $s++]);
		Models\EntityField::create(['entity_id' => $id, 'id' => '75c037f009b811e7a8d4690574908709', 'name' => 'name', 'comment' => '名称', 'type_type' => 'string', 'type_id' => '4ed0e57009b611e7b2bbbfa514fdeb8d', 'sequence' => $s++]);
		Models\EntityField::create(['entity_id' => $id, 'id' => '75c0386009b811e7b97ed9a929ee57aa', 'name' => 'comment', 'comment' => '描述', 'type_type' => 'string', 'type_id' => '4ed0e57009b611e7b2bbbfa514fdeb8d', 'sequence' => $s++]);
		Models\EntityField::create(['entity_id' => $id, 'id' => '75c038c009b811e78ad6ddb73667aa37', 'name' => 'collection', 'comment' => '是否集合', 'type_type' => 'boolean', 'type_id' => '4ed0e8f009b611e78ef127fdddb27dab', 'sequence' => $s++]);
		Models\EntityField::create(['entity_id' => $id, 'id' => '75c0392009b811e785243722cd1e3a2f', 'name' => 'sequence', 'comment' => '顺序', 'type_type' => 'integer', 'type_id' => '4ed0e7e009b611e7aa8a6ded049c186f', 'sequence' => $s++]);
		Models\EntityField::create(['entity_id' => $id, 'id' => '75c0398009b811e79c68ad16faa490a3', 'name' => 'type', 'comment' => '数据类型', 'type_type' => 'gmf.sys.entity', 'type_id' => 'f532f4a009b611e78197f91f9f35de3a', 'sequence' => $s++]);
		Models\EntityField::create(['entity_id' => $id, 'id' => '75c03a4009b811e783c531f16200cbde', 'name' => 'default_value', 'comment' => '默认值', 'type_type' => 'string', 'type_id' => '4ed0e57009b611e7b2bbbfa514fdeb8d', 'sequence' => $s++]);
		Models\EntityField::create(['entity_id' => $id, 'id' => 'bb892cd018b711e79dab6d698d9598c3', 'name' => 'field_name', 'comment' => '字段', 'type_type' => 'string', 'type_id' => '4ed0e57009b611e7b2bbbfa514fdeb8d', 'sequence' => $s++]);

		Models\EntityField::create(['entity_id' => $id, 'id' => '9ac28010b32a11e78dcf8107b0a39d1b', 'name' => 'nullable', 'comment' => '可空', 'type_type' => 'string', 'type_id' => '4ed0e57009b611e7b2bbbfa514fdeb8d', 'sequence' => $s++]);
		Models\EntityField::create(['entity_id' => $id, 'id' => '9ac28470b32a11e7acc2d95a75a33285', 'name' => 'length', 'comment' => '长度', 'type_type' => 'string', 'type_id' => '4ed0e57009b611e7b2bbbfa514fdeb8d', 'sequence' => $s++]);
		Models\EntityField::create(['entity_id' => $id, 'id' => '9ac285f0b32a11e7bad0bfd0e8cfc59f', 'name' => 'format', 'comment' => '格式', 'type_type' => 'string', 'type_id' => '4ed0e57009b611e7b2bbbfa514fdeb8d', 'sequence' => $s++]);
		Models\EntityField::create(['entity_id' => $id, 'id' => '9ac28680b32a11e7b9f557a3a3e7e264', 'name' => 'former', 'comment' => '构成', 'type_type' => 'string', 'type_id' => '4ed0e57009b611e7b2bbbfa514fdeb8d', 'sequence' => $s++]);

		Models\EntityField::create(['entity_id' => $id, 'id' => '4078eaa0b3f611e7a696dd21de850f19', 'name' => 'scale', 'comment' => '基数', 'type_type' => 'integer', 'type_id' => '4ed0e7e009b611e7aa8a6ded049c186f', 'sequence' => $s++]);
		Models\EntityField::create(['entity_id' => $id, 'id' => '4078ede0b3f611e7babaa784bf73f69d', 'name' => 'precision', 'comment' => '精度', 'type_type' => 'integer', 'type_id' => '4ed0e7e009b611e7aa8a6ded049c186f', 'sequence' => $s++]);

		Models\EntityField::create(['entity_id' => $id, 'id' => 'aaf46670b2f611e795e2bfc78373bbb0', 'name' => 'foreign_key', 'comment' => '外键', 'type_type' => 'string', 'type_id' => '4ed0e57009b611e7b2bbbfa514fdeb8d', 'sequence' => $s++]);
		Models\EntityField::create(['entity_id' => $id, 'id' => 'aaf46c20b2f611e7b5c7034c4d12f1a0', 'name' => 'local_key', 'comment' => '本方建', 'type_type' => 'string', 'type_id' => '4ed0e57009b611e7b2bbbfa514fdeb8d', 'sequence' => $s++]);

	}

}
