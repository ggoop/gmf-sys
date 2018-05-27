<?php

namespace Gmf\Sys\Bp;
use Gmf\Sys\Models\File;
use Illuminate\Http\Request;
use Log;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
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
					Log::error(static::class . ':' . $fileParamName . ', load begin');

					$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
					$worksheetNames = $reader->listWorksheetNames($path);
					$worksheetNames = collect($worksheetNames)->reject(function ($v) {
						return starts_with(strtolower($v), 'sheet');
					})->all();
					$reader->setLoadSheetsOnly($worksheetNames);
					$spreadsheet = $reader->load($path)->getAllSheets();
					foreach ($spreadsheet as $sheet) {
						$cells = $sheet->getCellCollection();
						$maxColName = $sheet->getHighestDataColumn();
						$maxCInd = Coordinate::columnIndexFromString($maxColName);
						$maxRowName = $sheet->getHighestDataRow();
						$cols = [];
						for ($i = 1; $i <= $maxCInd; $i++) {
							$cName = Coordinate::stringFromColumnIndex($i);
							$col = $cells->get($cName . '1');
							if (empty($col) || empty($col->getCalculatedValue())) {
								break;
							}
							$cols[$cName] = $col->getCalculatedValue();
						}
						for ($i = 3; $i <= $maxRowName; $i++) {
							$rowsData = [];
							$empty = true;
							foreach ($cols as $ck => $cv) {
								$col = $cells->get($ck . $i);
								if (!empty($col)) {
									$rowsData[$cv] = $col->getCalculatedValue();
								}
								if (!empty($rowsData[$cv])) {
									$empty = false;
								}
							}
							if ($empty || empty($rowsData)) {
								break;
							}
							$datas->push($rowsData);
						}
					}
					unset($spreadsheet);
					Log::error(static::class . ':' . $fileParamName . ', load end');
				}
			}
		}
		if (count($datas)) {
			Log::error(static::class . ':' . $fileParamName . ',rows:' . count($datas));
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
