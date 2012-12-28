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
abstract class CSX_Controller
{
	abstract public function run($params);

	public function validate($params)
	{
		return true;
	}
}