<?php

namespace Gmf\Sys\GAuth;
use Auth;

class GAuthGuard {
	protected $m_ent;
	protected $m_user;
	protected $m_client;

	protected $m_forged = false;

	public function setForged($forged = true) {
		$this->m_forged = $forged;
	}
	public function forged() {
		return $this->m_forged;
	}

	public function id() {
		return $this->userId();
	}

	public function check() {
		return !is_null($this->ent());
	}
	public function ent() {
		return $this->m_ent;
	}
	public function entId() {
		if ($this->ent()) {
			return $this->ent()->id;
		}
		return '';
	}
	public function setEnt($ent) {
		$this->m_ent = $ent;
		return $this;
	}

	public function client() {
		return $this->m_client;
	}
	public function clientId() {
		if ($this->client()) {
			return $this->client()->id;
		}
		return '';
	}
	public function setClient($client) {
		$this->m_client = $client;
		return $this;
	}
	public function user() {
		if ($this->m_user) {
			return $this->m_user;
		}
		if (!$this->forged()) {
			return Auth::user();
		}
		return null;
	}
	public function userId() {
		if ($this->user()) {
			return $this->user()->id;
		}
		return '';
	}
	public function setUser($user) {
		$this->m_user = $user;
		return $this;
	}
}
