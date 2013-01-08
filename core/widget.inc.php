<?php
class CSX_Widget {
	protected $id = null;
	public $params = null;

	protected $application = null;

	public function __construct($id, $params = array()) {
		global $APPLICATION;
		$this->params = new CSX_Hash($params);
		$this->application = $APPLICATION;
	}

	public static function includeWidget($cls, $params = array()) {
		$class = CSX_Compat::resolveClassName($cls).'_Widget';

		if ($id==null) {
			$id = uniqid();
		}

		$cls = new ReflectionClass($class);
		
		$obj = $cls->newInstance($id, $wparams);

		$obj->init();
		$obj->display();
		
		return $obj;
	}

	protected function init() {
	}

	protected function prepare() {
		return array();
	}

	protected function display() {
		$GLOBALS['DATA'] = $this->prepare();
		$template = $this->getDir('templates') . '/index.php';
		if (file_exists($template)) {
			include($template);
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
		return PSP_SERVER_ROOT_DIR . $this->getDirUrl($dir, $className);
	}
}