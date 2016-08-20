<?php

namespace Core\Translate;

use Illuminate\Cache\CacheManager;
use Illuminate\Translation\FileLoader;

class DatabaseLoader extends FileLoader
{
    /**
     * @var CacheManager
     */
    private $cache;
    /**
     * @var bool
     */
    private $cacheEnable;

    /**
     * DatabaseLoader constructor.
     *
     * @param CacheManager $cache
     * @param bool         $cacheEnable
     * @param array        $klasses
     */
    public function __construct(CacheManager $cache, $cacheEnable, ...$klasses)
    {
        parent::__construct(...$klasses);
        $this->cache = $cache;
        $this->cacheEnable = $cacheEnable;
    }

    /**
     * Load the messages for the given locale.
     *
     * @param  string $locale
     * @param  string $group
     * @param  string $namespace
     *
     * @return array
     */
    public function load($locale, $group, $namespace = null)
    {
        $res = parent::load($locale, $group, $namespace);

        $dbRes = $this->cacheEnable ? $this->loadFromCache($locale, $group, $namespace) : [];

        return array_merge($res, $dbRes);
    }

    /**
     * @param $locale
     * @param $group
     * @param $namespace
     *
     * @return array
     */
    private function loadFromCache($locale, $group, $namespace)
    {
        $res = $this->cache->get(sprintf('translations.%s', $locale));

        if (is_null($res)) {
            return [];
        }

        return isset($res[$group]) ? $res[$group] : [];
    }


}