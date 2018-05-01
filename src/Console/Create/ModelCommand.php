<?php

namespace Gmf\Sys\Console\Create;

use Gmf\Sys\Console\GeneratorCommand;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Illuminate\Support\Str;

class ModelCommand extends GeneratorCommand {
	protected $name = 'gmf:create-model';
	protected $description = 'Create a new model class';
	protected $type = 'model';
	protected $composer;

	public function __construct(Filesystem $files, Composer $composer) {
		parent::__construct($files);
		$this->composer = $composer;
	}
	public function handle() {
		parent::handle();

		//$this->composer->dumpAutoloads();
	}
	/**
	 * Get migration path (either specified by '--path' option or default location).
	 *
	 * @return string
	 */
	protected function getPath($name) {
		if ($this->hasOption('package') && $this->option('package')) {
			return $this->path_combine('src', 'Models');
		} else {
			return $this->path_combine('app', 'Models');
		}
	}
	protected function getStub() {
		return __DIR__ . '/stubs/model.stub';
	}
	protected function handleStub($name, $stub) {
		$fullName = strtolower(implode('_', explode('\\', $this->getRootNamespace()))) . '_' . Str::snake($this->getClassName($name));

		$className = $this->getClassName($name);

		$ns = $this->getNamespace($name);
		$stub = str_replace('DummyTable', Str::snake($fullName), $stub);
		$stub = str_replace('DummyClass', $className, $stub);
		$stub = str_replace('DummyNamespace', $ns, $stub);
		return $stub;
	}
	protected function getDefaultNamespace($rootNamespace) {
		return $rootNamespace . '\Models';
	}
	protected function getFileName($name) {
		return $this->getClassName($name) . '.php';
	}
	protected function getNameInput() {
		return Str::snake(trim($this->input->getArgument('name')));
	}
}
