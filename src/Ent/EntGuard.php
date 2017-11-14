<?php

namespace Gmf\Sys\Ent;

class EntGuard {
	protected $m_ent;

	public function check() {
		return !is_null($this->ent());
	}
	public function ent() {
		return $this->m_ent;
	}
	public function id() {
		if ($this->ent()) {
			return $this->ent()->id;
		}
		return '';
	}
	public function setEnt($ent) {
		$this->m_ent = $ent;
		return $this;
	}
}
