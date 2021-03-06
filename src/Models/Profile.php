<?php

namespace Gmf\Sys\Models;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_profiles';
	public $incrementing = false;
	protected $fillable = ['id', 'ent_id', 'code', 'name', 'memo', 'default_value', 'scope_enum'];
	public function values() {
		return $this->hasMany('Gmf\Sys\Models\ProfileValue');
	}
	public static function getValue($code, $opts = []) {
		$v = '';
		$query = Profile::with('values')->where('code', $code);
		if (!empty($opts['ent_id'])) {
			$query->where('ent_id', $opts['ent_id']);
		}

		$p = $query->first();
		if ($p) {
			$v = $p->default_value;
		}
		if ($p && $p->values && count($p->values)) {
			$v = $p->values[0]->value;
		}
		return $v;
	}
	public static function setValue($code, $value, $opts = []) {
		$v = '';
		$query = Profile::where('code', $code);
		if (!empty($opts['ent_id'])) {
			$query->where('ent_id', $opts['ent_id']);
		}
		$p = $query->first();
		if (!$p) {
			$opts['code'] = $code;
			$opts['default_value'] = $value;
			$p = Profile::create($opts);
		}
		$pv = ProfileValue::where('profile_id', $p->id)->first();
		if (!$pv) {
			ProfileValue::create(['profile_id' => $p->id, 'value' => $value]);
		} else {
			ProfileValue::where('profile_id', $p->id)->update(['value' => $value]);
		}
		return $value;
	}
}
