<?php

namespace Gmf\Sys\Console\Create;

use Gmf\Sys\Console\GeneratorCommand;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Illuminate\Support\Str;

class PreSeedCommand extends GeneratorCommand {
	protected $name = 'gmf:create-preseed';
	protected $description = 'Create a new preseed class';
	protected $type = 'preseed';
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
		return $this->path_combine('database', 'preseeds');
	}
	protected function getStub() {
		return __DIR__ . '/stubs/preseed.stub';
	}
	protected function handleStub($name, $stub) {
		$className = $this->getClassName($name);
		$stub = str_replace('DummyClass', $className, $stub);
		return $stub;
	}
}
