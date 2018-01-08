<?php

namespace Gmf\Sys\Models;
use Gmf\Sys\Traits\HasGuard;
use Illuminate\Database\Eloquent\Model;

class Transfer extends Model {
	use HasGuard;
	protected $table = 'gmf_sys_transfers';
	protected $fillable = [
		'id', 'client_id', 'client_key',
		'indentifier', 'src_id', 'src_type', 'target_id', 'target_type'];

}
