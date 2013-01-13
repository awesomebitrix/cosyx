<?php
/**
 * Cosyx Bitrix Extender Project
 *
 * @package cache
 * @version $Id$
 * @author Peredelskiy Aleksey <info@web-n-roll.ru>
 */

/**
 * APC library based cache store
 *
 * @package cache
 */
class CSX_Cache_ApcStore extends CSX_Cache_Store
{
    function __construct($params = array())
    {
        if (!function_exists('apc_store')) {
            throw new CSX_Exception('Looks like no APC extension is installed');
        }

        parent::__construct($params);
    }

    protected function store($key, $data, $compression = 0, $ttl = null)
    {
        apc_store($key, $this->compress(serialize($data), $compression), $ttl);
    }

    protected function fetch($key)
    {
        return unserialize($this->uncompress(apc_fetch($key)));
    }

    protected function remove($key)
    {
        apc_delete($key);
    }

    function flush()
    {
        apc_clear_cache('user');
    }
}
