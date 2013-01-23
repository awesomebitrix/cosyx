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

	public static function dateRU($date)
	{
		$d = explode(' ', $date);
		if (count($d == 2)) {
			$d = $d[0];
		}
		else {
			$d = $date;
		}
		$d = explode('-', $d);
		if (count($d == 3)) {
			$d = "{$d[2]}.{$d[1]}.{$d[0]}";
		}
		else {
			$d = NULL;
		}
		return $d;
	}

	public static function dateMD($date)
	{
		$d = explode('.', $date);
		if (count($d == 3)) {
			$d = "{$d[2]}-{$d[1]}-{$d[0]}";
		}
		else {
			$d = NULL;
		}
		return $d;
	}

	public static function dateArr($start, $finish)
	{
		$dates = array();
		if (is_array($start)) {
			$mindate_ = $start;
		}
		else {
			$mindate_ = Util::dateRUToArray($start);
		}
		if (is_array($finish)) {
			$maxdate_ = $finish;
		}
		else {
			$maxdate_ = Util::dateRUToArray($finish);
		}
		$start = mktime(0, 0, 0, $mindate_['m'], $mindate_['d'], $mindate_['y']);
		$finish = mktime(0, 0, 0, $maxdate_['m'], $maxdate_['d'], $maxdate_['y']);
		$currdate = $start;
		$i = 0;
		while ($currdate < $finish) {
			$currdate = mktime(0, 0, 0, $mindate_['m'], $mindate_['d'] + $i, $mindate_['y']);
			$dates[] = date('Y-m-d', $currdate);
			$i++;
		}
		return $dates;
	}
}