<?php

namespace Gmf\Sys\Console;

use App;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use InvalidArgumentException;
use ReflectionClass;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

abstract class GeneratorCommand extends Command {
	/**
	 * The filesystem instance.
	 *
	 * @var \Illuminate\Filesystem\Filesystem
	 */
	protected $files;

	/**
	 * The type of class being generated.
	 *
	 * @var string
	 */
	protected $type;
	protected $postCreate = [];
	/**
	 * Create a new controller creator command instance.
	 *
	 * @param  \Illuminate\Filesystem\Filesystem  $files
	 * @return void
	 */
	public function __construct(Filesystem $files) {
		parent::__construct();

		$this->files = $files;
	}

	/**
	 * Get the stub file for the generator.
	 *
	 * @return string
	 */
	abstract protected function getStub();

	public function handle() {
		$name = $this->getNameInput();
		$path = $this->path_combine($this->getRootPath($name), $this->getPath($name), $this->getInputPath());

		$fileName = $this->getFileName($name);
		$this->ensureDoesntAlreadyExist($path, $fileName);

		$this->makeDirectory($path);
		$path = $this->path_combine($path, $fileName);

		$this->files->put($path, $this->buildClass($name));

		$this->firePostCreateHooks();

		$this->line($this->type . " created successfully:\t" . $path);
	}
	protected function firePostCreateHooks() {
		foreach ($this->postCreate as $callback) {
			call_user_func($callback);
		}
	}
	protected function buildClass($name) {
		$stub = $this->files->get($this->getStub());
		return $this->handleStub($name, $stub);
	}
	protected function handleStub($name, $stub) {
		return $stub;
	}
	protected function makeDirectory($path) {
		if (!$this->files->isDirectory($path)) {
			$this->files->makeDirectory($path, 0777, true, true);
		}
		return $path;
	}
	protected function ensureDoesntAlreadyExist($path, $fileName) {
		$name = implode('.', array_slice(explode('.', $fileName), 0, -1));
		for ($i = 0; $i < strlen($name); $i++) {
			if (preg_match("/[A-Za-z]/", $name[$i])) {
				$name = substr($name, $i);
				break;
			}
		}

		$className = $this->getClassName($name);
		if ($this->files->glob($path . DIRECTORY_SEPARATOR . '*' . $name . '.*')) {
			throw new InvalidArgumentException("{$name} is already exists.");
		}
	}
	protected function getClassName($name) {
		return Str::studly($name);
	}
	protected function getNamespace($name) {
		$ss = implode('\\', Collection::make($this->splitPathName($this->getInputPath()))->map(function ($v) {
			return Str::studly($v);
		})->all());
		$ns = $this->getDefaultNamespace($this->getRootNamespace());
		if ($ss) {
			$ns .= '\\' . $ss;
		}
		return $ns;
	}
	protected function getDefaultNamespace($rootNamespace) {
		return $rootNamespace;
	}
	protected function getNameInput() {
		return Str::snake(trim($this->input->getArgument('name')));
	}
	protected function getFileName($name) {
		return date('Y_m_d_His') . '_' . $name . '.php';
	}
	protected function getRootPath($name) {
		if ($this->hasOption('package') && $this->option('package') && $path = $this->getPackagePath($name)) {
			return $path;
		}
		return $this->laravel->basePath();
	}
	protected function getInputPath() {
		if ($this->hasOption('path') && $this->option('path') && !is_null($targetPath = $this->input->getOption('path'))) {
			return $targetPath;
		}
		return '';
	}
	protected function getPath($name) {
		if (!is_null($targetPath = $this->input->getOption('path'))) {
			return $targetPath;
		}
		return '';
	}
	protected function getRootNamespace() {
		if ($this->hasOption('package') && $this->option('package') && !empty($ns = $this->getPackageNamespace())) {

		} else {
			$ns = $this->laravel->getNamespace();
		}
		if (ends_with($ns, '\\')) {
			$ns = trim(str_replace_last('\\', '', $ns));
		}
		return $ns;
	}
	protected function getArguments() {
		return [
			['name', InputArgument::REQUIRED, 'The name of the class'],
		];
	}
	protected function getOptions() {
		return [
			['package', null, InputOption::VALUE_OPTIONAL, 'The package where the  file should be created.'],
			['path', null, InputOption::VALUE_OPTIONAL, 'The location where the  file should be created.'],
		];
	}
	protected function splitPathName($name) {
		$name = preg_split("/[\/|\\\]/", $name);
		return $name;
	}
	protected function getNameNamespace($name) {
		$ns = $this->splitPathName($name);
		return trim(implode(DIRECTORY_SEPARATOR, array_slice($ns, 0, -1)), DIRECTORY_SEPARATOR);
	}
	protected function getNamePath($name) {
		$ns = $this->splitPathName($name);
		return implode(DIRECTORY_SEPARATOR, array_slice($ns, 0, -1));
	}
	protected function getNameName($name) {
		$ns = $this->splitPathName($name);
		return $ns[count($ns) - 1];
	}
	protected function path_combine() {
		return implode(DIRECTORY_SEPARATOR, Collection::make(func_get_args())
				->reject(function ($p) {
					return empty($p);
				})->all());
	}
	private function getPackage() {
		if ($this->hasOption('package') && $package = $this->option('package')) {
			$providers = Collection::make(config('app.providers'))
				->reject(function ($provider) {
					return Str::startsWith($provider, 'Illuminate\\');
				});
			foreach ($providers as $key => $value) {
				$provider = App::getProvider($value);
				if (strtolower($value) == strtolower($package) || (method_exists($provider, 'alias') && strtolower($provider->alias()) == strtolower($package))) {
					return $provider;
				}
			};
			throw new \Exception(sprintf('package [%s] provider is not found! ', $package));
		}
		return false;
	}

	protected function getPackageNamespace() {
		if ($package = $this->getPackage()) {
			$reflector = new ReflectionClass(get_class($package));
			return $reflector->getNamespaceName();
		}
		return false;
	}

	protected function getPackagePath() {
		if ($package = $this->getPackage()) {
			$reflector = new ReflectionClass(get_class($package));
			return dirname($reflector->getFileName()) . DIRECTORY_SEPARATOR . '..';
		}
		return false;
	}
}
