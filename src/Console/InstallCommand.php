<?php

namespace Gmf\Sys\Console;

use Illuminate\Console\Command;

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

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle() {
		$this->call('migrate');

		$opt = [];
		$opt['--tag'] = 'gmf';
		if ($this->option('force')) {
			$opt['--force'] = true;
		}
		$this->call('vendor:publish', $opt);

		$this->call('gmf:sql', ['--tag' => 'presql']);

		$opt = [];
		if ($this->option('seed')) {
			$this->call('gmf:seed');
		}

		$this->call('gmf:sql');

		$this->call('gmf:sql', ['--tag' => 'postsql']);

	}
}
