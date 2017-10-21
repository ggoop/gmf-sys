<?php

namespace Gmf\Sys\Models\Authority;
use Closure;
use Gmf\Sys\Builder;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class RoleUser extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_authority_role_users';
	public $incrementing = false;
	protected $fillable = ['ent_id', 'role_id', 'user_id'];

	public function role() {
		return $this->belongsTo('Gmf\Sys\Models\Authority\Role');
	}
	public function user() {
		return $this->belongsTo(config('gmf.user.model'));
	}

	public static function build(Closure $callback) {
		tap(new Builder, function ($builder) use ($callback) {
			$callback($builder);
			$data = array_only($builder->toArray(), ['id', 'ent_id', 'role_id', 'user_id']);

			if (!empty($builder->role)) {
				$tmp = Role::where('code', $builder->role)->where('ent_id', $builder->ent_id)->first();
				$data['role_id'] = $tmp ? $tmp->id : '';
			}
			static::updateOrCreate(['ent_id' => $data['ent_id'], 'role_id' => $data['role_id'], 'user_id' => $data['user_id']], $data);
		});
	}
}
