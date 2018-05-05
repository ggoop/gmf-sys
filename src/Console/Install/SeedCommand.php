<?php

namespace Gmf\Sys\Console\Install;

use Exception;
use Gmf\Sys\Libs\Common;
use Gmf\Sys\Models;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Packager;

class SeedCommand extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'gmf:install-seed
            {--force : Overwrite data}
            {--name= : The name of seed files to be executed.}
            {--tag= : seed by tag},
            {--ent= : seed ent data}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'seed data';
	protected $files;
	public function __construct(Filesystem $files) {
		parent::__construct();
		$this->files = $files;
	}
	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function handle() {
		$paths = Packager::dbPaths();
		$tag = $this->getTags();
		$paths = Collection::make($paths)->map(function ($path) use ($tag) {
			return $path . $tag;
		})->all();

		$this->info("======\t {$tag} begin");

		$files = $this->filterFiles($files = $this->getMigrationFiles($paths));

		$this->requireFiles($migrations = $this->pendingMigrations($files, ['DatabaseSeeder']));

		foreach ($migrations as $file) {
			$this->runUp($file);
		}
		$this->info("======\t {$tag} end");
	}
	protected function getTags() {
		return $this->option('tag') . 'seeds';
	}
	protected function runUp($file) {
		$tag = $this->getTags();
		$migration = $this->resolve($name = $this->getMigrationName($file));
		$this->line("{$tag} seeding :\t{$name}");

		$entId = $this->option('ent') ?: false;
		if ($entId) {
			if (empty(Models\Ent::find($entId))) {
				$this->line("entid is null, returned. \t{$name}");
				throw new Exception("the entid is null", 1);
				return;
			}
		}
		Model::unguarded(function () use ($migration, $entId) {
			if ($entId && !array_has(get_object_vars($migration), 'entId')) {
				$this->line("entId property is not exists, returned. \t{$name}");
				return;
			}
			if ($entId && array_has(get_object_vars($migration), 'entId')) {
				$migration->entId = $entId;
			}
			if (method_exists($migration, 'run')) {
				$migration->run();
			}
		});
		$this->line("{$tag} seeded:\t{$name}");
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
			return Common::listFiles($path, '*_*_*_*.php');
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
