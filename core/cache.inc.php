<?php
/**
 * Cosix Bitrix Extender
 *
 * @package cache
 * @version $Id$
 * @author Peredelskiy Aleksey <info@web-n-roll.ru>
 */

/**
 * Singletone cache store register
 * Provides access to a set of cache store objects
 *
 * @package cache
 */
class CSX_Cache
{
	public $stores = array();

	/**
	 * Get singletone instance
	 *
	 * @static
	 * @access public
	 * @return void
	 */
	static public function getInstance()
	{
		static $instance;
		if (null === $instance) {
			$instance = new CSX_Cache();
		}
		return $instance;
	}

	static public function registerStore($name, $store, $params = null)
	{
		$factory = new CSX_Factory($store, $params);
		CSX_Config::set('cache', $name, $factory);
	}

	static public function unregisterStore($name)
	{
		CSX_Config::set('cache', $name, null);
	}

	/**
	 * Return cache store object
	 * If store not exists it returns stub object of class StubStore
	 * or throws exception (depends on second argument)
	 *
	 * @param string $name
	 * @param boolean $emulate
	 * @static
	 * @access public
	 * @return void
	 */
	static public function getStore($name = null, $emulate = true, $params = null)
	{
		if (null === $name) {
			$name = 'default';
		}

		if (($agent = CSX_Config::get('cache', $name)) !== null || $name === 'default') {
			$defaultClassName = (!CSX_CACHE_DISABLE_APC && function_exists('apc_store')) ? 'CSX::Cache::ApcStore' : 'CSX::Cache::FileStore';

			if ($name === 'default' && $agent === null) {
				$agent = array(
					'prefix' => CSX_CACHE_PREFIX,
					'path' => CSX_CACHE_DIR
				);
			}
			return CSX_Factory::resolve($agent, $defaultClassName)->construct();
		}
		elseif ($emulate) {
			$factory = new CSX_Factory('CSX::Cache::StubStore', $params);
			return $factory->construct();
		}
		else {
			throw new CSX_Exception('cache agent [' . $name . '] not found');
		}
	}
}