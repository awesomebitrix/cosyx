<?php
/**
 * Cosyx Bitrix Extender Project
 *
 * @package cache
 * @version $Id$
 * @author Peredelskiy Aleksey <info@web-n-roll.ru>
 */

/**
 * eAccelerator library based cache store
 *
 * @package cache
 */
class CSX_Cache_eAcceleratorStore extends CSX_Cache_Store
{
    function __construct($params = array())
    {
        if (!function_exists('eaccelerator_info')) {
            throw new CSX_Exception('Looks like no eAccelerator extension is installed');
        }

        parent::__construct($params);
    }

    protected function store($key, $data, $compression = 0, $ttl = null)
    {
        eaccelerator_put($key, $this->compress(serialize($data), $compression), $ttl);
    }

    protected function fetch($key)
    {
        return unserialize($this->uncompress(eaccelerator_get($key)));
    }

    protected function remove($key)
    {
        eaccelerator_rm($key);
    }

    function flush()
    {
		if ($this->prefix!='') {
			$keys = eaccelerator_list_keys();
			foreach ($keys as $key) {
				if (strpos($key, $this->prefix)!==false) {
					$this->remove($key);
				}
			}
		}
		else {
        	eaccelerator_clean();
		}
    }
}
