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
class CSX_Server
{
	protected $request;
	protected $response;
	protected $routeTable;
	protected $session;
	protected $isSessionStarted = false;

	/**
	 *
	 */
	public static function getInstance()
	{
		static $instance;
		if (null === $instance) {
			$instance = new CSX_Server();
		}
		return $instance;
	}

	public static function handleRequest($controllerClass = null, $controllerParams = array())
	{
		try {
			if (self::isHttp()) {
				if (isset($_GET['phpinfo'])) {
					phpinfo();
					exit();
				}

				if ($controllerClass == null) {
					$result = false;
					$after = null;
					$uri = self::getRequest()->getRequestUri();
					while (!$result && ($hash = self::getRouteTable()->find($uri, $after)) !== false) {
						list($class, $params, $name) = $hash;
						$controller = new $class();
						if ($controller instanceof CSX_Controller) {
							if ($controller->validate($params)) {
								$result = $controller->run($params);
								$after = $name;
							}
						}
						else {
							throw new CSX_Exception(sprintf('Class [%s] should be an instance of CSX_Controller', $class));
						}

						//	get url again because controller can modify it
						$uri = self::getRequest()->getRequestUri();
					}
				}
				else {
					$controller = new $controllerClass();
					if ($controller instanceof CSX_Controller) {
						$controller->run($controllerParams);
					}
				}
			}

			if ($result) {
				self::getResponse()->send();
				exit();
			}
		} catch (Exception $e) {
			self::handleError($e);
			exit();
		}
	}

	public static function handleError($error)
	{
		if (!empty($error) && ($error instanceof Exception || false !== strrpos($error, 'Parse error') || false !== strrpos($error, 'Fatal error'))) {

			if (is_string($error)) {
				$error = trim($error);
			}

			if (self::isHttp()) {
				self::getResponse()->setHeader('content-type', 'text/plain; charset=UTF-8');

				$content = '500 Internal server error';
				if ($error->getCode() == CSX_Server_HttpResponse::SC_NOT_FOUND) {
					self::getResponse()->setStatus(CSX_Server_HttpResponse::SC_NOT_FOUND);
					$content = '404 Not found';
				}
				else if ($error instanceof CSX_Exception && $error->getCode() == CSX_Server_HttpResponse::SC_FORBIDDEN) {
					self::getResponse()->setStatus(CSX_Server_HttpResponse::SC_FORBIDDEN);
					$content = '403 Forbidden';
				}
				else {
					self::getResponse()->setStatus(CSX_Server_HttpResponse::SC_INTERNAL_SERVER_ERROR);
				}
			}

			self::getResponse()->setContent($content);

			self::getResponse()->send();
			return self::getResponse()->getContent();
		}
		else {
			return $error;
		}
	}

	public static function getRequest()
	{
		$self = self::getInstance();
		if (null === $self->request) {
			$self->request = $self->createRequest();
		}
		return $self->request;
	}

	public static function getResponse()
	{
		$self = self::getInstance();
		if (null === $self->response) {
			$self->response = $self->createResponse();
		}
		return $self->response;
	}

	public static function getRouteTable()
	{
		$self = self::getInstance();
		if (null === $self->routeTable) {
			$self->routeTable = $self->createRouteTable();
		}
		return $self->routeTable;
	}

	public static function getSession()
	{
		$self = self::getInstance();
		if (null === $self->session) {
			$self->session = $self->createSession();
		}
		if (!$self->isSessionStarted) {
			$self->session->start();
			$self->isSessionStarted = true;
		}

		return $self->session;
	}

	protected function createRequest()
	{
		return new CSX_Server_Request();
	}

	protected function createResponse()
	{
		if (self::isCli()) {
			return new CSX_Server_CliResponse();
		}
		else {
			return new CSX_Server_HttpResponse();
		}
	}

	protected function createRouteTable()
	{
		return new CSX_Server_RouteTable();
	}

	protected function createSession()
	{
		if (self::isCli()) {
			throw new CSX_Exception('No session is available when running in CLI mode');
		}
		elseif ($store = CSX_Config::get('server', 'session', 'store')) {
			return new CSX_Server_CacheStoreSession(CSX_Cache::getStore($store, false));
		}
		else {
		}

		return new CSX_Server_PhpSession();
	}

	public static function isCli()
	{
		return php_sapi_name() === 'cli';
	}

	public static function isHttp()
	{
		return php_sapi_name() !== 'cli';
	}

	static public function tryExecPhpScript($script)
	{
		$php_path = CSX_Config::get('system', 'php');
		$php_path = empty($php_path) ? 'php' : $php_path;

		$result = exec($php_path . ' -q ' . $script);

		if ($result == "1") {
			return true;
		}

		return false;
	}

	static public function execPhpScript($script, $params = array(), $background = false)
	{
		$php_path = CSX_Config::get('system', 'php');
		$php_path = empty($php_path) ? 'php' : $php_path;

		$commandLine = $php_path . ' -q ' . $script . (!empty($params) ? ' "' . str_replace('"', '\\"', json_encode($params)) . '"' : '');

		if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
			$commandLine .= ' > /dev/null';
		}

		self::exec($commandLine, $background);
	}

	public static function exec($commandLine)
	{
		$descriptorspec = array(
			0 => array("pipe", "r"),
			1 => array("pipe", "w"),
			2 => array("pipe", "w")
		);

		$pr = proc_open($commandLine, $descriptorspec, $pipes);

		if (is_resource($pr)) {
			// $pipes now looks like this:
			// 0 => writeable handle connected to child stdin
			// 1 => readable handle connected to child stdout
			// Any error output will be appended to /tmp/error-output.txt

			fclose($pipes[0]);

			$output = '';
			while (!feof($pipes[1])) {
				$output .= fgets($pipes[1], 128);
			}
			fclose($pipes[1]);

			$err = '';
			while (!feof($pipes[2])) {
				$err .= fgets($pipes[2], 128);
			}
			fclose($pipes[2]);

			// It is important that you close any pipes before calling
			// proc_close in order to avoid a deadlock
			$return_value = proc_close($pr);

			return array(
				'result' => $return_value
			, 'output' => $output
			, 'error' => $err
			);
		}
		else {
			return null;
		}
	}
}