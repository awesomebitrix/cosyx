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
class CSX_Controller_Redirect extends PSP_Controller {
	public function run($params) {
		$uri = PSP_Server::getRequest()->getRequestUri();

		$redirect = $params['redirect'];
		$pattern = $params['pattern'];

		$csx_root_url = str_replace('/', '\/', CSX_ROOT_URL);

		if ($pattern[0]=='^') {
			$pattern = '^' . $csx_root_url . PSP_String::substr($pattern, 1);
		}
		else {
			$pattern = $csx_root_url . $pattern;
		}

		$redirect_uri = preg_replace('/'.$pattern.'/i', $redirect, $uri);

		PSP_Server::getResponse()->setStatus(PSP_Server_HttpResponse::SC_MOVED_PERMANENTLY);
		PSP_Server::getResponse()->sendRedirect($redirect_uri);

		return true;
	}
}