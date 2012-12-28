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
class CSX_Server_HttpForbiddenException extends CSX_Exception
{
	public function __construct()
	{
		parent::__construct('403 Forbidden', CSX_Server_HttpResponse::SC_FORBIDDEN);
	}
}