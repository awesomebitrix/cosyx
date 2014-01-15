<?php
/**
 * Cosyx Bitrix Extender Project
 *
 * @package controller
 * @version $Id$
 * @author Peredelskiy Aleksey <info@web-n-roll.ru>
 */

/**
 *
 *
 * @package controller
 */
class CSX_Controller_Rewrite extends PSP_Controller {
	public function run($params) {
		$uri = PSP_Server::getRequest()->getRequestUri();

		if (!array_key_exists('redirect', $params)) {
			throw new PSP_Exception('No [redirect] parameter for CSX::Controller::Rewrite');
		}

		$redirect = $params['redirect'];
		$pattern = $params['pattern'];
		
		$csx_root_url = str_replace('/', '\/', CSX_ROOT_URL);

		if ($pattern[0]=='^') {
			$pattern = '^' . $csx_root_url . PSP_String::substr($pattern, 1);
		}
		else {
			$pattern = $csx_root_url . $pattern;
		}

		$uri = parse_url($uri);
		$redirect_uri = preg_replace('/'.$pattern.'/i', $redirect, $uri['path']);
		
		if (array_key_exists('query', $uri)) {
			$temp = parse_url($redirect_uri);
			if (array_key_exists('query', $temp)) {
				$redirect_uri .= '&'.$uri['query'];
			}
			else {
				$redirect_uri .= '?'.$uri['query'];
			}
		}
		
		//	rewrite
		$uri = parse_url($redirect_uri);

		$_SERVER['ORIGINAL_QUERY_STRING'] = $_SERVER['QUERY_STRING'];
		$_SERVER['ORIGINAL_SCRIPT_NAME'] = $_SERVER['SCRIPT_NAME'];
		$_SERVER['ORIGINAL_SCRIPT_FILENAME'] = $_SERVER['SCRIPT_FILENAME'];
		$_SERVER['ORIGINAL_URL'] = PSP_Server::getRequest()->getRequestUri();
		$_SERVER['ORIGINAL_PHP_SELF'] = $_SERVER['PHP_SELF'];

		$_SERVER['QUERY_STRING'] = array_key_exists('query', $uri) ? $uri['query'] : '';
		$_SERVER['SCRIPT_NAME'] = $uri['path'];
		$_SERVER['SCRIPT_FILENAME'] = PSP_ROOT_DIR.$uri['path'];
		$_SERVER['REQUEST_URI'] = $uri['path'];
		$_SERVER['PHP_SELF'] = $uri['path'];
		
		if (array_key_exists('query', $uri)) {
			parse_str($uri['query'], $qs);
			foreach ($qs as $key => $value) {
				$_GET[$key] = $value;
				$_REQUEST[$key] = $value;			
				PSP_Server::getRequest()->set($key, $value);
			}
		}

		if (array_key_exists('include_script', $params)) {
			include($_SERVER['SCRIPT_FILENAME']);
			return true;
		}
		else {
			return false;
		}
	}
}