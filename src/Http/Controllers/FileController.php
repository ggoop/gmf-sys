<?php
namespace Gmf\Sys\Http\Controllers;

use GAuth;
use Gmf\Sys\Http\Resources;
use Gmf\Sys\Models\File;
use Illuminate\Http\Request;

class FileController extends Controller {
	public function show(Request $request, string $id) {
		$file = File::select('id', 'title', 'type', 'ext', 'size', 'created_at')
			->addSelect('disk', 'path', 'pdf_disk', 'pdf_path')
			->find($id);
		return $this->toJson(new Resources\File($file));
	}
	public function store(Request $request) {
		GAuth::check('user');
		$files = File::storage($request, $request->input('name', 'files'), 'file');
		if ($files) {
			return $this->toJson(Resources\File::collection($files));
		} else {
			return $this->toJson(false);
		}
	}
}
