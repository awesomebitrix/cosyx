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
class CSX_Config
{
	public $hash;

	public function __construct()
	{
		$this->hash = new CSX_Hash();
	}

	public static function getInstance()
	{
		static $instance;
		if (null === $instance) {
			$instance = new CSX_Config();
		}
		return $instance;
	}

	static public function get()
	{
		$args = func_get_args();
		return call_user_func_array(array(self::getInstance()->hash, 'get'), $args);
	}

	static public function set()
	{
		$args = func_get_args();
		call_user_func_array(array(self::getInstance()->hash, 'set'), $args);
	}

	static public function has()
	{
		$args = func_get_args();
		return call_user_func_array(array(self::getInstance()->hash, 'has'), $args);
	}

	static public function remove()
	{
		$args = func_get_args();
		return call_user_func_array(array(self::getInstance()->hash, 'remove'), $args);
	}

	static public function reload()
	{
		$cache = CSX_Cache::getStore();
		$config_key = 'config_' . md5(CSX_CONFIG);
		$cache->delete($config_key);
		self::load();
	}

	/**
	 *
	 */
	static public function load()
	{
		if (!file_exists(CSX_CONFIG)) {
			return;
		}

		$cache = CSX_Cache::getStore();
		$config_key = 'config_' . md5(CSX_CONFIG);

		if (!is_array($config_hash = $cache->get($config_key, filemtime(CSX_CONFIG)))) {
			$config_hash = CSX_Config::parseConfig(CSX_CMS_CONFIG);
			$config_hash = array_merge_recursive($config_hash, CSX_Config::parseConfig(CSX_CONFIG));

			CSX_Config::set($config_hash);

			$config_hash = CSX_Config::get();

			$cache->set($config_key, $config_hash);
		}
		else {
			CSX_Config::set($config_hash);
		}
	}


	static public function parseConfig($file)
	{
		//	configuration
		$conf = new DOMDocument();
		$conf->load($file);

		$xpath = new DOMXPath($conf);

		$data = array();

		$defaultParser = new CSX_Config_DefaultSectionParser();

		$sections = $xpath->query('/configuration/section');
		for ($i = 0; $i < $sections->length; $i++) {
			$section = $sections->item($i);
			$sectionName = $section->getAttribute('name');
			$parser = $section->getAttribute('parser');

			if ($parser != null) {
				$obj = CSX_Factory::resolve(null, $parser)->construct();
				$sectionData = $obj->parseSection($section);
			}
			else {
				$sectionData = $defaultParser->parseSection($section);
			}

			$data[$sectionName] = $sectionData;
		}

		return $data;
	}
}