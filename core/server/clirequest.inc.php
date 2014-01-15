<?php
/**
 * Cosix Bitrix Extender
 *
 * @package core
 * @version $Id$
 * @author Peredelskiy Aleksey <info@web-n-roll.ru>
 */

/**
 * @package core
 */
class CSX_Server_CliRequest extends CSX_Server_Request
{
	function __construct()
	{
		$data = array();
		parent::__construct($data);
	}
}
