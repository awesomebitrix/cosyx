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
class CSX_Widget {
	protected $id = null;
	protected $parent = null;
	public $children = array();
	public $params = null;

	protected $application = null;

	public $isInitialized = false;
	public $isRendered = false;

	public function __construct($id, $parent, $params = array()) {
		global $APPLICATION;
		$this->parent = $parent;
		$this->params = new CSX_Hash($params);
		$this->application = new CSX_BXApplication($APPLICATION);
	}

	public static function createWidget($cls, $parent, $params = array(), $id = null) {
		$class = CSX_Compat::resolveClassName($cls).'_Widget';

		if ($id==null) {
			$id = uniqid();
		}

		$cls = new ReflectionClass($class);
		
		$obj = $cls->newInstance($id, $parent, $params);

		if ($parent) {
			$parent->children[$id] = $obj;
		}

		return $obj;
	}

	public static function includeWidget($cls, $parent = null, $params = array()) {
		$obj = self::createWidget($cls, $parent, $params);

		$obj->preinit();

		if (!$obj->isInitialized) {
			$obj->init();
			$obj->isInitialized = true;
		}

		if (!$obj->isRendered) {
			$obj->display();
			$obj->isRendered = true;
		}

		return $obj;
	}

	protected function preinit() {
	}

	protected function init() {
		foreach ($this->children as $id => $child) {
			if (!$child->isInitialized) {
				$child->init();
				$child->isInitialized = true;
			}
		}
	}

	protected function prepare() {
		return array();
	}

	protected function display() {
		foreach ($this->children as $id => $child) {
			if (!$child->isDisplayed) {
				$child->display();
				$child->isDisplayed = true;
			}
		}

		$GLOBALS['DATA'] = $GLOBALS['DATA'] ? array_merge($GLOBALS['DATA'], $this->prepare()) : $this->prepare();
		$GLOBALS['PARAMS'] = $GLOBALS['PARAMS'] ? array_merge($GLOBALS['PARAMS'], $this->params->getHashRef()) : $this->params->getHashRef();

		$dir = $this->getDir('templates');
		if ($dir) {
			$template = $dir . '/index.php';
			if (file_exists($template)) {
				global $DATA, $PARAMS;
				include($template);
			}
		}
	}

	public function getRef($className = null) {
		if ($className==null) { $className = get_class($this); }
		return implode('::', explode('_', $className, -1));
	}

	public function getDirUrl($dir, $className = null) {
		$path = explode('::', $this->getRef($className));

		for ($i=0;$i<count($path);$i++) {
			$path[$i] = strtolower($path[$i]);
		}

		array_push($path, $dir);
		if ('csx' == $path[0]) {
			$path[0] = CSX_COSYX_URL . '/lib';
			$r[] = $path;
		} else {
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
	public function getPubUrl($className = null) {
		return $this->getDirUrl('pub', $className);
	}

	/**
	 *
	 */
	public function getPubDir($className = null) {
		return $this->getDir('pub', $className);
	}

	/**
	 *
	 */
	public function getDir($dir, $className = null) {
		$dir = $this->getDirUrl($dir, $className);
		if ($dir) {
			return CSX_SERVER_ROOT_DIR . $dir;
		}
		else {
			return null;
		}
	}

	/**
	 *
	 */
	public function getParam($key, $default = null) {
		if ($this->params->has($key)) {
			return $this->params->get($key);
		}
		else {
			return $default;
		}
	}

	/**
	 *
	 */
	public function setParam($key, $value) {
		$this->params->set($key, $value);
	}
}