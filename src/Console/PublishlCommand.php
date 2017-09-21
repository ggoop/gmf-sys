<?php

namespace Gmf\Sys\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class PublishlCommand extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'gmf:publish
            {--force : Overwrite data}
            {--tag : seed by tag}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'install gmf sys laravel for use, publish proc ';
	protected $files;
	public function __construct(Filesystem $files) {
		parent::__construct();
		$this->files = $files;
	}
	private function reNewRun() {
		$files = [];
		//seeds,sqls等文件
		$path = $this->laravel->databasePath() . DIRECTORY_SEPARATOR . 'preseeds';
		$files = array_unique(array_merge($files, $this->files->glob($path . '/*_*_*_*.php')));

		$path = $this->laravel->databasePath() . DIRECTORY_SEPARATOR . 'seeds';
		$files = array_unique(array_merge($files, $this->files->glob($path . '/*_*_*_*.php')));

		$path = $this->laravel->databasePath() . DIRECTORY_SEPARATOR . 'postseeds';
		$files = array_unique(array_merge($files, $this->files->glob($path . '/*_*_*_*.php')));

		$path = $this->laravel->databasePath() . DIRECTORY_SEPARATOR . 'presqls';
		$files = array_unique(array_merge($files, $this->files->glob($path . '/*_*_*_*.sql')));

		$path = $this->laravel->databasePath() . DIRECTORY_SEPARATOR . 'sqls';
		$files = array_unique(array_merge($files, $this->files->glob($path . '/*_*_*_*.sql')));

		$path = $this->laravel->databasePath() . DIRECTORY_SEPARATOR . 'postsqls';
		$files = array_unique(array_merge($files, $this->files->glob($path . '/*_*_*_*.sql')));

		foreach ($files as $file) {
			$this->files->delete($file);
		}
	}
	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle() {

		if ($this->option('force')) {
			$this->reNewRun();
		}

		$opt = [];
		$opt['--tag'] = 'gmf';
		if ($this->option('force')) {
			$opt['--force'] = true;
		}
		$this->call('vendor:publish', $opt);

	}
}
