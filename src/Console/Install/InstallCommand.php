<?php

namespace Gmf\Sys\Console\Install;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Packager;

class InstallCommand extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'gmf:install
            {--force : Overwrite data}
            {--tag : by tag}
            {--seed : seed data}
            {--sql : sql data}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'install gmf sys laravel for use, migrate,seed,install proc ';
	protected $files;
	public function __construct(Filesystem $files) {
		parent::__construct();
		$this->files = $files;
	}
	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle() {

		Packager::loadDatabasesFrom($this->laravel->databasePath());

		$opt = [];
		$opt['--tag'] = 'gmf';
		if ($this->option('force')) {
			$opt['--force'] = true;
		}
		$this->call('gmf:publish', $opt);

		//migrate
		$opt = [];
		$this->call('gmf:install-md', $opt);

		//$this->call('migrate', $opt);

		//gmf:sql --tag=pre
		$opt = ['--tag' => 'pre'];
		$this->call('gmf:install-sql', $opt);

		//gmf:seed --tag=pre
		$opt = ['--tag' => 'pre'];
		$this->call('gmf:install-seed', $opt);

		if ($this->option('sql')) {
			$this->call('gmf:install-sql', []);
		}
		if ($this->option('seed')) {
			$this->call('gmf:install-seed', []);
		}

		//gmf:sql --tag=post
		$opt = ['--tag' => 'post'];
		$this->call('gmf:install-sql', $opt);

		//gmf:seed --tag=post
		$opt = ['--tag' => 'post'];
		$this->call('gmf:install-seed', $opt);
	}
}
