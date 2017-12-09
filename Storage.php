<?php

namespace Skvn\Cache;

use Skvn\Base\Traits\ConstructorConfig;
use Skvn\Base\Traits\AppHolder;
use Skvn\Event\Events\Log as LogEvent;


abstract class Storage
{
    use ConstructorConfig;
    use AppHolder;

    protected $keySuffix = null;

    abstract function get($key);
    abstract function set($key, $value, $ttl = 0);
    abstract function add($key, $value, $ttl = 0);
    abstract function delete($key);
    abstract function flush();
    abstract function keys($pattern = null, $full = false);
    abstract function increment($key, $value = 1);

    function getKey($key)
    {
        return $key . '_' . ($this->keySuffix ?? '0');
    }

    function setKeySuffix($suffix)
    {
        $this->keySuffix = $suffix;
    }

    function lock($name, $seconds, $throw = true)
    {
        $start = microtime(true);
        $step = 0;
        while ($this->add($name, 1, $seconds) === false) {
            $this->app->triggerEvent(new LogEvent([
                'message' => ++$step . '. ' . $name,
                'category' => 'cache_lock'
            ]));
            if (microtime(true) - $start > $seconds) {
                if ($throw) {
                    throw new Exceptions\LockException('Unable to get lock: ' . $name);
                }
                return;
            }
            usleep(50000);
        }
    }

    function lockedExecute($name, $seconds, callable $callback, $throw = true)
    {
        $this->lock($name, $seconds, $throw);
        $callback();
        $this->unlock($name);
    }

    function unlock($name)
    {
        $this->delete($name);
    }
    
    function getValue($key, callable $callback, $ttl = 0)
    {
        $value = $this->get($key);
        if (is_null($value)) {
            $value = $callback();
            $this->set($key, $value, $ttl);
        }
        return $value;
    }


}