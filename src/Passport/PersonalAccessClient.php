<?php

namespace Gmf\Sys\Passport;

use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class PersonalAccessClient extends Model {
	use Snapshotable, HasGuard;

	public $incrementing = false;
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'gmf_oauth_personal_access_clients';

	/**
	 * The guarded attributes on the model.
	 *
	 * @var array
	 */
	protected $guarded = [];

	/**
	 * Get all of the authentication codes for the client.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function client() {
		return $this->belongsTo(Client::class);
	}
}
