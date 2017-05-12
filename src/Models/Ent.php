<?php

namespace Gmf\Sys\Models;
use Closure;
use Gmf\Sys\Builder;
use Gmf\Sys\Passport\Client;
use Gmf\Sys\Passport\PersonalAccessClient;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class Ent extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_ents';
	public $incrementing = false;
	protected $keyType = 'string';
	protected $fillable = ['id', 'code', 'name', 'memo', 'shortName', 'avatar'];

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
					$ent = static::create(array_only($builder->toArray(), ['id', 'code', 'name', 'memo']));
				}
			}
			if (!$ent) {
				$ent = static::create(array_only($builder->toArray(), ['id', 'code', 'name', 'memo']));
			}

			$id = $ent->id;
			//客户端
			if (!Client::find($id)) {

				$b = new Builder(array_only($builder->toArray(), ['name', 'secret', 'user_id', 'personal_access_client', 'password_client']));
				$b->id($id)->revoked(0);

				Client::create($b->toArray());
			}
			//私有端
			if (!empty($builder->personal_access_client)) {

				if (!PersonalAccessClient::find($id)) {
					$b = new Builder();
					$b->id($id)->client_id($id);
					PersonalAccessClient::create($b->toArray());
				}
			}

		});
	}
}
