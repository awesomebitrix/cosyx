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
class CSX_String
{
	protected function __construct()
	{
	}

	public static function getInstance()
	{
		static $instance = null;
		if ($instance == null) {
			$instance = new CSX_String();
		}

		return $instance;
	}

	/**
	 *
	 */
	public static function toUpper($s)
	{
		return mb_strtoupper($s);
	}

	/**
	 *
	 */
	public static function toLower($s)
	{
		return mb_strtolower($s);
	}

	public static function len($s)
	{
		return mb_strlen($s);
	}

	public static function substr()
	{
		$ar = func_get_args();
		return call_user_func_array('mb_substr', $ar);
	}

	public static function substrReplace($output, $replace, $pos, $len)
	{
		return self::substr($output, 0, $pos) . $replace . self::substr($output, $pos + $len);
	}

	public static function strReplace($needle, $replacement, $haystack)
	{
		$needle_len = mb_strlen($needle);
		$replacement_len = mb_strlen($replacement);
		$pos = mb_strpos($haystack, $needle);
		while ($pos !== false) {
			$haystack = mb_substr($haystack, 0, $pos) . $replacement
				. mb_substr($haystack, $pos + $needle_len);
			$pos = mb_strpos($haystack, $needle, $pos + $replacement_len);
		}
		return $haystack;
	}

	/**
	 *
	 */
	public static function ucWords($s)
	{
		$l = explode(" ", $s);
		for ($i = 0; $i < count($l); $i++) {
			$s = $l[$i];
			$l[$i] = mb_strtoupper(mb_substr($s, 0, 1)) . mb_substr($s, 1);
		}
		return implode(" ", $l);
	}

	public static function toUtf($s)
	{
		if (is_object($s)) {
			foreach ($s as $k => $v) {
				$s->$k = CSX_String::toUtf($v);
			}

			return $s;
		}
		else if (is_array($s)) {
			foreach ($s as $k => $v) {
				$s[$k] = CSX_String::toUtf($v);
			}

			return $s;
		}
		else {
			return is_string($s) ? iconv('cp1251', 'utf-8//TRANSLIT', $s) : $s;
		}
	}

	public static function toWin($s)
	{
		if (is_object($s)) {
			foreach ($s as $k => $v) {
				$s->$k = CSX_String::toWin($v);
			}

			return $s;
		}
		else if (is_array($s)) {
			foreach ($s as $k => $v) {
				$s[$k] = CSX_String::toWin($v);
			}

			return $s;
		}
		else {
			return is_string($s) ? iconv('utf-8', 'cp1251//TRANSLIT', $s) : $s;
		}
	}

	public static function format()
	{
		$args = func_get_args();
		$args[0] = CSX_String::lc($args[0]);

		return call_user_func_array('sprintf', $args);
	}

	public static function generateRandomPassword()
	{
		$words = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
		$wcount = strlen($words);
		$length = 6;

		$password = "";
		for ($i = 0; $i < $length; $i++) {
			$r = rand(0, $wcount - 1);
			$password .= $words{$r};
		}

		return $password;

	}

	public static function generateRandomString($length = 6)
	{
		$words = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
		$wcount = strlen($words);

		$password = "";
		for ($i = 0; $i < $length; $i++) {
			$r = rand(0, $wcount - 1);
			$password .= $words{$r};
		}

		return $password;

	}

	public static function generateRandomNumber($length = 6)
	{
		$words = "0123456789";
		$wcount = strlen($words);

		$password = "";
		for ($i = 0; $i < $length; $i++) {
			$r = rand(0, $wcount - 1);
			$password .= $words{$r};
		}

		return $password;

	}

