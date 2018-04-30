<?php

namespace Gmf\Sys\Console\Create;

use Gmf\Sys\Console\GeneratorCommand;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class PageCommand extends GeneratorCommand {
	protected $name = 'gmf:create-page';
	protected $description = 'Create a new vue page component';
	protected $type = 'page';

	public function __construct(Filesystem $files) {
		parent::__construct($files);
	}
	public function handle() {
		parent::handle();
	}
	/**
	 * Get migration path (either specified by '--path' option or default location).
	 *
	 * @return string
	 */
	protected function getPath($name) {
		return $this->path_combine('resources', 'assets', 'js', 'pages');
	}
	protected function getStub() {
		return __DIR__ . '/stubs/page.vue';
	}
	protected function handleStub($name, $stub) {
		$className = $this->getClassName($name);
		$fullName = implode('', explode('\\', $this->getRootNamespace())) . $className;
		$stub = str_replace('DummyName', $fullName, $stub);
		return $stub;
	}
	protected function getFileName($name) {
		return Str::studly($name) . '.vue';
	}
}
