<?php

namespace Gmf\Sys\Database;
use Gmf\Sys\Builder;
use Gmf\Sys\Models;
use Gmf\Sys\Uuid;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Fluent;

class Metadata {
	public static $defaultStringLength = 255;

	protected $columns = [];
	protected $commands = [];
	protected $mainEntity;
	public static $mdEntityName = 'gmf_sys_entities';

	public static function create($id, array $parameters = []) {
		return new Metadata($id, $parameters);
	}
	public static function dropIfExists($id) {
		$md = Models\Entity::find($id);
		if ($md) {

		}
		if ($md->type === 'entity') {
			Schema::dropIfExists($id);
		}
	}
	public static function defaultStringLength($length) {
		static::$defaultStringLength = $length;
	}
	public function __construct($id, array $parameters = []) {
		$this->mainEntity = new Fluent(
			array_merge(compact('id'), $parameters)
		);
	}
	//==================== entity begin //
	public function mdEntity(string $name) {
		return $this->md($name, 'entity')->tableName(str_replace('.', '_', $name));
	}
	public function mdEnum(string $name) {
		return $this->md($name, 'enum');
	}
	public function md($name, $type) {
		$this->mainEntity->name = $name;
		$this->mainEntity->type = $type;
		return $this->mainEntity;
	}
	//==================== entity end //
	//=================================== columns begin//
	public function entity($column, $refType) {
		return $this->addColumn('entity', $column, compact('refType'));
	}
	public function enum($column, $refType) {
		return $this->addColumn('enum', $column, compact('refType'));
	}
	public function increments($column) {
		return $this->unsignedInteger($column, true);
	}
	public function bigIncrements($column) {
		return $this->unsignedBigInteger($column, true);
	}
	public function string($column, $length = null) {
		$length = $length ?: static::$defaultStringLength;

		return $this->addColumn('string', $column, compact('length'));
	}
	public function text($column) {
		return $this->addColumn('text', $column);
	}
	public function longText($column) {
		return $this->addColumn('longText', $column);
	}
	public function unsignedInteger($column, $autoIncrement = false) {
		return $this->integer($column, $autoIncrement, true);
	}
	public function unsignedBigInteger($column, $autoIncrement = false) {
		return $this->bigInteger($column, $autoIncrement, true);
	}
	public function integer($column, $autoIncrement = false, $unsigned = false) {
		return $this->addColumn('integer', $column, compact('autoIncrement', 'unsigned'));
	}
	public function tinyInteger($column, $autoIncrement = false, $unsigned = false) {
		return $this->addColumn('tinyInteger', $column, compact('autoIncrement', 'unsigned'));
	}
	public function smallInteger($column, $autoIncrement = false, $unsigned = false) {
		return $this->addColumn('smallInteger', $column, compact('autoIncrement', 'unsigned'));
	}
	public function bigInteger($column, $autoIncrement = false, $unsigned = false) {
		return $this->addColumn('bigInteger', $column, compact('autoIncrement', 'unsigned'));
	}
	public function float($column, $total = 8, $places = 2) {
		return $this->addColumn('float', $column, compact('total', 'places'));
	}
	public function double($column, $total = null, $places = null) {
		return $this->addColumn('double', $column, compact('total', 'places'));
	}
	public function decimal($column, $total = 8, $places = 2) {
		return $this->addColumn('decimal', $column, compact('total', 'places'));
	}
	public function boolean($column) {
		return $this->addColumn('boolean', $column);
	}
	public function json($column) {
		return $this->addColumn('json', $column);
	}
	public function date($column) {
		return $this->addColumn('date', $column);
	}
	public function dateTime($column) {
		return $this->addColumn('dateTime', $column);
	}
	public function time($column) {
		return $this->addColumn('time', $column);
	}
	public function timestamp($column) {
		return $this->addColumn('timestamp', $column);
	}
	public function timestamps() {
		$this->timestamp('created_at')->nullable();

		$this->timestamp('updated_at')->nullable();
	}
	public function softDeletes() {
		return $this->timestamp('deleted_at')->nullable();
	}
	public function rememberToken() {
		return $this->string('remember_token', 100)->nullable();
	}
	public function addColumn($type, $name, array $parameters = []) {
		$this->columns[] = $column = new Fluent(
			array_merge(compact('type', 'name'), $parameters)
		);
		return $column;
	}
	//=================================== columns end//
	//=================================== commands begin//
	public function primary($columns, $index = null, $algorithm = null) {
		return $this->indexCommand('primary', $columns, $index, $algorithm);
	}
	public function unique($columns, $index = null, $algorithm = null) {
		return $this->indexCommand('unique', $columns, $index, $algorithm);
	}
	public function index($columns, $index = null, $algorithm = null) {
		return $this->indexCommand('index', $columns, $index, $algorithm);
	}
	public function foreign($columns, $index = null) {
		return $this->indexCommand('foreign', $columns, $index);
	}
	protected function indexCommand($name, $columns, $index, $algorithm = null) {
		return $this->addCommand($name, compact('index', 'columns', 'algorithm'));
	}
	public function dropColumn($columns) {
		return $this->addCommand('dropColumn', compact('columns'));
	}
	protected function addCommand($name, array $parameters = []) {
		$this->commands[] = $command = new Builder(array_merge(compact('name'), $parameters));
		return $command;
	}
	//=================================== commands end//

