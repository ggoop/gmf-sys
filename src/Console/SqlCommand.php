<?php

namespace Gmf\Sys\Console;

use DB;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SqlCommand extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'gmf:sql
            {--force : Overwrite data}
            {--name= : The name of sql files to be executed.}
            {--tag= : seed by tag}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'init sql';
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

		$path = $this->getCommandPath();

		$tag = $this->getTags();

		$path .= $tag;
		$this->info('******************' . $tag . ' sql executing*************');

		$files = $this->getMigrationFiles($path);
		$files = $this->filterFiles($files);
		$migrations = $this->pendingMigrations($files);

		foreach ($migrations as $file) {
			$this->runUp($file);
		}
		$this->info('******************' . $tag . ' sql execute complete******');

	}
	protected function getTags() {
		return $this->option('tag') . 'sqls';
	}
	protected function getCommandPath() {
		return $this->laravel->databasePath() . DIRECTORY_SEPARATOR;
	}
	protected function runUp($file) {
		$tag = $this->getTags();

		$name = $this->getMigrationName($file);
		$this->line($tag . "  execute begin:    {$name}");

		$content = $this->files->get($file);
		//DB::statement($content);
		DB::unprepared($content);
		$this->line($tag . " execute completed: {$name}");
	}
	protected function getResolveName($file) {
		$sp = explode('_', $file);
		if (count($sp) > 4 && strlen($sp[0]) == 4 && strlen($sp[1]) == 2 && strlen($sp[2]) == 2) {
			$class = implode('_', array_slice($sp, 4));
		} else {
			$class = implode('_', $sp);
		}
		return $class;
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
				$name = strtolower($this->getResolveName($this->getMigrationName(str_replace('.sql', '', $file))));
				return in_array($name, $names);
			})->values()->all();
	}
	public function getMigrationFiles($paths) {
		return Collection::make($paths)->flatMap(function ($path) {
			return $this->files->glob($path . '/*_*_*_*.sql');
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
	public function getMigrationName($path) {
		return basename($path);
	}
}
