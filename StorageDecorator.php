<?php

namespace Skvn\Cache;

abstract class StorageDecorator extends Storage
{

    protected $cache;


    protected function init()
    {
        if (empty($this->config['cache']) || !($this->config['cache'] instanceof Storage)) {
            throw new Exceptions\CacheException('Unable to decorate cache storage');
        }
        $this->cache = $this->config['cache'];
    }

    function get($key)
    {
        return $this->cache->get($key);
    }

    function set($key, $value, $ttl = 0)
    {
        return $this->cache->set($key, $value, $ttl);
    }

    function add($key, $value, $ttl = 0)
    {
        return $this->cache->add($key, $value, $ttl);
    }

    function delete($key)
    {
        return $this->cache->delete($key);
    }

    function flush()
    {
        return $this->cache->flush();
    }

    function keys($pattern = null, $full = false)
    {
        return $this->cache->keys($pattern , $full);
    }
}