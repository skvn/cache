<?php

namespace Skvn\Cache;

class SingleGetDecorator extends StorageDecorator
{

    protected $storage = [];

    function get($key)
    {
        if (!array_key_exists($key, $this->storage)) {
            $this->storage[$key] = $this->cache->get($key);
        }
        return $this->storage[$key];
    }

    function set($key, $value, $ttl = 0)
    {
        $set = $this->cache->set($key, $value, $ttl);
        if ($set !== false) {
            $this->storage[$key] = $value;
        }
        return $set;
    }

    function add($key, $value, $ttl = 0)
    {
        $add = $this->cache->add($key, $value, $ttl);
        if ($add !== false) {
            $this->storage[$key] = $value;
        }
        return $add;
    }

    function delete($key)
    {
        unset($this->storage[$key]);
        return $this->cache->delete($key);
    }
    
    function increment($key, $value = 1)
    {
        return $this->cache->increment($key, $value);
    }


}