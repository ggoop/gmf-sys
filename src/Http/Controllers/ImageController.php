<?php
namespace Gmf\Sys\Http\Controllers;

use Gmf\Sys\Models;
use Illuminate\Http\Request;
use Storage;

class ImageController extends Controller {
	public function show(Request $request, string $id) {
		$input = $request->all();
		$w = $request->input('w', 200);
		$h = $request->input('h', 200);
		$file = Models\File::find($id);
		$fileContent = Models\FileContent::where('file_id', $id)->first();
		if ($file && $fileContent) {
			$base64 = substr(strstr($fileContent->data, ','), 1);
			$img = base64_decode($base64);
			return response($img, 200, ['Content-Type' => $file->type]);
		} else if ($file) {
			return response(Storage::disk($file->disk)->get($file->path), 200, ['Content-Type' => $file->type]);
		}
		ob_clean();
		ob_start();
		$im = @imagecreate($w, $h);
		imagecolorallocate($im, 255, 255, 255);
		imagepng($im);
		imagedestroy($im);
		$content = ob_get_clean();
		return response($content, 200, ['Content-Type' => 'image/png']);
	}
}
