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
class CSX_SingletonWidget extends CSX_Widget
{
	static $inited = array();

	protected function isInited()
	{
		$obj = new ReflectionObject($this);
		$class = $obj->getName();
		return array_key_exists($class, self::$inited);
	}

	protected function init()
	{
		if (!$this->isInited()) {
			foreach ($this->children as $id => $child) {
				$child->init();
			}
		}
	}

	protected function prepare()
	{
		return array();
	}

	protected function display()
	{
		if (!$this->isInited()) {
			foreach ($this->children as $id => $child) {
				$child->display();
			}

			$GLOBALS['DATA'] = $this->prepare();
			$GLOBALS['PARAMS'] = $this->params->getHashRef();

			$template = $this->getDir('templates') . '/index.php';
			if (file_exists($template)) {
				include($template);
			}
		}
	}

	protected function destruct() {
		parent::destruct();

		if (!$this->isInited()) {
			//	set inited flag
			$obj = new ReflectionObject($this);
			$class = $obj->getName();
			self::$inited[$class] = true;
		}
	}

	public function getRef($className = null)
	{
		if ($className == null) {
			$className = get_class($this);
		}
		return implode('::', explode('_', $className, -1));
	}

	public function getDirUrl($dir, $className = null)
	{
		$path = explode('::', $this->getRef($className));

		for ($i = 0; $i < count($path); $i++) {
			$path[$i] = strtolower($path[$i]);
		}

		array_push($path, $dir);
		if ('csx' == $path[0]) {
			$path[0] = CSX_COSYX_URL . '/lib';
			$r[] = $path;
		}
		else {
			array_unshift($path, CSX_APP_URL . '/widget');
			$r[] = $path;

			$ext_path = $path;
			array_shift($ext_path);
			array_unshift($ext_path, CSX_EXT_URL);
			$r[] = $ext_path;
		}

		foreach ($r as $p) {
			if (file_exists(CSX_SERVER_ROOT_DIR . '/' . ($f = implode('/', $p)))) {
				return $f;
			}
		}

		return null;
	}

	/**
	 *
	 */
	public function getPubUrl($className = null)
	{
		return $this->getDirUrl('pub', $className);
	}

	/**
	 *
	 */
	public function getPubDir($className = null)
	{
		return $this->getDir('pub', $className);
	}

	/**
	 *
	 */
	public function getDir($dir, $className = null)
	{
		return PSP_SERVER_ROOT_DIR . $this->getDirUrl($dir, $className);
	}
}