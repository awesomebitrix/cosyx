<?php
/**
 * Cosix Bitrix Extender
 *
 * @package ui
 * @version $Id$
 * @author Peredelskiy Aleksey <info@web-n-roll.ru>
 */

/**
 * @package ui
 */
class CSX_SingletonWidget extends CSX_Widget
{
	static $inited = array();

	protected function isInited()
	{
		$obj = new ReflectionObject($this);
		$class = $obj->getName();
		return array_key_exists($class, self::$inited);
	}

	protected function preinit()
	{
		if (!$this->isInitialized && $this->isInited()) {
			$this->isInitialized = true;
		}

		if (!$this->isDisplayed && $this->isInited()) {
			$this->isDisplayed = true;
		}
	}

	protected function init()
	{
		parent::init();

		//	set inited flag
		$obj = new ReflectionObject($this);
		$class = $obj->getName();
		self::$inited[$class] = true;
	}
}