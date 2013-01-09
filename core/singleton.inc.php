<?php
/**
 * Cosix Bitrix Extender
 *
 * @package core
 * @version $Id$
 * @author Peredelskiy Aleksey <info@web-n-roll.ru>
 */

/**
 * @package core
 */
abstract class CSX_Singleton extends CSX_Observable
{
	protected static $instance = array();

	protected abstract function __construct($args = array());

	protected static function _getInstance($className)
	{
		if (!isset(self::$instance[$className])) {
			$args = func_get_args();
			array_shift($args);

			$l = array();
			for ($i = 0; $i < count($args); $i++) {
				$l[] = '$args[' . $i . ']';
			}

			$s = "return new $className(" . implode(',', $l) . ');';
			self::$instance[$className] = eval($s);
		}

		return self::$instance[$className];
	}

	public abstract static function getInstance();

	public static function getInstanceStatic($className)
	{
		return self::_getInstance($className);
	}
}