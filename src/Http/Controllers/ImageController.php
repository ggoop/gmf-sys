<?php
namespace Gmf\Sys\Http\Controllers;

use Gmf\Sys\Models;
use Illuminate\Http\Request;

class ImageController extends Controller {
	public function show(Request $request, string $id) {
		$input = $request->all();
		$w = $request->input('w', 200);
		$h = $request->input('h', 200);
		$file = Models\File::find($id);
		if ($file) {
			$base64 = substr(strstr($file->data, ','), 1);
			$img = base64_decode($base64);
			return response($img, 200, ['Content-Type' => $file->type]);
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
