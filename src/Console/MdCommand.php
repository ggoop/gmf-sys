<?php

namespace Gmf\Sys\Console;

use Illuminate\Console\Command;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use PDO;

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
	private function getPDOConnection($host, $port, $username, $password) {
		return new PDO(sprintf('mysql:host=%s;port=%d;', $host, $port), $username, $password);
	}
	private function initDatabase() {
		$database = env('DB_DATABASE', false);

		if (!$database) {
			$this->info('Skipping creation of database as env(DB_DATABASE) is empty');
			return;
		}
		try {

			$pdo = $this->getPDOConnection(env('DB_HOST'), env('DB_PORT'), env('DB_USERNAME'), env('DB_PASSWORD'));
			$pdo->exec(sprintf(
				'CREATE DATABASE IF NOT EXISTS %s CHARACTER SET %s COLLATE %s;',
				$database,
				env('DB_CHARSET', 'utf8'),
				env('DB_COLLATION', 'utf8_general_ci')
			));
			$this->info(sprintf('Successfully created %s database', $database));
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

		$files = $this->migrator->getMigrationFiles($this->migrator->paths());
		if ($this->option('name')) {
			$files = $this->filterFiles($files, $this->option('name'));
		}
		$this->migrator->requireFiles($files, []);
		$files = $this->pendingMigrations($files);

		$this->migrator->runPending($files);

		foreach ($this->migrator->getNotes() as $note) {
			$this->output->writeln($note);
		}
	}
	public function filterFiles($files, $name) {
		return Collection::make($files)
			->filter(function ($file) use ($name) {
				return str_contains($this->migrator->getMigrationName($file), $name);
			})->values()->all();
	}
	protected function pendingMigrations($files) {
		return Collection::make($files)
			->reject(function ($file) {
				$instance = $this->migrator->resolve($this->migrator->getMigrationName($file));
				return empty($instance->mdID) && empty($instance->isMd);
			})->values()->all();
	}
}
