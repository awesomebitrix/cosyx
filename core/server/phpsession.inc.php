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
class CSX_Server_PhpSession extends CSX_Server_Session
{
	function start($sessionId = null)
	{
		if (null !== $sessionId) {
			session_id($sessionId);
		}
		if (!strlen($this->getId())) session_start();
		$this->setHashRef($_SESSION);
	}

	function getId()
	{
		return session_id();
	}
}
