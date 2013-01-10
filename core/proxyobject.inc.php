<?php
/**
 * Cosix Bitrix Extender
 *
 * @package core
 * @version $Id$
 * @author Peredelskiy Aleksey <info@web-n-roll.ru>
 */

/**
 *
 * @package core
 */
class CSX_ProxyObject
{
	protected $object = null;

	public function __construct($obj) {
		$this->object = $obj;
	}

	public function __call($method, $args)
	{
		if (method_exists($this, $method) && is_callable(array($this, $method))) {
			return call_user_func_array(array($this, $method), $args);
		}
		elseif (method_exists($this->object, $method) && is_callable(array($this->object, $method))) {
			return call_user_func_array(array($this->object, $method), $args);
		}
		else {
			throw new CSX_Exception("Call to undefined method '{$method}'");
		}
	}
}