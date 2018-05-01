<?php

namespace Gmf\Sys\Console\Create;

use Gmf\Sys\Console\GeneratorCommand;
use Gmf\Sys\Uuid;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class MdCommand extends GeneratorCommand {
	protected $name = 'gmf:create-md';
	protected $description = 'Create a new md class';
	protected $type = 'md';
	protected $composer;

	public function __construct(Filesystem $files, Composer $composer) {
		parent::__construct($files);
		$this->composer = $composer;
	}
	public function handle() {
		if (parent::handle() === false) {
			return;
		}

		if ($this->option('all')) {
			$this->input->setOption('controller', true);
			$this->input->setOption('model', true);
		}
		if ($this->option('model')) {
			$this->createModel();
		}
		if ($this->option('controller')) {
			$this->createController();
		}
		//$this->composer->dumpAutoloads();
	}
	protected function createModel() {
		$this->call('gmf:create-model', [
			'name' => $this->argument('name'),
			'--package' => $this->option('package'),
			'--path' => $this->option('path'),
		]);
	}
	protected function createController() {
		$this->call('gmf:create-controller', [
			'name' => $this->argument('name'),
			'--package' => $this->option('package'),
			'--path' => $this->option('path'),
		]);
	}
	/**
	 * Get migration path (either specified by '--path' option or default location).
	 *
	 * @return string
	 */
	protected function getPath($name) {
		return $this->path_combine('database', 'migrations');
	}
	protected function getStub() {
		return __DIR__ . '/stubs/md.stub';
	}
	protected function handleStub($name, $stub) {
		$fullName = strtolower(implode('_', explode('\\', $this->getRootNamespace()))) . '_' . Str::snake($this->getClassName($name));
		$className = $this->getClassName($fullName);
		$stub = str_replace('DummyClass', $className, $stub);
		$stub = str_replace('DummyName', str_replace('_', '.', Str::snake($fullName)), $stub);
		$stub = str_replace('DummyTable', Str::snake($fullName), $stub);
		$stub = str_replace('DummyId', Uuid::generate(1, 'gmf', Uuid::NS_DNS, ""), $stub);

		return $stub;
	}
	protected function getFileName($name) {
		$fullName = strtolower(implode('_', explode('\\', $this->getRootNamespace()))) . '_' . Str::snake($this->getClassName($name));
		return date('Y_m_d_His') . '_' . $fullName . '.php';
	}
	protected function getOptions() {
		return [
			['package', null, InputOption::VALUE_OPTIONAL, 'The package where the file should be created.'],
			['path', null, InputOption::VALUE_OPTIONAL, 'The location where the file should be created.'],
			['controller', null, InputOption::VALUE_NONE, 'The controller file should be created.'],
			['model', null, InputOption::VALUE_NONE, 'The model file should be created.'],
			['all', null, InputOption::VALUE_NONE, 'The controller,model file should be created.'],
		];
	}
}