	protected static $tlTable = array(
		"А" => "A",
		"Б" => "B",
		"В" => "V",
		"Г" => "G",
		"Д" => "D",
		"Е" => "E",
		"Ё" => "E",
		"Ж" => "ZH",
		"З" => "Z",
		"И" => "I",
		"Й" => "I",
		"К" => "K",
		"Л" => "L",
		"М" => "M",
		"Н" => "N",
		"О" => "O",
		"П" => "P",
		"Р" => "R",
		"С" => "S",
		"Т" => "T",
		"У" => "U",
		"Ф" => "F",
		"Х" => "H",
		"Ц" => "c",
		"Ч" => "CH",
		"Ш" => "SH",
		"Щ" => "SH",
		"Ь" => "",
		"Ы" => "I",
		"Ъ" => "",
		"Э" => "E",
		"Ю" => "U",
		"Я" => "YA",
		"а" => "a",
		"б" => "b",
		"в" => "v",
		"г" => "g",
		"д" => "d",
		"е" => "e",
		"ё" => "e",
		"ж" => "zh",
		"з" => "z",
		"и" => "i",
		"й" => "i",
		"к" => "k",
		"л" => "l",
		"м" => "m",
		"н" => "n",
		"о" => "o",
		"п" => "p",
		"р" => "r",
		"с" => "s",
		"т" => "t",
		"у" => "u",
		"ф" => "f",
		"х" => "h",
		"ц" => "c",
		"ч" => "ch",
		"ш" => "sh",
		"щ" => "sh",
		"ь" => "",
		"ы" => "i",
		"ъ" => "",
		"э" => "e",
		"ю" => "u",
		"я" => "ya"
	);

	public static function tl($str)
	{
		$str_out = '';
		for ($i = 0; $i < mb_strlen($str); $i++) {
			$c = mb_substr($str, $i, 1);
			if (array_key_exists($c, self::$tlTable)) {
				$tl = self::$tlTable[$c];
				$str_out .= $tl;
			}
			else {
				$str_out .= $c;
			}
		}

		return $str_out;
	}

	public static function toMysqlDateTime($s)
	{
		$t = strtotime($s);
		return date('YmdHis', $t);
	}

	public static function isValidEmail($email)
	{
		if (preg_match("/[a-zA-Z0-9_\-\.\+]+@[a-zA-Z0-9\-]+.[a-zA-Z]+/", $email) > 0)
			return true;
		else
			return false;
	}

	public static function isValidPhone($phone)
	{
		if (preg_match("/^\+\d{11}$/", $phone) > 0)
			return true;
		else
			return false;
	}

	public static function breaks($s)
	{
		return preg_replace("/\n/", "<br/>", $s);
	}

	/**
	 *
	 */
	public static function suffix($num, $base, $s1, $s2_4, $s5_20)
	{
		return $num . " " . self::suffixEx($num, $base, $s1, $s2_4, $s5_20);
	}

	/**
	 *
	 */
	public static function suffixEx($num, $base, $s1, $s2_4, $s5_20)
	{
		$suffix = $base;

		$goodnum = $num % 100;

		if ($goodnum == 1) {
			$suffix .= $s1;
		}
		else if ($goodnum > 1 && $goodnum < 5) {
			$suffix .= $s2_4;
		}
		else if ($goodnum > 5 && $goodnum < 20) {
			$suffix .= $s5_20;
		}
		else {
			$lastdigit = $goodnum % 10;
			if ($lastdigit == 1) {
				$suffix .= $s1;
			}
			else if ($lastdigit > 1 && $lastdigit < 5) {
				$suffix .= $s2_4;
			}
			else {
				$suffix .= $s5_20;
			}
		}

		return $suffix;
	}

	public static $PADLIST = array("i", "r", "v", "d", "t", "p");

	public static function pad($p, $base, $s)
	{
		$list = explode(',', $s);
		return self::padEx($p, $base, $list);
	}

	public static function padEx($p, $base, $list)
	{
		$idx = array_search($p, self::$PADLIST);
		if ($idx !== FALSE) {
			return $base . $list[$idx];
		}
		else {
			return "";
		}
	}

	public static function ellipse($string, $length = 30)
	{
		if (mb_strlen($string) > $length) {
			$string = mb_substr($string, 0, $length) . '...';
		}

		return $string;
	}

	public static function intval($string)
	{
		return intval(str_replace(',', '.', $string));
	}

	public static function floatval($string)
	{
		return floatval(str_replace(',', '.', $string));
	}
}

?>