<?php

namespace Skvn\Cache;

class MemcacheStorage extends Storage
{

    protected $memcache = null;


    protected function getMemcache()
    {
        if (is_null($this->memcache)) {
            if (!class_exists(\Memcache :: class)) {
                throw new Exceptions\CacheException('Memcache extension not available');
            }
            $this->memcache = new \Memcache();
            $this->memcache->pconnect($this->config['host'] ?? 'localhost', $this->config['port'] ?? 11211);
        }
        return $this->memcache;
    }


    function get($key)
    {
        $v = $this->getMemcache()->get($this->getKey($key));
        return $v === false ? null : $v;
    }

    function set($key, $value, $ttl = 0)
    {
        return $this->getMemcache()->set($this->getKey($key), $value, 0, $ttl);
    }

    function add($key, $value, $ttl = 0)
    {
        return $this->getMemcache()->add($this->getKey($key), $value, 0, $ttl);
    }

    function delete($key)
    {
        return $this->getMemcache()->delete($this->getKey($key));
    }
    
    function increment($key, $value = 1)
    {
        return $this->getMemcache()->increment($this->getKey($key), $value);
    }

    function flush()
    {
        return $this->getMemcache()->flush();
    }

    function keys($pattern = null, $full = false)
    {
        $slabs = $this->getMemcache()->getStats('slabs');
        $keys = array();
        foreach ($slabs as $slabid => $slab) {
            if (!is_numeric($slabid)) {
                continue;
            }
            if (empty($slab['used_chunks'])) {
                continue;
            }
            $content = $this->getMemcache()->getStats('cachedump', intval($slabid), 1000000);
            if (is_array($content)) {
                foreach ($content as $key => $info) {
                    if (!empty($pattern)) {
                        if (!preg_match('#' . $pattern . '#', $key)) {
                            continue;
                        }
                    }
                    if (!$full) {
                        if (!preg_match('#' . $this->getKey('') . '$#', $key)) {
                            continue;
                        }
                    }
                    $keys[] = $key;
                }
            }
        }
        return $keys;
    }


}