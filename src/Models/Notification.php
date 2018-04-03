<?php

namespace Gmf\Sys\Models;
use Carbon\Carbon;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_notifications';
	public $incrementing = false;
	protected $fillable = ['id', 'via', 'type', 'fm_user_id', 'user_id', 'src_id', 'src_type', 'content', 'is_completed'];

	public function fm_user() {
		return $this->belongsTo(config('gmf.user.model'));
	}
	public function user() {
		return $this->belongsTo(config('gmf.user.model'));
	}
	public function src() {
		return $this->morphTo();
	}

	public function scopeRecent($query) {
		return $query->orderBy('created_at', 'desc');
	}
	public function scopeWithVia($query, $via) {
		return $query->where('via', '=', $via);
	}
	public function scopeWithType($query, $type) {
		return $query->where('type', '=', $type);
	}
	public function scopeNews($query) {
		return $query->where('is_completed', 0);
	}
	public function scopeToWhom($query, $user_id) {
		return $query->where('user_id', '=', $user_id);
	}

	public static function markAsRead($ids) {
		if (is_string($ids)) {
			static::whereIn('id', explode(',', $ids))->update(['is_completed' => '1']);
		} else if (count($ids)) {
			static::whereIn('id', $ids)->update(['is_completed' => '1']);
		}
	}
	public static function markAsArrived($ids) {
		if (!is_array($ids)) {
			$ids = explode(',', $ids);
		}
		static::whereIn('id', $ids)->update(['arrived_at' => Carbon::now()]);
	}
	public static function markAsError($ids, $error, \Exception $exception = null) {
		$msg = false;
		if (!$msg && method_exists($exception, 'getRawMessage')) {
			$msg = $exception->getRawMessage();
		}
		if (!$msg && method_exists($exception, 'getMessage')) {
			$msg = $exception->getMessage();
		}
		if (!is_array($ids)) {
			$ids = explode(',', $ids);
		}

		if (!empty($msg)) {
			$error = $error . ':' . $msg;
		}

		static::whereIn('id', $ids)->update(['error' => $error]);
	}
}
