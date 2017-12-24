<?php

namespace Gmf\Sys\GAuth;

class GAuthGuard {
	protected $m_ent;
	protected $m_user;
	protected $m_client;

	public function login($user, $ent) {

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
		return $this->m_user;
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
