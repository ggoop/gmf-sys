<?php

namespace Gmf\Sys\Bp;
use Excel;
use Gmf\Sys\Models\File;
use Illuminate\Http\Request;
use Storage;

class DataImport {
	public function create(Request $request) {
		$fileParamName = $request->input('name', 'files');
		$entity = $request->input('entity');
		if (empty($entity)) {
			throw new \Exception('实体不能为空!');
		}
		$datas = collect([]);
		if ($request->has($fileParamName) && is_array($request->input($fileParamName))) {
			$datas = collect($request->input($fileParamName));
		} else {
			$files = File::storage($request, $fileParamName, 'import', 'local');
			if ($files) {
				foreach ($files as $key => $file) {
					$disk = Storage::disk($file->disk);
					$path = $disk->path($file->path);
					Excel::load($path, function ($reader) use ($request, $datas) {
						$results = $reader->all();
						foreach ($results as $sheet) {
							$cols = array_where($sheet->getHeading(), function ($value) {
								return is_string($value) && $value;
							});
							$rowsData = [];
							foreach ($sheet as $key => $row) {
								if ($key > 0 && !empty($row->key)) {
									$datas->push($row->only($cols)->all());
								}
							}
						}
					});
				}
			}
		}
		if (count($datas)) {
			if (!class_exists($entity)) {
				throw new \Exception('找不到实体：' . $entity);
			}
			if (method_exists($entity, 'fromImport') && is_callable(array($entity, 'fromImport'))) {
				return $entity::fromImport($datas);
			} else {
				throw new \Exception('该实体不支持导入!');
			}
		} else {
			throw new \Exception('没有任何数据!');
		}
		return false;
	}
}
