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
        $conf = $this->config['storages'][$name] ?? null;
        if (empty($conf)) {
            throw new Exceptions\CacheException('Cache storage ' . $name . ' do not exists');
        }
        $class = $conf['class'];
        $storage = new $class($conf);
        $storage->setApp($this->app);
        $storage->setKeySuffix($this->config['key_suffix']);
        foreach ($conf['decorate'] ?? [] as $decorator) {
            $storage = new $decorator(['cache' => $storage]);
        }
        $this->storages[$name] = $storage;
        return $this->storages[$name];
    }

    function __call($method, $args)
    {
        return $this->storage()->$method(...$args);
    }




}