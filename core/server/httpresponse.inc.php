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
class CSX_Server_HttpResponse extends CSX_Server_Response
{
	protected $status = CSX_Server_HttpResponse::SC_OK;
	protected $headers = array();
	protected $cookies = array();

	/**
	 * Send response
	 *
	 * @access public
	 * @return void
	 */
	protected function sendResponse()
	{
		$this->sendHeaders();
		echo $this->getContent();
	}

	/**
	 * Send error
	 * @param int    $code
	 * @param string $message
	 */
	function sendError($code, $message = null)
	{
		$this->setStatus($code);
		$this->setContent($message);
		$this->send();
	}

	/**
	 * Send redirect
	 * If url is null redirected to current page
	 * with previous request parameters
	 * @param string $url
	 * @param int $refresh
	 * @param string $content
	 */
	function sendRedirect($url = null, $refresh = 0, $content = null)
	{
		$url = $this->getRealUrl($url);
		if ($this->status !== CSX_Server_HttpResponse::SC_MOVED_PERMANENTLY) {
			$this->setStatus(CSX_Server_HttpResponse::SC_MOVED_TEMPORARILY);
		}
		$this->sendCookies();
		$this->sendStatus();
		if ($refresh > 0 || strlen($content)) {
			$this->sendHeader('Refresh: ' . (int)$refresh . '; URL=' . $url);
			echo $content;
		}
		else {
			$this->sendHeader('Location: ' . $url);
		}
		$this->stop();
	}

	protected function stop()
	{
		$this->isSent = true;
		exit;
	}

	/**
	 * Return absolut URL
	 *
	 * @param string $url
	 * @access public
	 * @return string
	 */
	public function getRealUrl($url = null)
	{
		$url = trim((string)$url);
		if (!strlen($url)) {
			$url = CSX_Server::getRequest()->getRequestUri();
		}
		elseif (!preg_match('/^\w+:\/\//i', $url)) {
			$base = CSX_Server::getRequest()->getProtocolPrefix() . CSX_Server::getRequest()->getHost() . CSX_Server::getRequest()->getPortPostfix();
			if (substr($url, 0, 1) == "/") {
				$url = $base . $url;
			}
			elseif (substr($url, 0, 1) == "?") {
				if (1 === strlen($url)) {
					$url = '';
				}
				$url = $base . preg_replace("/\?.*$/", "", CSX_Server::getRequest()->getRequestUri()) . $url;
			}
			else {
				$url = $base . dirname(CSX_Server::getRequest()->getRequestUri()) . "/" . $url;
			}
		}
		return $url;
	}

	/**
	 * Send status
	 *
	 * @param int $status
	 * @access protected
	 * @return void
	 */
	protected function sendStatus()
	{
		if (empty($this->status)) {
			$this->status = self::SC_OK;
		}
		switch ($this->status) {
			case self::SC_NOT_FOUND:
				header('HTTP/1.0 404 Not found', true, $this->status);
				break;
			case self::SC_INTERNAL_SERVER_ERROR:
				header('HTTP/1.0 500 Internal Server Error', true, $this->status);
				break;
			case self::SC_MOVED_PERMANENTLY:
				header('HTTP/1.0 301 Moved permanently', true, $this->status);
				break;
			default:
				header('HTTP/1.0', true, $this->status);
				break;
		}
	}

	/**
	 * Send headers to client
	 *
	 */
	protected function sendHeaders()
	{
		if (headers_sent($file, $line)) {
			if ($file) {
				$file = " in " . $file . " on line " . $line;
			}
			trigger_error("HTTP Response: can't send headers - already sent" . $file, E_USER_WARNING);
			return false;
		}
		$this->sendCookies();
		$this->sendStatus();
		foreach ($this->headers as $name => $value) {
			$this->sendHeader($name . ': ' . $value);
		}
	}

	/**
	 * Send header to client
	 *
	 * @param string $header
	 * @access protected
	 * @return void
	 */
	protected function sendHeader($header)
	{
		header($header);
	}

	/**
	 * Send cookies to client
	 *
	 */
	protected function sendCookies()
	{
		foreach ($this->cookies as $cookie) {
			$this->sendCookie($cookie);
		}
	}

	/**
	 * Send cookie to client
	 *
	 */
	protected function sendCookie($cookie)
	{
		call_user_func_array('setcookie', $cookie);
	}

