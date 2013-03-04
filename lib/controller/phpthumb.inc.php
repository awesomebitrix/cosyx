<?php
/**
 * Cosyx Bitrix Extender Project
 *
 * @package controller
 * @version $Id$
 * @author Peredelskiy Aleksey <info@web-n-roll.ru>
 */

/**
 * phpThumb image processing engine controller
 *
 * @package controller
 */
class CSX_Controller_PhpThumb extends CSX_Controller_Param {
	public function run($params) {
		parent::run($params);

		$src = $this->parameters['src'];

		$src = str_replace("%20", " ", $src);

		$thumbScript = '/thumb/phpThumb.php';

		$ruleParams = CSX_Config::get('thumb', $this->parameters['rule']);
		if (!$ruleParams) {
			throw new CSX_Server_HttpNotFoundException();
		}

		$emptySrc = array_key_exists('empty', $ruleParams) ? $ruleParams['empty'] : null;

		if (strstr($src, 'http://')!==false) {
			$ruleParams['src'] = $src;
			unset($ruleParams['empty']);
		}
		else {
			$ppath = CSX_ROOT_DIR . '/' . $src;

			if ($ruleParams!=null && !array_key_exists('vpath', $ruleParams)) {
				$ruleParams['src'] = '/' . $src;
			}
			else {
				$ruleParams = array( 'src' => '' );
			}

			if (array_key_exists('empty', $ruleParams)) {
				if (!file_exists($ppath) || !is_file($ppath)) {
					$ruleParams['src'] = $ruleParams['empty'];
				}
				unset($ruleParams['empty']);
			}
		}

		$_SERVER['SCRIPT_NAME'] = CSX_COSYX_URL . $thumbScript;
		$_SERVER['SCRIPT_FILENAME'] = CSX_COSYX_DIR . $thumbScript;
		$_SERVER['PHP_SELF'] = $thumbScript;
		
		$qs = array();
		foreach ($ruleParams as $key => $value) {
			if (is_numeric($key[strlen($key)-1])) {
				$key0 = substr($key, 0, strlen($key) - 1);
				$qs[] = $key0 . '[]=' . $value;

				if (!isset($_GET[$key0])) {
					$_GET[$key0] = array();
					$_REQUEST[$key0] = array();
				}

				$_GET[$key0][] = $value;
				$_REQUEST[$key0][] = $value;
			}
			else {
				$_GET[$key] = $value;
				$_REQUEST[$key] = $value;
				$qs[] = $key . '=' . $value;
			}

			CSX_Server::getRequest()->set($key, $value);
		}
	
		$_SERVER['QUERY_STRING'] = implode('&', $qs);

		global $phpThumb;
		global $PHPTHUMB_CONFIG;

		$PHPTHUMB_CONFIG['document_root'] = CSX_ROOT_DIR;
		$PHPTHUMB_CONFIG['cache_directory'] = CSX_PHPTHUMB_CACHE_DIR . '/';
		$PHPTHUMB_CONFIG['disable_debug'] = !CSX_DEBUG;

		if ($emptySrc) {
			$PHPTHUMB_CONFIG['error_message_image_default'] = $emptySrc;
		}

		restore_error_handler();
		restore_exception_handler();
		
		include($_SERVER['SCRIPT_FILENAME']);

		return true;
	}
}