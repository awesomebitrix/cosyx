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
class CSX_Util {
	public static function get($hash, $key, $default = null) {
		if (array_key_exists($key, $hash)) {
			return $hash[$key];
		}
		else {
			return $default;
		}
	}

	public static function has($hash, $key) {
		return (array_key_exists($key, $hash));
	}

	public static function getl($hash, $key, $lang = null, $default = null) {
		if (array_key_exists($key . ':' . $lang, $hash)) {
			return $hash[$key . ':' . $lang];
		}
		if (array_key_exists($key . ':ne', $hash)) {
			return $hash[$key . ':ne'];
		}
		else if (array_key_exists($key, $hash)) {
			return $hash[$key];
		}
		else {
			return $default;
		}
	}

	public static function clearEmpty(&$hash) {
		foreach ($hash as $k => $v) {
			if (empty($v)) {
				unset($hash[$k]);
			}
		}
	}

	/**
	 *
	 */
	public static function sort(&$rows, $fld, $desc = false) {
		$code = 'return $a["' . $fld . '"]'
			. ($desc ? "<" : ">")
			. '$b["' . $fld . '"];';

		$compare = create_function('$a,$b', $code);
		usort($rows, $compare);
	}

	public static function isAssoc($var) {
		return is_array($var) && array_diff_key($var, array_keys(array_keys($var)));
	}
}