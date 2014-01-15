<?php
/**
 * Cosyx Bitrix Extender Project
 *
 * @package cache
 * @version $Id$
 * @author Peredelskiy Aleksey <info@web-n-roll.ru>
 */

/** 
 * Cache store based on memcache library
 *
 * @package cache
 */
class CSX_Cache_MemcacheStore extends CSX_Cache_Store
{
    protected $memcache;

    function __construct($params = array())
    {
        parent::__construct($params);

        if (isset($params['memcache']) && $params['memcache'] instanceof Memcache) {
            $this->memcache = $memcache;
        } else {
            $host = empty($params['host']) ? 'localhost' : $params['host'];
            $port = empty($params['port']) ? 11211 : $params['port'];
            $timeout = empty($params['timeout']) ? 1 : $params['timeout'];

            $this->memcache = new Memcache();
            $this->memcache->connect($host, $port, $timeout);
        }
    }

    protected function store($key, $data, $compression = 0, $ttl = null)
    {
        $this->memcache->set($key, $data, $compression ? MEMCACHE_COMPRESSED : null, $ttl);
    }

    protected function fetch($key)
    {
        return $this->memcache->get($key);
    }

    protected function remove($key)
    {
        $this->memcache->delete($key);
    }

    function flush()
    {
        $r = $this->memcache->flush();
    }

    function getMemcache()
    {
        return $this->memcache;
    }
}
