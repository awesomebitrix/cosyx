<?php
/**
 * Cosix Bitrix Extender
 *
 * @package core
 * @version $Id$
 * @author Peredelskiy Aleksey <info@web-n-roll.ru>
 */

/**
 * Singletone class loader tool
 * Provides class autoload based on its name
 *
 * @package core
 */
class CSX_ClassLoader
{
	//
	public $pathes = array();

	//
	public $classCache = null;
	public $inlineCache = array();

	/**
	 *
	 */
	public static function getInstance()
	{
		static $instance;
		if (null === $instance) {
			$instance = new CSX_ClassLoader();
		}
		return $instance;
	}

	public function __destruct()
	{
	}

	/**
	 *
	 */
	public static function clearCache()
	{
	}

	/**
	 *
	 */
	public static function enableAutoload()
	{
		spl_autoload_register(array(self::getInstance(), 'loadClass'));
	}

	/**
	 *
	 */
	public static function disableAutoload()
	{
		spl_autoload_unregister(array(self::getInstance(), 'loadClass'));
	}

	/**
	 *
	 */
	public static function registerPath($path, $namespace = null, $priority = 0)
	{
		self::getInstance()->pathes[] = array($path, $namespace, $priority);
	}

	/**
	 *
	 */
	public static function find($className)
	{
		$ns_class_name = strtolower($className);
		$ns_class_name = str_replace('_', '::', $className);
		if (array_key_exists($ns_class_name, self::getInstance()->inlineCache)) {
			if (CSX_DEBUG) CSX_Debug::log('[' . __METHOD__ . '] [' . $ns_class_name . '] folder [' . self::getInstance()->inlineCache[$ns_class_name] . '] from cache');
			return self::getInstance()->inlineCache[$ns_class_name];
		}
		else {
			foreach (self::getInstance()->pathes as $path) {
				if (is_string($path[1])) {
					$ns = $path[1] . '::';
					if (0 === stripos($ns_class_name, $ns)) {
						$class_name = substr($ns_class_name, strlen($ns));
					}
					else {
						continue;
					}
				}
				else {
					$class_name = $ns_class_name;
				}

				$class_name = strtolower($class_name);

				$class_path = str_replace('::', '/', $class_name) . '.inc.php';

				if (is_readable($path[0] . '/' . $class_path)) {
					$resPath = $path[0] . '/' . $class_path;
					self::getInstance()->inlineCache[$ns_class_name] = $resPath;
					return $resPath;
				}
			}

			return false;
		}
	}

	public function loadClass($className)
	{
		if (null === $this->classCache && defined('CSX_BUILD_CLASSES') && CSX_BUILD_CLASSES) {
			$this->classCache = CSX_CACHE_DIR . '/classes.php';
			if (file_exists($this->classCache)) {
				if (is_writeable($this->classCache) && ($f = fopen($this->classCache, 'a'))) {
					if (flock($f, LOCK_SH)) {
						if (defined('CSX_BUILD_CLASSES_TTL') &&
							(filemtime($this->classCache) + CSX_BUILD_CLASSES_TTL) < time()
						) {
							unlink($this->classCache);
						}
						else {
							require_once($this->classCache);
						}
						flock($f, LOCK_UN);
					}
					else {
						$this->classCache = false;
					}

					fclose($f);
				}
				else {
					require_once($this->classCache);
					$this->classCache = false;
				}

				if (class_exists($className, false)) {
					return true;
				}
			}
		}

		if ($file = self::find($className)) {
			require_once($file);

			if ($this->classCache) {
				if ($f = fopen($this->classCache, 'a')) {
					if (flock($f, LOCK_EX)) {
						$cache = file_get_contents($this->classCache);
						if (empty($cache)) {
							$cache = '<' . '?php';
						}

						if (!preg_match('/\s(class|interface)\s+' . $className . '\s/i', $cache)) {
							$data = file_get_contents($file);
							$data = preg_replace('/^\s*<\?(php)?/', '',
								$data);
							$data = preg_replace('/\?>\s*$/', '', $data);
							$data = preg_replace('/__FILE__/', "'" . $file . "'", $data);
							ftruncate($f, 0);
							fwrite($f, $cache . $data);
						}

						flock($f, LOCK_UN);
					}

					fclose($f);
				}
			}
		}

		return class_exists($className, false);
	}
}
