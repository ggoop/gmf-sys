<?php

namespace Gmf\Sys\Console\Create;

use Gmf\Sys\Console\GeneratorCommand;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Illuminate\Support\Str;

class PostSeedCommand extends GeneratorCommand {
	protected $name = 'gmf:create-postseed';
	protected $description = 'Create a new postseed class';
	protected $type = 'postseed';
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
		return $this->path_combine('database', 'postseeds');
	}
	protected function getStub() {
		return __DIR__ . '/stubs/postseed.stub';
	}
	protected function handleStub($name, $stub) {
		$fullName = strtolower(implode('_', explode('\\', $this->getRootNamespace()))) . '_' . Str::snake($this->getClassName($name));
		$className = $this->getClassName($fullName);
		$stub = str_replace('DummyClass', $className, $stub);
		return $stub;
	}
	protected function getFileName($name) {
		$fullName = strtolower(implode('_', explode('\\', $this->getRootNamespace()))) . '_' . Str::snake($this->getClassName($name));
		return date('Y_m_d_His') . '_' . $fullName . '.php';
	}
	protected function getNameInput() {
		return Str::snake(trim($this->input->getArgument('name') . 'PostSeeder'));
	}
}
