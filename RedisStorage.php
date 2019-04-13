<?php

namespace Skvn\Cache;

class RedisStorage extends Storage
{

    protected $redis = null;


    protected function getRedis()
    {
        if (is_null($this->redis)) {
            if (!class_exists(\Redis :: class)) {
                throw new Exceptions\CacheException('redis extension not available');
            }
            $this->redis = $this->app->get('redis')->connection($this->config['connection']);
        }
        return $this->redis;
    }


    function get($key)
    {
        $v = $this->getRedis()->get($this->getKey($key));
        return $v === false ? null : $this->unserialize($v);
    }

    function set($key, $value, $ttl = 0)
    {
        $redis = $this->getRedis();
        if ($ttl > 0) {
            return $this->getRedis()->setex($this->getKey($key), $ttl, $this->serialize($value));
        } else {
            return $this->getRedis()->set($this->getKey($key), $this->serialize($value));
        }
    }

    private function serialize($value)
    {
        if (is_scalar($value)) {
            return $value;
        } else {
            return serialize($value);
        }
    }

    private function unserialize($value)
    {
        if (preg_match('#^[a-z]\:#', $value)) {
            return @unserialize($value);
        } else {
            return $value;
        }
    }

    function add($key, $value, $ttl = 0)
    {
        $result = $this->getRedis()->setnx($this->getKey($key), $value);
        if (empty($result)) {
            return false;
        }
        if (!empty($ttl)) {
            $expres = $this->getRedis()->expire($this->getKey($key), $ttl);
            if (empty($expres)) {
                return false;
            }
        }
        return $result;
    }

    function delete($key)
    {
        return $this->getRedis()->del($this->getKey($key));
    }

    function increment($key, $value = 1)
    {
        return $this->getRedis()->incrby($this->getKey($key), $value);
    }

    function flush()
    {
        return $this->getRedis()->flushdb();
    }

    function keys($pattern = null, $full = false)
    {
        $keys = $this->getRedis()->keys($pattern);
        if (!$full) {
            $suffix = $this->getKey('');
            $keys = array_values(array_filter($keys, function ($k) use ($suffix) {
                return preg_match('#' . $suffix . '$#', $k);
            }));
        }
        return $keys;
    }


}