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
            {--path= : The path of sql files to be executed.}
            {--tag : seed by tag}';

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

		$tag = $this->option('tag') ?: 'sql';

		$path .= $tag;
		$this->info('******************' . $tag . ' sql executing*************');

		$files = $this->getMigrationFiles($path);

		$migrations = $this->pendingMigrations($files);

		foreach ($migrations as $file) {
			$this->runUp($file);
		}
		$this->info('******************' . $tag . ' sql execute complete******');

	}
	protected function getCommandPath() {
		return $this->laravel->databasePath() . DIRECTORY_SEPARATOR;
	}
	protected function runUp($file) {
		$name = $this->getMigrationName($file);
		$this->line("sql begin:     {$name}");

		$content = $this->files->get($file);
		DB::statement($content);

		$this->line("sql completed: {$name}");
	}
	public function getMigrationFiles($paths) {
		return Collection::make($paths)->flatMap(function ($path) {
			return $this->files->glob($path . '/*.sql');
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
