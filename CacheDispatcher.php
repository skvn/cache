<?php

namespace Skvn\Cache;

use Skvn\Base\Traits\AppHolder;
use Skvn\Base\Traits\ConstructorConfig;

class CacheDispatcher
{
    use AppHolder;
    use ConstructorConfig;

    protected $storages = [];


    function storage($name = null)
    {
        if (empty($name)) {
            $name = $this->config['default'];
        }
        return $this->storages[$name] ?? $this->createStorage($name);
    }

    protected function createStorage($name)
    {
        if (!isset($this->config['storages'][$name])) {
            throw new Exceptions\CacheException('Cache storage ' . $name . ' do not exists');
        }
        $class = $this->config['storages'][$name]['class'];
        $this->storages[$name] = new $class($this->config['storages'][$name]);
        $this->storages[$name]->setKeySuffix($this->config['key_suffix']);
        return $this->storages[$name];
    }

    function __call($method, $args)
    {
        return $this->storage()->$method(...$args);
    }




}