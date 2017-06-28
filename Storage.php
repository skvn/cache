<?php

namespace Skvn\Cache;

use Skvn\Base\Traits\ConstructorConfig;

abstract class Storage
{
    use ConstructorConfig;

    protected $keySuffix = null;

    abstract function get($key);
    abstract function set($key, $value, $ttl = 0);
    abstract function add($key, $value, $ttl = 0);
    abstract function delete($key);
    abstract function flush();
    abstract function keys($pattern = null, $full = false);

    function getKey($key)
    {
        return $key . '_' . ($this->keySuffix ?? '0');
    }

    function setKeySuffix($suffix)
    {
        $this->keySuffix = $suffix;
    }


}