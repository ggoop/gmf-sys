<?php

namespace Gmf\Sys\Database;
use Gmf\Sys\Builder;
use Gmf\Sys\Models;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Fluent;

class Metadata {
	public static $defaultStringLength = 250;

	protected $columns = [];
	protected $commands = [];
	protected $mainEntity;

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
		$installer = new MDInstaller($this->mainEntity, $this->columns, $this->commands);
		$installer->install($onlyMetadata);
	}

}