	/**
	 * Set cookie
	 * @param name
	 * @param value
	 * @param expires
	 * @param path
	 * @param domain
	 * @param secure
	 */
	public function setCookie($name, $value = null, $expires = null, $path = null, $domain = null, $secure = null)
	{
		$this->cookies[$name] = func_get_args();
	}

	public function setCookieImmediate($name, $value = null, $expires = null, $path = null, $domain = null, $secure = null)
	{
		$args = func_get_args();
		call_user_func_array('setcookie', $args);
	}

	/**
	 * Remove cookie
	 *
	 * Remove cookie by sending it with null value and
	 * expires time in past.
	 *
	 * @param string $name
	 * @access public
	 * @return void
	 */
	function removeCookie($name)
	{
		$this->setCookie($name, null, $this->getTimeInPast());
	}

	protected function getTimeInPast()
	{
		return time() - 1000;
	}

	/**
	 * Get all response cookies
	 *
	 * @return array
	 */
	function getCookies()
	{
		return $this->cookies;
	}

	/**
	 * Add a field to the response header
	 * with the given name and value.
	 * @param string $field
	 * @param string $value
	 * @param bool   $append - append to existing header
	 */
	function setHeader($name, $value, $append = false)
	{
		if (null === $value || false === $value) {
			unset($this->headers[$name]);
		}
		elseif ($this->hasHeader($name) && $append) {
			$this->headers[$name] .= ';' . $value; // ????
		}
		else {
			$this->headers[$name] = $value;
		}
	}

	public function setContentType($contentType)
	{
		$this->setHeader('Content-type', $contentType);
	}

	/**
	 * Get header
	 * @param string $name header name
	 * @return string
	 */
	function getHeader($name)
	{
		return $this->headers[$name];
	}

	/**
	 * Check if header already exists
	 * @param string $name header name
	 * @return bool
	 */
	function hasHeader($name)
	{
		return isset($this->headers[$name]);
	}

	/**
	 * Get all response headers
	 *
	 * @return array
	 */
	function getHeaders()
	{
		return $this->headers;
	}

	/**
	 * Set response status
	 * @param int $code
	 */
	function setStatus($code)
	{
		$this->status = $code;
	}

	/**
	 * Get response status
	 *
	 * @return int
	 */
	function getStatus()
	{
		return $this->status;
	}

	function save()
	{
		return serialize(array($this->status, $this->headers, parent::save()));
	}

	function restore($data)
	{
		if (is_array($data = unserialize($data)) && (count($data) == 3) && is_array($data[1])) {
			if (parent::restore($data[2])) {
				list($this->status, $this->headers,) = $data;
				return true;
			}
		}
		return false;
	}

