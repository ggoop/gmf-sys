<?php

namespace Gmf\Sys\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class InstallCommand extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'gmf:install
            {--force : Overwrite data}
            {--tag : by tag}
            {--seed : seed data}';

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
		//gmf:publish
		$opt = [];
		$opt['--tag'] = 'gmf';
		if ($this->option('force')) {
			$opt['--force'] = true;
		}
		$this->call('gmf:publish', $opt);

		//migrate
		$opt = [];
		$this->call('migrate', $opt);

		//gmf:sql presql
		$opt = ['--tag' => 'presql'];
		$this->call('gmf:sql', $opt);

		//gmf:seed
		$opt = [];
		if ($this->option('seed')) {
			$this->call('gmf:seed');
		}

		//gmf:sql sql
		$opt = ['--tag' => 'sql'];
		$this->call('gmf:sql', $opt);

		//gmf:sql postsql
		$opt = ['--tag' => 'postsql'];
		$this->call('gmf:sql', $opt);
	}
}
