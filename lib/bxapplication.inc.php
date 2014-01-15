<?php
/**
 * Cosix Bitrix Extender
 *
 * @package lib
 * @version $Id$
 * @author Peredelskiy Aleksey <info@web-n-roll.ru>
 */

/**
 *
 * @package lib
 */
class CSX_BXApplication extends CSX_ProxyObject
{
    public function AddHeadStylesheet($src)
    {
        $this->object->AddHeadString("<link href=\"{$src}\" type=\"text/css\" rel=\"stylesheet\" />", true);
    }

    public static function isPageMain()
    {
        global $APPLICATION;

        return $APPLICATION->GetCurPage(true) == SITE_DIR . 'index.php';
    }

    public static function Register404Handler() {
        AddEventHandler('main', 'OnEpilog', array(CSX_BXApplication, 'Redirect404'));
    }

    public static function SetStatus404()
    {
        CHTTP::SetStatus("404 Not Found");
        @define("ERROR_404", "Y");
    }

    public static function Redirect404() {
        if (
            !defined('ADMIN_SECTION') &&
            defined("ERROR_404") &&
            file_exists($_SERVER["DOCUMENT_ROOT"] . '/404.php')
        ) {
            global $APPLICATION;
            $APPLICATION->RestartBuffer();
            CHTTP::SetStatus("404 Not Found");
            include($_SERVER["DOCUMENT_ROOT"] . SITE_TEMPLATE_PATH . "/header.php");
            include($_SERVER["DOCUMENT_ROOT"] . '/404.php');
            include($_SERVER["DOCUMENT_ROOT"] . SITE_TEMPLATE_PATH . "/footer.php");
        }
    }
}