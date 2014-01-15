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
abstract class CSX_Mvc_View {
	protected $view;
	protected $params;
	
	public function __construct($view, $params = array()) {
		$this->view = $view;
		$this->params = $params;
	}
	
	public abstract function fetch();
}