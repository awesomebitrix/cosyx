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
class CSX_Server_Utility
{
	/**
	 *
	 */
	public static function queryStringExclude()
	{
		$num_args = func_num_args();

		$args = func_get_args();

		return self::queryStringExcludeRaw($args);
	}

	/**
	 *
	 */
	public static function queryStringExcludeRaw($args)
	{
		reset($_GET);

		$list = array();

		while (list($key, $value) = each($_GET)) {
			if (!in_array($key, $args))
				$list[$key] = $value;
		}

		$list1 = array();
		while (list($key, $value) = each($list)) {
			if (is_array($value)) {
				for ($i = 0; $i < count($value); $i++) {
					$list1[] = urlencode($key . "[]") . "=" . urlencode($value[$i]);
				}
			}
			else {
				$list1[] = "$key=" . urlencode($value);
			}
		}

		$string = join("&", $list1);
		if (strlen($string) == 0) {
			$string = "1=1";
		}

		return $string;
	}

	/**
	 *
	 */
	public static function sendFileToClient($filename, $data, $no_contenttype_set = false)
	{
		if (!$no_contenttype_set) {
			CSX_Server::getResponse()->setContentType('application/ofx');
		}

		CSX_Server::getResponse()->setHeader('Content-Disposition', "attachment; filename=$filename");
		CSX_Server::getResponse()->setContent($data);
		CSX_Server::getResponse()->send();
		exit();
	}

	/**
	 *
	 */
	public static function queryStringToForm()
	{
		$num_args = func_num_args();
		$args = func_get_args();
		return self::queryStringToFormRaw($args);
	}

	/**
	 *
	 */
	public static function queryStringToFormRaw($args)
	{
		$num_args = count($args);

		reset($_GET);

		$list = array();
		if ($num_args == 0) {
			$list = $_GET;
		}
		else {
			while (list($key, $value) = each($_GET)) {
				if (!in_array($key, $args)) {
					$list[$key] = $value;
				}
			}
		}

		$output = array();

		while (list($key, $value) = each($list)) {
			if (is_array($value)) {
				for ($i = 0; $i < count($value); $i++) {
					$output[] = "<input type=HIDDEN name=\"" . $key . "[]\" value=\"" . $value[$i] . "\">";
				}
			}
			else
				$output[] = "<input type=HIDDEN name=\"$key\" value=\"$value\">";
		}

		return implode("\n", $output);
	}
}

?>