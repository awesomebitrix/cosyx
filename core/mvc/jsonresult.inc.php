<?php
/**
 * Cosyx Bitrix Extender Project
 *
 * @package mvc
 * @version $Id$
 * @author Peredelskiy Aleksey <info@web-n-roll.ru>
 */

/**
 * @package mvc
 */
class CSX_Mvc_JsonResult extends CSX_Mvc_ActionResult {
	protected $obj;
	
	public function __construct($obj) {
		$this->obj = $obj;
	}
	
	public function getResult() {
		return json_encode($this->obj);
	}
}