	public function build($onlyMetadata = false) {
		$exception = DB::transaction(function () use ($onlyMetadata) {
			$this->initContext();
			$this->buildMD();

			if (!$onlyMetadata && $this->mainEntity->type === 'entity') {
				Schema::create($this->mainEntity->tableName, function (Blueprint $table) {
					foreach ($this->columns as $column) {
						$this->buildDBColumn($table, $column);
					}
					foreach ($this->commands as $command) {
						$this->buildDBCommand($table, $command);
					}
				});
			}
		});
	}
	private function buildDBCommand(Blueprint $table, $command) {
		if (empty($command->name)) {
			return;
		}
		if (in_array($command->name, ['primary', 'unique', 'index', 'foreign', 'dropColumn'])) {
			$params = array_except($command->toArray(), ['name', 'columns']);
			$cmd = $table->{$command->name}($command->columns);
			foreach ($params as $key => $value) {
				if (!empty($value)) {
					$cmd->{$key} = $value;
				}
			}
		}
	}
	private function buildDBColumn(Blueprint $table, $column) {
		if (!empty($column->collection)) {
			return;
		}
		$parameters = array_except($column->toArray(), ['type', 'name']);
		$type = $column->type;
		$name = $column->name;

		if ($type === 'entity' || $type === 'enum') {
			$parameters = array_merge(['length' => 100], $parameters);
			$type = 'string';
			$name = $column->fieldName;
		}
		$table->addColumn($type, $name, $parameters);

	}
	private function getShortName($name) {
		$parts = explode('.', $name);
		$len = count($parts);
		return $len > 0 ? $parts[$len - 1] : $name;
	}
	private function buildMD() {
		if (empty($this->mainEntity->id)) {
			$this->mainEntity->id = Uuid::generate(1, 'gmf', Uuid::NS_DNS, "");
		}

		$id = $this->mainEntity->id;
		$mdType = $this->mainEntity->type;
		$s = 0;

		$data = $this->mainEntity->toArray();
		if (!empty($data['tableName'])) {
			$data['table_name'] = $data['tableName'];
		}
		Models\Entity::where('id', $id)->delete();
		Models\Entity::create($data);

		Models\EntityField::where('entity_id', $id)->delete();
		foreach ($this->columns as $column) {
			$column->entity_id = $id;
			$column->sequence = $s++;
			if (empty($column->id)) {
				$column->id = Uuid::generate(1, 'gmf', Uuid::NS_DNS, "");
			}
			if ($mdType == 'entity' && $column->type === 'entity' && empty($column->fieldName)) {
				$column->fieldName($column->name . '_id');
			}
			if ($mdType == 'entity' && $column->type === 'enum' && empty($column->fieldName)) {
				$column->fieldName($column->name . '_enum');
			}
			if ($mdType == 'entity' && empty($column->fieldName) && (empty($column->collection) || !$column->collection)) {
				$column->fieldName($column->name);
			}

			$ct = $this->getColumnType($column);
			$column->type_id = $ct->id;
			$column->type_type = $ct->type;
			if (empty($column->local_key) && $column->type === 'entity') {
				if (!empty($column->collection) && $column->collection) {
					$column->local_key = 'id';
				} else {
					$column->local_key = $column->name . '_id';
				}
			}
			if (empty($column->foreign_key) && $column->type === 'entity') {
				if (!empty($column->collection) && $column->collection) {
					$column->foreign_key = $this->getShortName($this->mainEntity->name) . '_id';
				} else {
					$column->foreign_key = 'id';
				}
			}
			if (empty($column->comment)) {
				$column->comment = $column->name;
			}

			$data = $column->toArray();
			if (!empty($data['fieldName'])) {
				$data['field_name'] = $data['fieldName'];
			}
			Models\EntityField::create($data);
		}
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
				$table->string('name')->unique()->comment('名称');
				$table->string('comment')->nullable()->comment('描述');
				$table->string('table_name')->nullable()->comment('集合名称');
				$table->string('type')->comment('类型');
				$table->timestamps();
			});

			Schema::create('gmf_sys_entity_fields', function (Blueprint $table) {
				$table->string('id', 100)->primary();
				$table->string('entity_id', 100);
				$table->string('name')->index()->comment('名称');
				$table->string('field_name', 100)->nullable()->comment('字段名称');
				$table->string('comment')->nullable()->comment('描述');
				$table->string('type_id', 100)->nullable()->comment('数据类型');
				$table->string('type_type', 200)->nullable()->comment('数据类型');
				$table->boolean('collection')->default('0')->comment('是否集合');
				$table->integer('sequence')->default('0')->comment('顺序');
				$table->string('default_value')->nullable()->comment('默认值');
				$table->string('foreign_key')->nullable()->comment('外键');
				$table->string('local_key')->nullable()->comment('本方建');

				$table->timestamps();

				$table->foreign('entity_id')->references('id')->on('gmf_sys_entities')->onDelete('cascade');
			});

			$this->seedEntity();
		}
	}
	private function seedEntity() {

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

		//gmf_sys_entity
		$id = 'f532f4a009b611e78197f91f9f35de3a';
		$s = 0;
		Models\Entity::where('id', $id)->delete();
		Models\Entity::create(['id' => $id, 'name' => 'gmf.sys.entity', 'comment' => '实体', 'table_name' => 'gmf_sys_entities', 'type' => 'entity']);

		Models\EntityField::where('entity_id', $id)->delete();
		Models\EntityField::create(['entity_id' => $id, 'id' => '2bb9a8f009b711e79da5a3c6c31086ee', 'name' => 'id', 'comment' => 'ID', 'type_type' => 'string', 'type_id' => '4ed0e57009b611e7b2bbbfa514fdeb8d', 'sequence' => $s++]);
		Models\EntityField::create(['entity_id' => $id, 'id' => '2bb9aac009b711e7b4fdcf693059f60a', 'name' => 'code', 'comment' => '编码', 'type_type' => 'string', 'type_id' => '4ed0e57009b611e7b2bbbfa514fdeb8d', 'sequence' => $s++]);
		Models\EntityField::create(['entity_id' => $id, 'id' => '2bb9ab4009b711e78586895885fcac14', 'name' => 'name', 'comment' => '名称', 'type_type' => 'string', 'type_id' => '4ed0e57009b611e7b2bbbfa514fdeb8d', 'sequence' => $s++]);
		Models\EntityField::create(['entity_id' => $id, 'id' => '2bb9aba009b711e7b848e54ec581b1ca', 'name' => 'memo', 'comment' => '备注', 'type_type' => 'string', 'type_id' => '4ed0e57009b611e7b2bbbfa514fdeb8d', 'sequence' => $s++]);
		Models\EntityField::create(['entity_id' => $id, 'id' => '2bb9abf009b711e78a0799e6922803b9', 'name' => 'table_name', 'comment' => '集合名称', 'type_type' => 'string', 'type_id' => '4ed0e57009b611e7b2bbbfa514fdeb8d', 'sequence' => $s++]);
		Models\EntityField::create(['entity_id' => $id, 'id' => '2bb9ac4009b711e7bfb959b13d8e317f', 'name' => 'type', 'comment' => '类型', 'type_type' => 'gmf.sys.type.enum', 'type_id' => '024f542009b711e78d418395e17293c8', 'sequence' => $s++]);
		Models\EntityField::create(['entity_id' => $id, 'id' => '2bb9ac9009b711e79bc2179587e6a8b1', 'name' => 'attrs', 'comment' => '类型', 'type_type' => 'gmf.sys.entity.field', 'type_id' => '6d28bb8009b711e78e9151efd7044098', 'collection' => '1', 'sequence' => $s++]);

		//gmf.sys.entity.field
		$id = '6d28bb8009b711e78e9151efd7044098';
		$s = 0;
		Models\Entity::where('id', $id)->delete();
		Models\Entity::create(['id' => $id, 'name' => 'gmf.sys.entity.field', 'comment' => '属性', 'table_name' => 'gmf_sys_entity_field', 'type' => 'entity']);

		Models\EntityField::where('entity_id', $id)->delete();
		Models\EntityField::create(['entity_id' => $id, 'id' => '75c0351009b811e7904909c360b924c2', 'name' => 'id', 'comment' => 'ID', 'type_type' => 'string', 'type_id' => '4ed0e57009b611e7b2bbbfa514fdeb8d', 'sequence' => $s++]);
		Models\EntityField::create(['entity_id' => $id, 'id' => '75c0374009b811e79e330f5163ac495b', 'name' => 'entity', 'comment' => '实体', 'type_type' => 'gmf.sys.entity', 'type_id' => 'f532f4a009b611e78197f91f9f35de3a', 'sequence' => $s++]);
		Models\EntityField::create(['entity_id' => $id, 'id' => '75c037f009b811e7a8d4690574908709', 'name' => 'name', 'comment' => '名称', 'type_type' => 'string', 'type_id' => '4ed0e57009b611e7b2bbbfa514fdeb8d', 'sequence' => $s++]);
		Models\EntityField::create(['entity_id' => $id, 'id' => '75c0386009b811e7b97ed9a929ee57aa', 'name' => 'memo', 'comment' => '备注', 'type_type' => 'string', 'type_id' => '4ed0e57009b611e7b2bbbfa514fdeb8d', 'sequence' => $s++]);
		Models\EntityField::create(['entity_id' => $id, 'id' => '75c038c009b811e78ad6ddb73667aa37', 'name' => 'collection', 'comment' => '是否集合', 'type_type' => 'boolean', 'type_id' => '4ed0e8f009b611e78ef127fdddb27dab', 'sequence' => $s++]);
		Models\EntityField::create(['entity_id' => $id, 'id' => '75c0392009b811e785243722cd1e3a2f', 'name' => 'sequence', 'comment' => '顺序', 'type_type' => '4ed0e7e009b611e7aa8a6ded049c186f', 'type_id' => '4ed0e7e009b611e7aa8a6ded049c186f', 'sequence' => $s++]);
		Models\EntityField::create(['entity_id' => $id, 'id' => '75c0398009b811e79c68ad16faa490a3', 'name' => 'type', 'comment' => '数据类型', 'type_type' => 'gmf.sys.entity', 'type_id' => 'f532f4a009b611e78197f91f9f35de3a', 'sequence' => $s++]);
		Models\EntityField::create(['entity_id' => $id, 'id' => '75c03a4009b811e783c531f16200cbde', 'name' => 'default_value', 'comment' => '默认值', 'type_type' => 'string', 'type_id' => '4ed0e57009b611e7b2bbbfa514fdeb8d', 'sequence' => $s++]);
		Models\EntityField::create(['entity_id' => $id, 'id' => 'bb892cd018b711e79dab6d698d9598c3', 'name' => 'field_name', 'comment' => '字段', 'type_type' => 'string', 'type_id' => '4ed0e57009b611e7b2bbbfa514fdeb8d', 'sequence' => $s++]);

	}

}
