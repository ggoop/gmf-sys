<?php

namespace Gmf\Sys\Console;

use Illuminate\Console\Command;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class MdCommand extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'gmf:md
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
	protected $migrator;
	public function __construct() {
		parent::__construct();
		$this->migrator = app('migrator');
	}
	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function handle() {
		if (!$this->migrator->repositoryExists()) {
			$this->call('migrate:install');
		}
		$files = $this->migrator->getMigrationFiles($this->migrator->paths());

		$this->migrator->requireFiles($files, []);
		$files = $this->pendingMigrations($files);

		$this->migrator->runPending($files);

		foreach ($this->migrator->getNotes() as $note) {
			$this->output->writeln($note);
		}
	}
	protected function pendingMigrations($files) {
		return Collection::make($files)
			->reject(function ($file) {
				$instance = $this->migrator->resolve($this->migrator->getMigrationName($file));
				return empty($instance->mdID) && empty($instance->isMd);
			})->values()->all();
	}
}
