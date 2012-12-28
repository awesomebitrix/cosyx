<?php
/**
 * PrimalSite CMS Project
 *
 * @package cache
 * @version $Id$
 * @author Peredelskiy Aleksey <casbah@yandex.ru>
 */
/**
 * Filesystem cache store
 *
 * @package cache
 */
class CSX_Cache_FileStore extends CSX_Cache_Store
{
    protected $path;
    private $local = array();
    private $expiredTime = null;

    function __construct($params = array())
    {
        if (empty($params['path'])) {
            throw new CSX_Exception('Path is not set for File store cache');
        }

        $path = $params['path'];

        parent::__construct($params);
        $this->path = $path;
    }

    protected function store($key, $data, $compression = 0, $ttl = null)
    {
        //$this->local[$key] = $data;
        $filename = $this->getFileName($key);
        $h = fopen($filename, 'a+');
        if (!$h) {
            throw new Exception('Could not write to cache');
        }
        flock($h, LOCK_EX);
        fseek($h, 0);
        ftruncate($h, 0);
        $expires = $ttl > 0 ? time() + $ttl : null;
        $data = serialize(array($expires, $this->compress($data, $compression)));
        if (false === fwrite($h, $data)) {
            throw new Exception('Could not write to cache');
        }
        fclose($h);
    }

    protected function fetch($key)
    {
        /*
        if (isset($this->local[$key])) {
            return $this->local[$key];
        }
        */
        $filename = $this->getFileName($key);
        if (!file_exists($filename)) {
            return false;
        }
//        if ($this->getExpiredTime() > filemtime($filename)) {
//            unlink($filename);
//            return false;
//        }
        if (!($h = fopen($filename, 'r'))) {
            return false;
        }
        flock($h, LOCK_SH);
        $data = file_get_contents($filename);
        fclose($h);
        if (false == ($data = @unserialize($data))) {
            unlink($filename);
            return false;
        }

        if (null !== $data[0] && time() >= $data[0]) {
            unlink($filename);
            return false;
        }

        $data = $this->uncompress($data[1]);
        if (false === $data) {
            unlink($filename);
            return false;
        }

        return $data;
    }

    protected function remove($key)
    {
        $filename = $this->getFileName($key);
        if (file_exists($filename)) {
            unlink($filename);
        }
    }

    function flush()
    {
        touch($this->getExpiredTimeFileName());
    }

    private function getExpiredTimeFileName()
    {
        return $this->getFileName($this->getPrefix() . '__expired__');
    }

    private function getExpiredTime()
    {
        if (null === $this->expiredTime) {
            $filename = $this->getExpiredTimeFileName();
            $this->expiredTime = file_exists($filename) ? (int)filemtime($filename) : 0;
        }
        return $this->expiredTime;
    }

    private function getFileName($key)
    {
        return $this->path . '/' . strtr($key, '/?', '__');
	//str_replace('/', '__', $key);
    }
}
