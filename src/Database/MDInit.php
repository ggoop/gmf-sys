<?php

namespace Gmf\Sys\Database;
use Gmf\Sys\Builder;
use Gmf\Sys\Models;
use Illuminate\Database\Schema\Blueprint;
use Log;
use Schema;

class MDInit {
	protected $E;
	protected $ECols = [];
	protected $F;
	protected $FCols = [];
	public static $installed = false;
	public static function init() {
		if (!static::$installed) {
			$install = new MDInit;
			$install->install();
		}
		static::$installed = true;
	}
	public function __construct() {
	}
	public function install() {
		Log::error('MDInit::install');
		$this->initPro();

		$this->initMDTable();
	}
	private function formatColumns($entity, $fields) {
		$s = 0;
		foreach ($fields as $item) {
			$item->sequence = $s++;
			$item->entity_id = $entity->id;

			if ($entity->type == 'entity' && $item->type === 'entity' && empty($item->field_name)) {
				$item->field_name($item->name . '_id');
			}
			if ($entity->type == 'entity' && $item->type === 'enum' && empty($item->field_name)) {
				$item->field_name($item->name . '_enum');
			}
			if ($entity->type == 'entity' && empty($item->field_name) && (empty($item->collection) || !$item->collection)) {
				$item->field_name($item->name);
			}
			if ($entity->type == 'entity' && empty($item->field_name)) {
				$item->field_name($item->name);
			}
			if (empty($item->comment)) {
				$item->comment = $item->name;
			}
			if ($item->type === 'entity' || $item->type === 'enum') {
				$item->dbType('string');
				$item->length(100);
			}
			if (empty($item->dbType)) {
				$item->dbType($item->type);
			}
			if (empty($item->refType)) {
				$item->refType($item->type);
			}
			if ($item->dbType == 'string' && empty($item->length)) {
				$item->length(Metadata::$defaultStringLength);
			}
		}
	}
	private function buildDBColumn(Blueprint $table, $column, $options = []) {
		if (!empty($column->collection) || empty($column->field_name)) {
			return false;
		}
		$parameters = array_only($column->toArray(), ['length', 'default', 'autoIncrement', 'comment', 'nullable', 'primary', 'index', 'unique']);

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

		$type = $column->dbType;
		$name = $column->field_name;
		if (!empty($parameters['change'])) {
			if (in_array($type, ['timestamp', 'enum', 'mediumInteger', 'tinyInteger', 'json', 'softDeletes', 'uuid'])) {
				return;
			}
		}
		return $table->addColumn($type, $name, $parameters);
	}
	private function initPro() {
		$this->E = (new Builder)
			->id('f532f4a009b611e78197f91f9f35de3a')
			->name('gmf.sys.entity')
			->comment('实体')
			->table_name('gmf_sys_entities')
			->type('entity');
		$this->ECols[] = (new Builder)->type('string')->id('2bb9a8f009b711e79da5a3c6c31086ee')->name('id')->length('100')->comment('id')->primary();
		$this->ECols[] = (new Builder)->type('string')->id('2bb9ab4009b711e78586895885fcac14')->name('name')->comment('名称')->index();
		$this->ECols[] = (new Builder)->type('string')->id('2bb9aba009b711e7b848e54ec581b1ca')->name('comment')->comment('描述')->nullable();
		$this->ECols[] = (new Builder)->type('string')->id('2bb9abf009b711e78a0799e6922803b9')->name('table_name')->comment('描述')->nullable();
		$this->ECols[] = (new Builder)->type('string')->id('2bb9ac4009b711e7bfb959b13d8e317f')->name('type')->comment('类型')->nullable();
		$this->ECols[] = (new Builder)->type('entity')->refType('gmf.sys.entity.field')->id('2bb9ac9009b711e79bc2179587e6a8b1')->name('fields')->comment('属性')->collection(true)->local_key('id')->foreign_key('entity_id');

		$this->ECols[] = (new Builder)->type('timestamp')->id('7aed33c0bf7511e7901e55b84684f7ff')->name('created_at')->comment('创建时间')->nullable();
		$this->ECols[] = (new Builder)->type('timestamp')->id('7aed3960bf7511e7bf0b693c08e6da1f')->name('updated_at')->comment('修改时间')->nullable();

		$this->formatColumns($this->E, $this->ECols);

		$this->F = (new Builder)
			->id('6d28bb8009b711e78e9151efd7044098')
			->name('gmf.sys.entity.field')
			->comment('属性')
			->table_name('gmf_sys_entity_fields')
			->type('entity');

		$this->FCols[] = (new Builder)->type('string')->id('75c0351009b811e7904909c360b924c2')->name('id')->length('100')->comment('id')->primary();
		$this->FCols[] = (new Builder)->type('entity')->refType('gmf.sys.entity')->id('75c0374009b811e79e330f5163ac495b')->name('entity')->comment('实体')->local_key('entity_id')->foreign_key('id')->index();
		$this->FCols[] = (new Builder)->type('string')->id('75c037f009b811e7a8d4690574908709')->name('name')->comment('名称');
		$this->FCols[] = (new Builder)->type('string')->id('75c0386009b811e7b97ed9a929ee57aa')->name('comment')->comment('描述')->nullable();
		$this->FCols[] = (new Builder)->type('boolean')->id('75c038c009b811e78ad6ddb73667aa37')->name('collection')->comment('是否集合')->default('0')->nullable();
		$this->FCols[] = (new Builder)->type('integer')->id('75c0392009b811e785243722cd1e3a2f')->name('sequence')->comment('顺序')->default('0')->nullable();

		$this->FCols[] = (new Builder)->type('entity')->refType('gmf.sys.entity')->id('75c0398009b811e79c68ad16faa490a3')->name('type')->nullable()->local_key('type_id')->foreign_key('id')->comment('数据类型');
		$this->FCols[] = (new Builder)->type('string')->id('4b551570bf8b11e7b0c6bf43ba3a968a')->name('type_type')->nullable()->comment('数据类型');
		$this->FCols[] = (new Builder)->type('string')->id('15202ed0bf8d11e7b5f7f516d1b3135e')->name('type_enum')->nullable()->comment('数据类型');

		$this->FCols[] = (new Builder)->type('string')->id('75c03a4009b811e783c531f16200cbde')->name('default_value')->comment('默认值')->nullable();
		$this->FCols[] = (new Builder)->type('string')->id('bb892cd018b711e79dab6d698d9598c3')->name('field_name')->comment('字段')->nullable();
		$this->FCols[] = (new Builder)->type('boolean')->id('9ac28010b32a11e78dcf8107b0a39d1b')->name('nullable')->comment('可空')->default('1')->nullable();
		$this->FCols[] = (new Builder)->type('integer')->id('9ac28470b32a11e7acc2d95a75a33285')->name('length')->comment('长度')->nullable();
		$this->FCols[] = (new Builder)->type('string')->id('9ac285f0b32a11e7bad0bfd0e8cfc59f')->name('format')->comment('格式')->nullable();
		$this->FCols[] = (new Builder)->type('string')->id('9ac28680b32a11e7b9f557a3a3e7e264')->name('former')->comment('构成')->nullable();
		$this->FCols[] = (new Builder)->type('integer')->id('4078eaa0b3f611e7a696dd21de850f19')->name('scale')->comment('基数')->nullable();
		$this->FCols[] = (new Builder)->type('integer')->id('4078ede0b3f611e7babaa784bf73f69d')->name('precision')->comment('精度')->nullable();

		$this->FCols[] = (new Builder)->type('string')->id('aaf46670b2f611e795e2bfc78373bbb0')->name('foreign_key')->comment('外键')->nullable();
		$this->FCols[] = (new Builder)->type('string')->id('aaf46c20b2f611e7b5c7034c4d12f1a0')->name('local_key')->comment('本方建')->nullable();

		$this->FCols[] = (new Builder)->type('timestamp')->id('95a54900bf7511e7a4f511ce49653965')->name('created_at')->comment('创建时间')->nullable();
		$this->FCols[] = (new Builder)->type('timestamp')->id('95a54b60bf7511e7ba92e5c1dd1be43a')->name('updated_at')->comment('修改时间')->nullable();
		$this->formatColumns($this->F, $this->FCols);
	}

