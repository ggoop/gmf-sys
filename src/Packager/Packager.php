<?php

namespace Gmf\Sys\Packager;

class Packager {
	protected $databases = [];
	public function loadDatabasesFrom($path = '') {
		if ($path = $this->formatPath($path)) {
			$this->databases[] = $path;
		}
	}
	public function dbPaths() {
		return $this->databases;
	}
	private function formatPath($path = '') {
		if (!ends_with($path, '/') && !ends_with($path, '\\')) {
			$path .= DIRECTORY_SEPARATOR;
		}
		return str_replace('/', DIRECTORY_SEPARATOR, $path);
	}
}
