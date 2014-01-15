<?php
/**
 * Cosyx Bitrix Extender Project
 *
 * @package controller
 * @version $Id$
 * @author Peredelskiy Aleksey <info@web-n-roll.ru>
 */

/**
 * 
 *
 * @package controller
 */
class CSX_Controller_Param extends CSX_Controller {
	protected $parameters = array();

	public function run($params) {
		$matches = $params['matches'];
		$names = array_key_exists('names', $params) ? $params['names'] : array();

		for ($i=1;$i<count($matches);$i++) {
			$this->parameters[$names[$i-1]] = $matches[$i];
		}

		return true;
	}
}