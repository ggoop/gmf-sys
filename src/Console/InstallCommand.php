<?php

namespace Gmf\Sys\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'gmf:install';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Run the commands necessary to prepare Passport for use';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle() {
		$this->call('gmf:client', ['--personal' => true, '--name' => config('app.name') . ' Personal Access Client']);
		$this->call('gmf:client', ['--password' => true, '--name' => config('app.name') . ' Password Grant Client']);
	}
}
