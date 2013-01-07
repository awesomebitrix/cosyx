<?php
/**
 * Cosyx Bitrix Extender Project
 *
 * @package cosyx
 * @version $Id$
 * @author Peredelskiy Aleksey <info@web-n-roll.ru>
 */

$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];

define('NO_KEEP_STATISTIC', true);
define('NOT_CHECK_PERMISSIONS', true);
define('NO_AGENT_STATISTIC', true);
define('NO_AGENT_CHECK', true);

require($DOCUMENT_ROOT . "/bitrix/modules/main/include/mainpage.php");

$siteId = CMainPage::GetSiteByAcceptLanguage();
define('SITE_ID', $siteId);
define('LANG', SITE_ID);

require($DOCUMENT_ROOT . "/bitrix/modules/main/include/prolog_before.php");