<?php
class CSX_Mvc_JsonResult extends CSX_Mvc_ActionResult {
	protected $obj;
	
	public function __construct($obj) {
		$this->obj = $obj;
	}
	
	public function getResult() {
		return json_encode($this->obj);
	}
}