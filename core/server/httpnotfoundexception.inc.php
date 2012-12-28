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
class CSX_Server_HttpNotFoundException extends CSX_Exception
{
	public function __construct()
	{
		parent::__construct('404 Not found', CSX_Server_HttpResponse::SC_NOT_FOUND);
	}
}