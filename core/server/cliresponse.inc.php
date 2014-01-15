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
class CSX_Server_CliResponse extends CSX_Server_Response
{
	protected function sendResponse()
	{
		echo $this->getContent();
	}
}
