<?php

namespace Skvn\Cache;

class ArrayStorage extends Storage
{
    protected $storage = [];

    function get($key)
    {
        return $this->storage[$this->getKey($key)] ?? null;
    }

    function set($key, $value, $ttl = 0)
    {
        $this->storage[$this->getKey($key)] = $value;
        return true;
    }

    function add($key, $value, $ttl = 0)
    {
        $this->storage[$this->getKey($key)] = $value;
        return true;
    }

    function delete($key)
    {
        unset($this->storage[$this->getKey($key)]);
        return true;
    }

    function flush()
    {
        $this->storage = [];
        return true;
    }

    function keys($pattern = null, $full = false)
    {
        return array_keys($this->storage);
    }

}