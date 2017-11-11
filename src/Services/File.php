<?php

namespace Gmf\Sys\Services;
use Auth;
use Gmf\Sys\Builder;
use Gmf\Sys\Models;
use Gmf\Sys\Uuid;
use Illuminate\Http\Request;
use Storage;

class File {
	private $request;
	public function __construct(Request $request) {
		$this->request = $request;
	}
	public function storage($mdFiles, $path = '', $disk = 'public') {
		$datas = [];
		if (is_array($mdFiles) && empty($mdFiles['name'])) {
			foreach ($mdFiles as $key => $value) {
				$id = $this->storageItem($value, $path, $disk);
				if ($id) {
					$datas[] = $id;
				}
			}
		} else {
			$id = $this->storageItem($mdFiles, $path, $disk);
			if ($id) {
				$datas[] = $id;
			}
		}
		return count($datas) ? collect($datas) : false;
	}
	private function storageItem($mdFiles, $path = '', $disk = 'public') {
		$builder = new Builder;
		$builder->disk($disk);
		if ($this->request && !empty($this->request->oauth_ent_id)) {
			$builder->ent_id($this->request->oauth_ent_id);
		}
		$builder->user_id(Auth::id());
		$builder->code(Uuid::generate(1, 'gmf', Uuid::NS_DNS, ""));
		$builder->title($mdFiles['name']);
		if (!empty($mdFiles['size'])) {
			$builder->size($mdFiles['size']);
		}
		if (!empty($mdFiles['type'])) {
			$builder->type($mdFiles['type']);
		}
		if (!empty($mdFiles['ext'])) {
			$builder->ext($mdFiles['ext']);
		}

		if ($path) {
			$name = $path . '/' . date('Ymd', time());
		} else {
			$name = date('Ymd', time());
		}
		$name .= Uuid::generate(1, 'gmf', Uuid::NS_DNS, "");
		if ($builder->ext) {
			$name .= '.' . $builder->ext;
		}
		$builder->path($name);

		if (!empty($mdFiles['base64'])) {
			$builder->data($mdFiles['base64']);
			$builder->url($this->toFile($builder));
		}
		return Models\File::create($builder->toArray());
	}
	private function toFile($builder) {
		if (preg_match('/^(data:)/', $builder->data, $result)) {
			$base64_body = substr(strstr($builder->data, ','), 1);
			$type = $builder->type;
			$img = base64_decode($base64_body);

			$disk = Storage::disk($builder->disk);

			$bool = $disk->put($builder->path, $img);
			if (!$bool) {
				return false;
			}
			$img_url = $disk->url($builder->path);
			return $img_url;
		}
		return false;
	}
}
