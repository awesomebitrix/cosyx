<?php
/**
 * Cosyx Bitrix Extender
 *
 * @package cosyx
 * @author Peredelskiy Aleksey <info@web-n-roll.ru>
 * @version $Id$
 */
define('CSX_REVISION', '0');
define('CSX_DEBUG', false);

if (!defined('CSX_STARTUP_TIME')) define('CSX_STARTUP_TIME', microtime(true));
if (!defined('CSX_ROOT_DIR')) define('CSX_ROOT_DIR', realpath(dirname(__FILE__) . '/..'));

if (!defined('CSX_ROOT_URL') && isset($_SERVER['DOCUMENT_ROOT']) && $_SERVER['DOCUMENT_ROOT'] != '' && file_exists($_SERVER['DOCUMENT_ROOT'] . $_SERVER['PHP_SELF'])) {
	$sdr = __init_normalize($_SERVER['DOCUMENT_ROOT']);
	$rd = __init_normalize(CSX_ROOT_DIR);

	define('CSX_SERVER_ROOT_DIR', $sdr);

	if ($sdr != $rd) {
		$path1 = explode('/', __init_normalize($sdr));
		$path2 = explode('/', __init_normalize($rd));

		define('CSX_ROOT_URL', '/' . implode('/', array_slice($path2, count($path1))));
	}
	else {
		define('CSX_ROOT_URL', '');
	}
}
else {
	if (!defined('CSX_ROOT_URL')) define('CSX_ROOT_URL', '');
	define('CSX_SERVER_ROOT_DIR', CSX_ROOT_DIR);
}

if (!defined('CSX_COSYX_DIR')) define('CSX_COSYX_DIR', dirname(__FILE__));
if (!defined('CSX_COSYX_URL')) define('CSX_COSYX_URL', CSX_ROOT_URL . '/primal');
if (!defined('CSX_APP_DIR')) define('CSX_APP_DIR', CSX_ROOT_DIR . '/cosyx.app');
if (!defined('CSX_APP_URL')) define('CSX_APP_URL', CSX_ROOT_URL . '/cosyx.app');
if (!defined('CSX_CONF_DIR')) define('CSX_CONF_DIR', CSX_APP_DIR . '/conf');
if (!defined('CSX_COSYX_CONF_DIR')) define('CSX_COSYX_CONF_DIR', CSX_COSYX_DIR . '/conf');
if (!defined('CSX_VAR_DIR')) define('CSX_VAR_DIR', CSX_APP_DIR . '/var');
if (!defined('CSX_VAR_URL')) define('CSX_VAR_URL', CSX_APP_URL . '/var');
if (!defined('CSX_LOG_DIR')) define('CSX_LOG_DIR', CSX_VAR_DIR . '/log');
if (!defined('CSX_CACHE_DIR')) define('CSX_CACHE_DIR', CSX_VAR_DIR . '/cache');
if (!defined('CSX_CACHE_DISABLE_APC')) define('CSX_CACHE_DISABLE_APC', false);
if (!defined('CSX_CACHE_PREFIX')) define('CSX_CACHE_PREFIX', 'csx_');
if (!defined('CSX_CONFIG')) define('CSX_CONFIG', CSX_CONF_DIR . '/config.xml');
if (!defined('CSX_COSYX_CONFIG')) define('CSX_COSYX_CONFIG', CSX_COSYX_CONF_DIR . '/config.xml');
if (!defined('CSX_TEMP_DIR')) define('CSX_TEMP_DIR', CSX_VAR_DIR . '/tmp');
if (!defined('CSX_TEMP_URL')) define('CSX_TEMP_URL', CSX_VAR_URL . '/tmp');
if (!defined('CSX_BUILD_CLASSES')) define('CSX_BUILD_CLASSES', false);
if (!defined('CSX_EXT_DIR')) define('CSX_EXT_DIR', CSX_ROOT_DIR . '/cosyx.ext');
if (!defined('CSX_EXT_URL')) define('CSX_EXT_URL', CSX_ROOT_URL . '/cosyx.ext');
if (!defined('CSX_PHPTHUMB_CACHE_DIR')) define('CSX_PHPTHUMB_CACHE_DIR', CSX_ROOT_DIR . '/bitrix/cache/phpthumb');
if (!defined('E_DEPRECATED')) define('E_DEPRECATED', -111);

require_once(dirname(__FILE__) . '/core/classloader.inc.php');

CSX_ClassLoader::registerPath(dirname(__FILE__) . '/core', 'CSX');
CSX_ClassLoader::registerPath(dirname(__FILE__) . '/lib', 'CSX');
CSX_ClassLoader::registerPath(CSX_APP_DIR . '/class');
CSX_ClassLoader::registerPath(CSX_APP_DIR . '/widget');
CSX_ClassLoader::registerPath(CSX_APP_DIR . '/controller', 'Controller');
CSX_ClassLoader::registerPath(CSX_APP_DIR . '/model', 'Model');
CSX_ClassLoader::registerPath(CSX_EXT_DIR);

CSX_ClassLoader::enableAutoload();

if (file_exists(CSX_APP_DIR . '/init.inc.php')) {
	include CSX_APP_DIR . '/init.inc.php';
}

CSX_Config::load();

set_error_handler(array('CSX_Debug', 'errorHandler'), E_ALL ^ E_NOTICE ^ E_USER_NOTICE ^ E_WARNING);
set_exception_handler(array('CSX_Debug', 'exceptionHandler'));

//	request handling
$table = CSX_Config::get('routetable');
if ($table) {
	foreach ($table as $k => $v) {
		CSX_Server::getRouteTable()->addRoute($k, $v);
	}
}

CSX_Server::getRouteTable()->addRoute('mvc', array(
	'pattern' => '^\/([^\/\?]+)\/?([^\/\?]+)?\/?([^\/\?]+)?\/?([^\/\?]+)?\/?([^\/\?]+)?\/?([^\/\?]+)?'
, 'controller' => "CSX::Mvc::Controller"
));

if (CSX_Server::getRequest()->get('clear_cache')=='Y') {
	CSX_Cache::getStore()->flush();
}

require_once(dirname(__FILE__) . '/ext/ib_property_checkbox.inc.php');
require_once(dirname(__FILE__) . '/ext/uf_html_element.inc.php');
require_once(dirname(__FILE__) . '/ext/uf_iblock.inc.php');

//	handle cosyx requests
CSX_Server::handleRequest();

function __init_normalize($path) {
	$path = preg_replace("/\\\\/", '/', trim($path));
	return $path;
}