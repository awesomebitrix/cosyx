<?php
/**
 * PrimalSite CMS Project
 *
 * @package cache
 * @version $Id$
 * @author Peredelskiy Aleksey <casbah@yandex.ru>
 */
/**
 * Cache store stub (do nothing)
 *
 * @package cache
 */
class CSX_Cache_StubStore extends CSX_Cache_Store
{
    protected function store($key, $data, $compression = 0, $ttl = null)
    {
    }

    protected function fetch($key)
    {
        return false;
    }

    protected function remove($key)
    {
    }

    function flush()
    {
    }
}