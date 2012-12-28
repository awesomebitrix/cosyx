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
abstract class CSX_Server_Session extends CSX_Hash
{
	abstract function start($sessionId = null);

	abstract function getId();
}
