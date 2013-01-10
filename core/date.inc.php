<?php
/**
 * Cosyx Bitrix Extender
 *
 * @package core
 * @author Peredelskiy Aleksey <info@web-n-roll.ru>
 * @version $Id$
 */

/**
 *
 * @package core
 */
class CSX_Date {
	public static function dateRUToArray($date)
	{
		$dd = array();
		$a = explode(' ', $date);
		if (count($a == 2)) {
			$d = $a[0];
			$t = $a[1];
			$t = explode(':', $t);
			$dd['hh'] = isset($t[0]) ? $t[0] : NULL;
			$dd['mm'] = isset($t[1]) ? $t[1] : NULL;
			$dd['ss'] = isset($t[2]) ? $t[2] : NULL;
		}
		else {
			$d = $date;
			$dd['hh'] = NULL;
			$dd['mm'] = NULL;
			$dd['ss'] = NULL;
		}
		$d = explode('.', $d);
		if (count($d == 3)) {
			$dd['y'] = $d[2];
			$dd['m'] = $d[1];
			$dd['d'] = $d[0];
		}
		else {
			$dd = NULL;
		}
		return $dd;
	}
}