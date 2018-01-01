<?php
namespace Gmf\Sys\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use Storage;

class File extends Resource {
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request
	 * @return array
	 */
	public function toArray($request) {
		if (empty($this->id)) {
			return false;
		}
		$rtn = [];
		Common::toField($this, $rtn, ['id', 'title', 'type', 'ext', 'created_at']);
		Common::toIntField($this, $rtn, ['size']);

		if (!empty($this->pivot)) {
			$rtn['post_id'] = $this->pivot->post_id;
			$rtn['created_at'] = $this->pivot->created_at . '';
		}
		if (empty($this->ext)) {
			$rtn['ext'] = substr(strrchr($this->title, "."), 1);
		}
		if ($this->isImage()) {
			$rtn['url'] = $this->getPathURL($request);
			$rtn['can_image'] = true;
		} else if ($this->isPdf()) {
			$rtn['url'] = $this->getPdfPathURL($request);
			$rtn['can_pdf'] = true;
		}
		return $rtn;
	}
	private function isImage() {
		return !empty($this->type) && starts_with($this->type, 'image/');
	}
	private function isPdf() {
		return !empty($this->pdf_disk) && $this->pdf_disk && !empty($this->pdf_path) && $this->pdf_path;
	}
	private function getPathURL($request) {
		if (!empty($this->path) && !empty($this->disk)) {
			return config('app.url') . Storage::disk($this->disk)->url($this->path);
		}
		return '';
	}
	private function getPdfPathURL($request) {
		if (!empty($this->pdf_path) && !empty($this->pdf_disk)) {
			return config('app.url') . Storage::disk($this->pdf_disk)->url($this->pdf_path);
		}
		return '';
	}
}
