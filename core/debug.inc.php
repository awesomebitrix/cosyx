<?php
class CSX_Debug {
	public static function log($message) {
		AddMessage2Log($message);
	}

	/**
	 * PHP error handler
	 *
	 * @param int	$errno
	 * @param string $errstr
	 * @param string $errfile
	 * @param int	$errline
	 * @param string $errcontext
	 */
	public static function errorHandler($errno, $errstr, $errfile, $errline, $errcontext) {
		static $errorTypes = array(
			E_ERROR => "Error",
			E_WARNING => "Warning",
			E_PARSE => "Parsing Error",
			E_NOTICE => "Notice",
			E_CORE_ERROR => "Core Error",
			E_CORE_WARNING => "Core Warning",
			E_COMPILE_ERROR => "Compile Error",
			E_COMPILE_WARNING => "Compile Warning",
			E_USER_ERROR => "User Error",
			E_USER_WARNING => "User Warning",
			E_USER_NOTICE => "User Notice",
			E_STRICT => "Runtime Notice",
			E_RECOVERABLE_ERROR => "Recoverable Error",
			E_DEPRECATED => "Deprecated Error"
		);

		$url = CSX_Server::getRequest()->getRequestUri();

		$msg = "[cosyx|" . $url . "] PHP " . $errorTypes[$errno] . ": " . strip_tags($errstr) . " in " . $errfile . " on line " . $errline;
		self::log($msg);
		ShowError($msg);
	}

	/**
	 * PHP uncatched exception handler
	 *
	 * @param Exception $ex
	 */
	public static function exceptionHandler($ex) {
		$url = CSX_Server::getRequest()->getRequestUri();

		$msg = "at [cosyx|" . $url . "]\nPHP uncatched exception: " . (string)$ex;
		self::log($msg);
		ShowError($msg);
	}
}