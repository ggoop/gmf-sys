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
		$fullName = strtolower(implode('_', explode('\\', $this->getRootNamespace()))) . '_' . Str::snake($this->getClassName($name));
		$className = $this->getClassName($fullName);
		$stub = str_replace('DummyName', $className, $stub);
		return $stub;
	}
	protected function getFileName($name) {
		return $this->getClassName($name) . '.vue';
	}
}