	private function initMDTable() {

		if (!Schema::hasTable($this->E->table_name)) {
			Schema::create($this->E->table_name, function (Blueprint $table) {
				foreach ($this->ECols as $column) {
					$this->buildDBColumn($table, $column);
				}
			});
		} else {
			Schema::table($this->E->table_name, function (Blueprint $table) {
				foreach ($this->ECols as $column) {
					if (!Schema::hasColumn($this->E->table_name, $column->field_name)) {
						$this->buildDBColumn($table, $column);
					}
				}
			});
		}

		$this->seedBaseEntityMD();

		if (!Schema::hasTable($this->F->table_name)) {
			Schema::create($this->F->table_name, function (Blueprint $table) {
				foreach ($this->FCols as $column) {
					$this->buildDBColumn($table, $column);
				}
			});
		} else {
			Schema::table($this->F->table_name, function (Blueprint $table) {
				foreach ($this->FCols as $column) {
					if (!Schema::hasColumn($this->F->table_name, $column->field_name)) {
						$this->buildDBColumn($table, $column);
					}
				}
			});
		}

		$this->seedSysTypeEnumMD();

		$this->seedSysEntityMD();
	}
	private function seedBaseEntityMD() {

		Models\Entity::build(function ($b) {$b->id('4ed0e76009b611e7825fcd17c27385aa')->name('number')->comment('数值')->type('number');});
		Models\Entity::build(function ($b) {$b->id('4ed0e7e009b611e7aa8a6ded049c186f')->name('integer')->comment('整数')->type('integer');});
		Models\Entity::build(function ($b) {$b->id('984f1400c9c111e78af3e1b5b480d007')->name('tinyInteger')->comment('整数')->type('tinyInteger');});
		Models\Entity::build(function ($b) {$b->id('984f18d0c9c111e7a873abffd2400e44')->name('smallInteger')->comment('整数')->type('smallInteger');});
		Models\Entity::build(function ($b) {$b->id('984f1b40c9c111e7aeab93ba1dd3c681')->name('mediumInteger')->comment('整数')->type('mediumInteger');});
		Models\Entity::build(function ($b) {$b->id('4ed0e85009b611e7b0e4e94c422d56dd')->name('bigInteger')->comment('长整数')->type('bigInteger');});
		Models\Entity::build(function ($b) {$b->id('4ed0e8a009b611e79be9a9e8d4997010')->name('float')->comment('浮点数')->type('float');});
		Models\Entity::build(function ($b) {$b->id('392bebb0187911e7b790eb28cb3253c6')->name('decimal')->comment('十进制')->type('decimal');});
		Models\Entity::build(function ($b) {$b->id('fb35ab20c9c011e78abd3dfdd8f66e00')->name('double')->comment('双精度数字')->type('double');});

		Models\Entity::build(function ($b) {$b->id('4ed0e8f009b611e78ef127fdddb27dab')->name('boolean')->comment('布尔')->type('boolean');});

		Models\Entity::build(function ($b) {$b->id('4ed0e94009b611e78a41c9fc87e23627')->name('date')->comment('日期')->type('date');});
		Models\Entity::build(function ($b) {$b->id('cd87e960c9c111e7b0d92564a66dee16')->name('time')->comment('日期')->type('time');});
		Models\Entity::build(function ($b) {$b->id('cb6672a018a611e7bff313c35726f18b')->name('dateTime')->comment('日期时间')->type('dateTime');});
		Models\Entity::build(function ($b) {$b->id('37cfb5e0187611e79a6c7b8596e6a936')->name('timestamp')->comment('时间戳')->type('timestamp');});
		Models\Entity::build(function ($b) {$b->id('e9bceac0c9c111e780803f1d97b6470a')->name('timestampTz')->comment('时间戳')->type('timestampTz');});

		Models\Entity::build(function ($b) {$b->id('4ed0e99009b611e7b216d7856f61a5f4')->name('object')->comment('对象')->type('object');});
		Models\Entity::build(function ($b) {$b->id('4ed0e9e009b611e7b65f21229f6ab51d')->name('entity')->comment('实体')->type('entity');});
		Models\Entity::build(function ($b) {$b->id('4ed0ea4009b611e796f7a196aabad97c')->name('enum')->comment('枚举')->type('enum');});
		Models\Entity::build(function ($b) {$b->id('cb66753018a611e78a38372cae478b7e')->name('json')->comment('json')->type('json');});
		Models\Entity::build(function ($b) {$b->id('11887bd0c9c111e7b3a54f2570c01bae')->name('jsonb')->comment('jsonb')->type('jsonb');});

		Models\Entity::build(function ($b) {$b->id('d8d01230c9c011e795c22b375e101413')->name('char')->comment('字符')->type('char');});
		Models\Entity::build(function ($b) {$b->id('4ed0e57009b611e7b2bbbfa514fdeb8d')->name('string')->comment('字符')->type('string');});
		Models\Entity::build(function ($b) {$b->id('392bee00187911e7bb094174849e0866')->name('text')->comment('文本')->type('text');});
		Models\Entity::build(function ($b) {$b->id('8462abd0c9c111e788a3793832b42f18')->name('mediumText')->comment('文本')->type('mediumText');});
		Models\Entity::build(function ($b) {$b->id('6236ac60c9c011e7be784dfa7b483491')->name('longText')->comment('长文本')->type('longText');});

	}
	private function seedSysTypeEnumMD() {
		//gmf.sys.type.enum
		$id = '024f542009b711e78d418395e17293c8';
		$s = 0;

		Models\Entity::build(function ($b) use ($id) {$b->id($id)->name('gmf.sys.type.enum')->comment('数据类型')->type('enum');});

		Models\EntityField::build(function ($b) use ($id, $s) {$b->entity_id($id)->id('ab7b629009b611e7a0fbcf9a026a7d6f')->name('string')->comment('字符')->type('string')->sequence($s++);});
		Models\EntityField::build(function ($b) use ($id, $s) {$b->entity_id($id)->id('ab7b649009b611e7a106cb503dee0d58')->name('number')->comment('数值')->type('string')->sequence($s++);});
		Models\EntityField::build(function ($b) use ($id, $s) {$b->entity_id($id)->id('ab7b651009b611e7be0d5385c4a2464f')->name('integer')->comment('整数')->type('string')->sequence($s++);});
		Models\EntityField::build(function ($b) use ($id, $s) {$b->entity_id($id)->id('ab7b658009b611e7927d57482a0bba41')->name('bigInteger')->comment('长整数')->type('string')->sequence($s++);});
		Models\EntityField::build(function ($b) use ($id, $s) {$b->entity_id($id)->id('ab7b65e009b611e7b22da958df3ae1d5')->name('boolean')->comment('布尔')->type('string')->sequence($s++);});
		Models\EntityField::build(function ($b) use ($id, $s) {$b->entity_id($id)->id('ab7b664009b611e7ba99193d372e4f9c')->name('date')->comment('日期')->type('string')->sequence($s++);});
		Models\EntityField::build(function ($b) use ($id, $s) {$b->entity_id($id)->id('ab7b669009b611e7a8e5b7b7dbbdfe31')->name('object')->comment('对象')->type('string')->sequence($s++);});
		Models\EntityField::build(function ($b) use ($id, $s) {$b->entity_id($id)->id('ab7b66f009b611e7a7185dbf4c634a59')->name('entity')->comment('实体')->type('string')->sequence($s++);});
		Models\EntityField::build(function ($b) use ($id, $s) {$b->entity_id($id)->id('ab7b674009b611e78338a9e39d1d7882')->name('enum')->comment('枚举')->type('string')->sequence($s++);});
	}
	private function seedSysEntityMD() {
		Models\Entity::build(function ($b) {
			$b->setAttributes($this->E->toArray());
		});
		foreach ($this->ECols as $column) {
			Models\EntityField::build(function ($b) use ($column) {
				$b->entity_id($this->E->id);
				$b->setAttributes(array_only(
					$column->toArray(), [
						'id', 'name', 'comment', 'length',
						'sequence', 'nullable', 'scale', 'precision',
						'foreign_key', 'local_key', 'field_name']));

				if (isset($column->default)) {
					$b->default_value($column->default);
				}
				$b->type_enum($column->type);
				if (isset($column->refType)) {
					$b->type($column->refType);
				}
			});

		}

		Models\Entity::build(function ($b) {
			$b->setAttributes($this->F->toArray());
		});
		foreach ($this->FCols as $column) {
			Models\EntityField::build(function ($b) use ($column) {
				$b->entity_id($this->F->id);
				$b->setAttributes(array_only(
					$column->toArray(), [
						'id', 'name', 'comment', 'length',
						'sequence', 'nullable', 'scale', 'precision',
						'foreign_key', 'local_key', 'field_name']));

				if (isset($column->default)) {
					$b->default_value($column->default);
				}
				$b->type_enum($column->type);
				if (isset($column->refType)) {
					$b->type($column->refType);
				}
			});
		}
	}
}
