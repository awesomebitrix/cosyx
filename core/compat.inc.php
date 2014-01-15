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
class CSX_Compat
{
	static public function resolveClassName($className)
	{
		return str_replace('::', '_', $className);
	}

	static public function encodeClassName($className)
	{
		return str_replace('_', '::', $className);
	}

	/**
	 *
	 */
	static public function resolveMethodName($action)
	{
		$method = '';
		for ($i = 0; $i < strlen($action); $i++) {
			$c = $action[$i];
			if (in_array($c, array("_", "."))) {
				if ($i + 1 < strlen($action)) {
					$method .= strtoupper($action[$i + 1]);
					$i++;
				}
				else {
					$method .= $c;
				}
			}
			else {
				$method .= $c;
			}
		}

		return $method;
	}
}