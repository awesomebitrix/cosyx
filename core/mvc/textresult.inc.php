<?php
class CSX_Mvc_TextResult extends CSX_Mvc_ActionResult {
	protected $text;

	public function __construct($text) {
		$this->text = $text;
	}
	
	public function getResult() {
		return $this->text;
	}
}