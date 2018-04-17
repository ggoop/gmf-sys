<?php

namespace Gmf\Sys\Models;
use Closure;
use Gmf\Sys\Builder;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class Ent extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_ents';
	public $incrementing = false;
	protected $keyType = 'string';
	protected $fillable = ['id', 'code', 'name', 'memo', 'short_name', 'avatar',
		'dc_host', 'dc_key', 'dc_secret', 'dc_token', 'in_host',
		'industry', 'area', 'revoked'];

	public static function addUser($entId, $userId, $type = 'member') {
		$m = EntUser::where('ent_id', $entId)->where('user_id', $userId)->first();
		if (!$m) {
			$m = EntUser::create(['ent_id' => $entId, 'user_id' => $userId, 'type_enum' => $type]);
		}
		return $m;
	}

	public static function build(Closure $callback) {
		//id,root,parent,code,name,memo,uri,sequence
		tap(new Builder, function ($builder) use ($callback) {
			$callback($builder);
			//用户验证
			if (!empty($builder->user_id)) {
				$user = User::find($builder->user_id);
				if (!$user) {
					$user = User::create(['type' => 'sys', 'id' => $builder->user_id, 'name' => $builder->name]);
					$builder->user_id($user->id);
				}
			}
			if (empty($builder->user_id)) {
				$user = User::create(['type' => 'sys', 'name' => $builder->name]);
				$builder->user_id($user->id);
			}

			//企业
			$ent = false;

			if (!empty($builder->id)) {
				$ent = Ent::find($builder->id);
				if (!$ent) {
					$ent = static::create(array_only($builder->toArray(), ['id', 'code', 'name', 'memo', 'short_name', 'avatar', 'dc_host', 'industry', 'area']));
				}
			}
			if (!$ent) {
				$ent = static::create(array_only($builder->toArray(), ['id', 'code', 'name', 'memo', 'short_name', 'avatar', 'dc_host', 'industry', 'area']));
			}
			return $ent;
		});
	}
}
