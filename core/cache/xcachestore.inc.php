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
class CSX_Cache_XCacheStore extends CSX_Cache_Store
{
    function __construct($params = array())
    {
        if (!function_exists('xcache_get')) {
            throw new CSX_Exception('Looks like no XCache extension is installed');
        }

        parent::__construct($params);
    }

    protected function store($key, $data, $compression = 0, $ttl = null)
    {
        xcache_set($key, $this->compress(serialize($data), $compression), $ttl);
    }

    protected function fetch($key)
    {
        return unserialize($this->uncompress(xcache_get($key)));
    }

    protected function remove($key)
    {
        xcache_unset($key);
    }

    function flush()
    {
        xcache_unset_by_prefix($this->getPrefix());
    }
}
