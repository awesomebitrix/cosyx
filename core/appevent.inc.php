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
class CSX_AppEvent
{
	public static function fireEvent($name, $args = null)
	{
		$handlers = CSX_Config::get('event');
		if ($handlers == null) return;

		if (array_key_exists($name, $handlers)) {
			$list = $handlers[$name];

			foreach ($list as $item) {
				$class = CSX_Compat::resolveClassName($item['class']);
				$handler = $item['handler'];

				call_user_func_array(array($class, $handler), $args);
			}
		}
	}

}