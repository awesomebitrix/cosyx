<?php
/**
 * PrimalSite CMS Project
 *
 * @package ext
 * @version $Id$
 * @author  Aleksey Sidorov <cbih315@gmail.com>
 */
require_once dirname(__FILE__) . '/phpexcel/wrapper.php';

/**
 *	PHPExcel wrapper class
 *
 *	@package ext
 */
class PHPExcel_Wrapper {
	public static function getInstance($filename) {
		return new PHPExcelWrapper($filename);
	}
}