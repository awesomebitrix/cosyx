<?php
/**
 * Cosyx Bitrix Extender Project
 *
 * @package cache
 * @version $Id$
 * @author Peredelskiy Aleksey <info@web-n-roll.ru>
 */

/**
 * Base abstract cache store class
 * Implemets base functionality
 * Every concrete cache class must inherit it
 *
 * @package cache
 */
abstract class CSX_Cache_Store implements CSX_IConstructable
{
    protected $prefix = '';

    function __construct($params = array ())
    {
        $prefix = empty($params['prefix']) ? '' : $params['prefix'];

        $this->setPrefix($prefix);
    }

    function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    function getPrefix()
    {
        return $this->prefix;
    }

    final function set($key, $data, $compression = 0, $ttl = null)
    {
        $this->store($this->getPrefix() . $key, array($data, time()), $compression, $ttl);
    }

    final function get($key, $lastModified = null)
    {
        if ($hash = $this->fetch($this->getPrefix() . $key)) {
            list($data, $created) = $hash;
            if (null === $lastModified || $lastModified <= $created) {
                return $data;
            }
        }
        return false;
    }

    final function delete($key)
    {
        return $this->remove($this->getPrefix() . $key);
    }

    abstract function flush();

    protected function compress($data, $compression)
    {
        if ($compression > 0) {
            $data = gzcompress(serialize($data), $compression);
        }
        return array($compression, $data);
    }

    protected function uncompress($compressed)
    {
        list($compression, $data) = $compressed;
        if ($compression > 0) {
            return unserialize(gzuncompress($data));
        }
        return $data;
    }

     /**
     * Put value to cache
     *
     * @param string $key
     * @param mixed $data
     * @param mixed $compression
     * @param mixed $ttl
     * @access public
     * @return void
     */
    abstract protected function store($key, $data, $compression = 0, $ttl = null);

     /**
     * Get value from cache
     *
     * @param string $key
     * @access public
     * @return mixed
     */
    abstract protected function fetch($key);

    abstract protected function remove($key);
}
