Cosyx Bitrix Extender
---------------------

Installation

1. Copy 'cosyx' folder into root folder of bitrix project
2. Move 'cosyx/install/cosyx.app' folder into root folder of bitrix project
3. Remove 'cosyx/install' folder
4. Add following line to the top of /bitrix/php_interface/init.php:

	include_once( $_SERVER["DOCUMENT_ROOT"] . "/cosyx/init.inc.php");

Have fun!