	/**
	 * Status code (202) indicating that a request was accepted for processing, but was not completed.
	 */
	const SC_ACCEPTED = 202;
	/**
	 * Status code (502) indicating that the HTTP server received an invalid response from a server it consulted when acting as a proxy or gateway.
	 */
	const SC_BAD_GATEWAY = 502;
	/**
	 * Status code (400) indicating the request sent by the client was syntactically incorrect.
	 */
	const SC_BAD_REQUEST = 400;
	/**
	 * Status code (409) indicating that the request could not be completed due to a conflict with the current state of the resource.
	 */
	const SC_CONFLICT = 409;
	/**
	 * Status code (100) indicating the client can continue.
	 */
	const SC_CONTINUE = 100;
	/**
	 * Status code (201) indicating the request succeeded and created a new resource on the server.
	 */
	const SC_CREATED = 201;
	/**
	 * Status code (403) indicating the server understood the request but refused to fulfill it.
	 */
	const SC_FORBIDDEN = 403;
	/**
	 * Status code (504) indicating that the server did not receive a timely response from the upstream server while acting as a gateway or proxy.
	 */
	const SC_GATEWAY_TIMEOUT = 504;
	/**
	 * Status code (410) indicating that the resource is no longer available at the server and no forwarding address is known.
	 */
	const SC_GONE = 410;
	/**
	 * Status code (505) indicating that the server does not support or refuses to support the HTTP protocol version that was used in the request message.
	 */
	const SC_HTTP_VERSION_NOT_SUPPORTED = 505;
	/**
	 * Status code (500) indicating an error inside the HTTP server which prevented it from fulfilling the request.
	 */
	const SC_INTERNAL_SERVER_ERROR = 500;
	/**
	 * Status code (411) indicating that the request cannot be handled without a defined Content-Length.
	 */
	const SC_LENGTH_REQUIRED = 411;
	/**
	 * Status code (405) indicating that the method specified in the Request-Line is not allowed for the resource identified by the Request-URI.
	 */
	const SC_METHOD_NOT_ALLOWED = 405;
	/**
	 * Status code (301) indicating that the resource has permanently moved to a new location, and that future references should use a new URI with their requests.
	 */
	const SC_MOVED_PERMANENTLY = 301;
	/**
	 * Status code (302) indicating that the resource has temporarily moved to another location, but that future references should still use the original URI to access the resource.
	 */
	const SC_MOVED_TEMPORARILY = 302;
	/**
	 * Status code (300) indicating that the requested resource corresponds to any one of a set of representations, each with its own specific location.
	 */
	const SC_MULTIPLE_CHOICES = 300;
	/**
	 * Status code (204) indicating that the request succeeded but that there was no new information to return.
	 */
	const SC_NO_CONTENT = 204;
	/**
	 * Status code (203) indicating that the meta information presented by the client did not originate from the server.
	 */
	const SC_NON_AUTHORITATIVE_INFORMATION = 203;
	/**
	 * Status code (406) indicating that the resource identified by the request is only capable of generating response entities which have content characteristics not acceptable according to the accept headerssent in the request.
	 */
	const SC_NOT_ACCEPTABLE = 406;
	/**
	 * Status code (404) indicating that the requested resource is not available.
	 */
	const SC_NOT_FOUND = 404;
	/**
	 * Status code (501) indicating the HTTP server does not support the functionality needed to fulfill the request.
	 */
	const SC_NOT_IMPLEMENTED = 501;
	/**
	 * Status code (304) indicating that a conditional GET operation found that the resource was available and not modified.
	 */
	const SC_NOT_MODIFIED = 304;
	/**
	 * Status code (200) indicating the request succeeded normally.
	 */
	const SC_OK = 200;
	/**
	 * Status code (206) indicating that the server has fulfilled the partial GET request for the resource.
	 */
	const SC_PARTIAL_CONTENT = 206;
	/**
	 * Status code (402) reserved for future use.
	 */
	const SC_PAYMENT_REQUIRED = 402;
	/**
	 * Status code (412) indicating that the precondition given in one or more of the request-header fields evaluated to false when it was tested on the server.
	 */
	const SC_PRECONDITION_FAILED = 412;
	/**
	 * Status code (407) indicating that the client MUST first authenticate itself with the proxy.
	 */
	const SC_PROXY_AUTHENTICATION_REQUIRED = 407;
	/**
	 * Status code (413) indicating that the server is refusing to process the request because the request entity is larger than the server is willing or able to process.
	 */
	const SC_REQUEST_ENTITY_TOO_LARGE = 413;
	/**
	 * Status code (408) indicating that the client did not produce a requestwithin the time that the server was prepared to wait.
	 */
	const SC_REQUEST_TIMEOUT = 408;
	/**
	 * Status code (414) indicating that the server is refusing to service the request because the Request-URI is longer than the server is willing to interpret.
	 */
	const SC_REQUEST_URI_TOO_LONG = 414;
	/**
	 * Status code (205) indicating that the agent SHOULD reset the document view which caused the request to be sent.
	 */
	const SC_RESET_CONTENT = 205;
	/**
	 * Status code (303) indicating that the response to the request can be found under a different URI.
	 */
	const SC_SEE_OTHER = 303;
	/**
	 * Status code (503) indicating that the HTTP server is temporarily overloaded, and unable to handle the request.
	 */
	const SC_SERVICE_UNAVAILABLE = 503;
	/**
	 * Status code (101) indicating the server is switching protocols according to Upgrade header.
	 */
	const SC_SWITCHING_PROTOCOLS = 101;
	/**
	 * Status code (401) indicating that the request requires HTTP authentication.
	 */
	const SC_UNAUTHORIZED = 401;
	/**
	 * Status code (415) indicating that the server is refusing to service the request because the entity of the request is in a format not supported by the requested resource for the requested method.
	 */
	const SC_UNSUPPORTED_MEDIA_TYPE = 415;
	/**
	 * Status code (305) indicating that the requested resource MUST be accessed through the proxy given by the Location field.
	 */
	const SC_USE_PROXY = 305;
}
