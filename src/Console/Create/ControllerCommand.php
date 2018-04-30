<?php

namespace Gmf\Sys\Console\Create;

use Gmf\Sys\Console\GeneratorCommand;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Illuminate\Support\Str;

class ControllerCommand extends GeneratorCommand {
	protected $name = 'gmf:create-controller';
	protected $description = 'Create a new controller class';
	protected $type = 'controller';
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
			return $this->path_combine('src', 'Http', 'Controllers');
		} else {
			return $this->path_combine('app', 'Http', 'Controllers');
		}
	}
	protected function getStub() {
		return __DIR__ . '/stubs/controller.stub';
	}
	protected function handleStub($name, $stub) {
		$className = $this->getClassName($name);
		$ns = $this->getNamespace($name);
		$stub = str_replace('DummyClass', $className, $stub);
		$stub = str_replace('DummyNamespace', $ns, $stub);
		return $stub;
	}
	protected function getDefaultNamespace($rootNamespace) {
		return $rootNamespace . '\Http\Controllers';
	}
	protected function getFileName($name) {
		return Str::studly($name) . '.php';
	}
	protected function getNameInput() {
		return Str::snake(trim($this->input->getArgument('name') . 'Controller'));
	}
}
