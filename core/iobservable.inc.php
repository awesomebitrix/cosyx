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
interface CSX_IObservable
{
	public function addEvent($name);

	public function attachEvent($name, $handler);

	public function fireEvent($name, $args = null);
}