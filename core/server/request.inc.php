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
class CSX_Server_Request extends CSX_Hash
{
	protected $cookies = array();

	public function __construct()
	{
		$result = $this->fixFilesArray($_FILES);
		$this->removeEmptyFiles($result);

		parent::__construct(array_merge_recursive($this->removeMagicQuotes(array_merge($_GET, $_POST)), $result));
		$this->cookies = $this->removeMagicQuotes($_COOKIE);
	}

	protected function removeEmptyFiles(&$files)
	{
		$names = array('name' => 1, 'type' => 1, 'tmp_name' => 1, 'error' => 1, 'size' => 1);

		$result = array();

		foreach ($files as $key => &$part) {
			if (is_array($part) && array_key_exists('tmp_name', $part)) {
				if (!is_uploaded_file($part['tmp_name'])) {
					unset($files[$key]);
				}
			}
			else {
				$this->removeEmptyFiles($part);
			}
		}
	}

	public function fixFilesArray($files)
	{
		$names = array('name' => 1, 'type' => 1, 'tmp_name' => 1, 'error' => 1, 'size' => 1);

		$result = array();

		foreach ($files as $key => $part) {
			$key = (string)$key;

			if (isset($names[$key]) && is_array($part)) {
				foreach ($part as $position => $value) {
					if (!isset($result[$position])) {
						$result[$position] = array();
					}

					if (is_array($value)) {
						foreach ($value as $k => $v) {
							if (!isset($result[$position][$k])) {
								$result[$position][$k] = array();
							}

							$result[$position][$k][$key] = $v;
						}
					}
					else {
						$result[$position][$key] = $value;
					}
				}
			}
			else if (isset($names[$key]) && !is_array($part)) {
				$result[$key] = $part;
			}
			else if (is_array($part)) {
				$result[$key] = $this->fixFilesArray($part);
			}
		}

		return $result;
	}

	public function isPost()
	{
		return 'POST' == $_SERVER['REQUEST_METHOD'];
	}

	public function hasCookie($name)
	{
		return array_key_exists($name, $this->cookies);
	}

	/**
	 *
	 */
	public function getCookie($name)
	{
		return isset($this->cookies[$name]) ? $this->cookies[$name] : null;
	}

	/**
	 *
	 */
	public function setCookie($name, $value)
	{
		$this->cookies[$name] = $value;
	}

	/**
	 *
	 */
	public function getRequestUri()
	{
		if (CSX_Server::isCli()) {
			$uri = $_SERVER['PHP_SELF'];
		}
		else {
			$uri = $_SERVER['REQUEST_URI'];
			if (array_key_exists('HTTP_X_REWRITE_URL', $_SERVER)) {
				$uri = $_SERVER['HTTP_X_REWRITE_URL'];
			}

			$uri = preg_replace('|^https?://[^/]+|i', '', $uri);
		}

		return $uri;
	}

	/**
	 *
	 */
	public function getOriginalUri()
	{
		$uri = $_SERVER['REQUEST_URI'];
		if (array_key_exists('ORIGINAL_URL', $_SERVER)) {
			$uri = $_SERVER['ORIGINAL_URL'];
		}
		else if (array_key_exists('HTTP_X_REWRITE_URL', $_SERVER)) {
			$uri = $_SERVER['HTTP_X_REWRITE_URL'];
		}
		else if (array_key_exists('REDIRECT_URL', $_SERVER)) {
			$uri = $_SERVER['REDIRECT_URL'];
		}

		$uri = preg_replace('|^https?://[^/]+|i', '', $uri);
		return $uri;
	}

	public function getHost()
	{
		if (CSX_Server::isCli()) {
			return 'PrimalSite PHP Cli framework';
		}
		else {
			if (isset($_SERVER['HTTP_HOST'])) {
				return $_SERVER['HTTP_HOST'];
			}
			else {
				return $_SERVER['SERVER_NAME'];
			}
		}
	}

	public function getPort()
	{
		return isset($_SERVER['HTTP_PORT']) ? $_SERVER['HTTP_PORT'] : null;
	}

	public function getPortPostfix()
	{
		$port = $this->getPort();
		if (!empty($port) && $port != ($this->isSecure() ? '443' : '80')) {
			$port = ':' . $port;
		}
		else {
			$port = '';
		}
		return $port;
	}

	public function getPathInfo()
	{
		return parse_url($this->getRequestUri(), PHP_URL_PATH);
	}

	public function isSecure()
	{
		return isset($_SERVER['HTTPS']) && 'on' == $_SERVER['HTTPS'];
	}

	public function getProtocolPrefix()
	{
		return ($this->isSecure() ? 'https' : 'http') . '://';
	}


	public function getServerAddress()
	{
		return $_SERVER['SERVER_ADDR'];
	}

	public function getServerVariable($var)
	{
		return array_key_exists($var, $_SERVER) ? $_SERVER[$var] : null;
	}

	public function getRemoteAddress()
	{
		$ip = '';
		if (false == (isset($_SERVER['HTTP_X_REAL_IP']) && $ip = $_SERVER['HTTP_X_REAL_IP'])) {
			if (false == (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $ip = $_SERVER['HTTP_X_FORWARDED_FOR'])) {
				if (isset($_SERVER['REMOTE_ADDR'])) {
					$ip = $_SERVER['REMOTE_ADDR'];
				}
			}
		}
		return $ip;
	}

	public function getQueryString()
	{
		return $_SERVER['QUERY_STRING'];
	}

	protected function removeMagicQuotes($value)
	{
		if (get_magic_quotes_gpc()) {
			if (is_array($value)) {
				$result = array();
				foreach ($value as $k => $v) {
					$result[$this->removeMagicQuotes($k)] = $this->removeMagicQuotes($v);
				}
				return $result;
			}
			return stripslashes($value);
		}
		return $value;
	}

	public function getRawPostData()
	{
		return file_get_contents('php://input');
	}
}

?>