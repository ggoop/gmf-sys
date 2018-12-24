<?php
namespace Gmf\Sys\Http\Controllers;

use GAuth;
use Gmf\Sys\Http\Resources;
use Gmf\Sys\Models\File;
use Illuminate\Http\Request;
use Validator;

class FileController extends Controller
{
  public function show(Request $request, string $id)
  {
    $file = File::select('id', 'title', 'type', 'ext', 'size', 'created_at')
      ->addSelect('disk', 'path', 'pdf_disk', 'pdf_path')
      ->find($id);
    return $this->toJson(new Resources\File($file));
  }
  public function store(Request $request)
  {
    GAuth::check('user');
    $fname = $request->input('name', 'files');
    $validator = Validator::make($request->all(), [
      $fname => 'mimes:jpeg,gif,bmp,png,docx,doc,txt,xlsx,ppt,pptx,pdf'
    ]);
    if ($validator->fails()) {
      throw new \Exception('嘿，是要进行攻击吗~');
    }
    $files = File::storage($request, $request->input('name', 'files'), $request->input('path', 'file'));
    if ($files) {
      return $this->toJson(Resources\File::collection($files));
    } else {
      return $this->toJson(false);
    }
  }
}
