<?php

namespace Gmf\Sys\Console\Install;

use Illuminate\Console\Command;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Packager;
use PDO;

class MdCommand extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'gmf:install-md
            {--force : Overwrite data}
            {--name= : The name of sql files to be executed.}
            {--path= : The path of migrations files to be executed.}
            {--tag= : seed by tag}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'init md';
	protected $files;
	protected $migrator;
	public function __construct(Filesystem $files) {
		parent::__construct();
		$this->migrator = app('migrator');
		$this->files = $files;
	}
	private function getPDOConnection($host, $port, $username, $password) {
		return new PDO(sprintf('mysql:host=%s;port=%d;', $host, $port), $username, $password);
	}
	private function initDatabase() {
		$database = env('DB_DATABASE', false);

		if (empty($database)) {
			$this->line('Skipping creation of database as env(DB_DATABASE) is empty');
			return;
		}
		try {
			$default = config('database.connections')[config('database.default')];
			$pdo = $this->getPDOConnection($default['host'], $default['port'], $default['username'], $default['password']);
			$pdo->exec(sprintf(
				'CREATE DATABASE IF NOT EXISTS %s CHARACTER SET %s COLLATE %s;',
				$default['database'],
				$default['charset'],
				$default['collation']
			));
			$this->line(sprintf('Successfully created %s database', $default['database']));
			if (!empty(config('database.connections')['log'])) {
				$default = config('database.connections')['log'];
				$pdo = $this->getPDOConnection($default['host'], $default['port'], $default['username'], $default['password']);
				$pdo->exec(sprintf(
					'CREATE DATABASE IF NOT EXISTS %s CHARACTER SET %s COLLATE %s;',
					$default['database'],
					$default['charset'],
					$default['collation']
				));
				$this->line(sprintf('Successfully created %s database', $default['database']));
			}

		} catch (PDOException $exception) {
			$this->error(sprintf('Failed to create %s database, %s', $database, $exception->getMessage()));
		}
	}
	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function handle() {

		$this->initDatabase();
		if (!$this->migrator->repositoryExists()) {
			$this->call('migrate:install');
		}
		$this->info("======\t Migrate begin");

		$paths = Packager::dbPaths();
		$paths = Collection::make($paths)->map(function ($path) {
			return $path . 'migrations';
		})->all();

		$files = $this->filterFiles($files = $this->getMigrationFiles($paths));
		$this->requireFiles($migrations = $this->pendingMigrations($files));

		foreach ($migrations as $file) {
			$this->runUp($file);
		}
		$this->info("======\t Migrate end");
	}
	protected function runUp($file) {
		$migration = $this->resolve($name = $this->getMigrationName($file));
		$this->line("Migrating:\t{$name}");
		if (method_exists($migration, 'up')) {
			$migration->up();
		}
		$this->line("Migrated:\t{$name}");
	}
	public function resolve($file) {
		$class = $this->getResolveName($file);
		return new $class;
	}
	protected function getResolveName($file) {
		$sp = explode('_', $file);
		if (count($sp) > 4 && strlen($sp[0]) == 4 && strlen($sp[1]) == 2 && strlen($sp[2]) == 2) {
			$class = Str::studly(implode('_', array_slice($sp, 4)));
		} else {
			$class = Str::studly(implode('_', $sp));
		}
		return $class;
	}
	public function getMigrationFiles($paths) {
		return Collection::make($paths)->flatMap(function ($path) {
			return $this->files->glob($path . '/*_*_*_*.php');
		})->filter()->sortBy(function ($file) {
			return $this->getMigrationName($file);
		})->values()->keyBy(function ($file) {
			return $this->getMigrationName($file);
		})->all();
	}
	protected function pendingMigrations($files, $ran = []) {
		return Collection::make($files)
			->reject(function ($file) use ($ran) {
				return in_array($this->getMigrationName($file), $ran);
			})->values()->all();
	}
	protected function filterFiles($files) {
		$names = strtolower($this->option('name') ?: '');
		if (!empty($names)) {
			$names = explode(',', $names);
		}
		if (empty($names) || count($names) == 0) {
			return $files;
		}
		return Collection::make($files)
			->filter(function ($file) use ($names) {
				$name = strtolower($this->getResolveName($this->getMigrationName($file)));
				return in_array($name, $names);
			})->values()->all();
	}
	public function getMigrationName($path) {
		return str_replace('.php', '', basename($path));
	}
	public function requireFiles(array $files) {
		foreach ($files as $file) {
			$this->files->requireOnce($file);
		}
	}
}
