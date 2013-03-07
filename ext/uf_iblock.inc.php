<?
if (!defined('LANG_CHARSET') || LANG_CHARSET!='windows-1251') {
	include(dirname(__FILE__) . '/uf_iblock/utf8/uf_iblock_element.php'); 
	include(dirname(__FILE__) . '/uf_iblock/utf8/uf_iblock_element_list.php');
}
else {
	include(dirname(__FILE__) . '/uf_iblock/windows1251/uf_iblock_element.php'); 
	include(dirname(__FILE__) . '/uf_iblock/windows1251/uf_iblock_element_list.php');
}