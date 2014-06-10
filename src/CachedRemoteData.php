<?php

namespace CL\CurrencyConvert;

use CL\PsrCache\CacheItemPoolInterface;
use Exception;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class CachedRemoteData
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $key;

    /**
     * @var CacheItemPoolInterface
     */
    private $cachePool;

    /**
     * @param CacheItemPoolInterface $cachePool
     * @param string                 $url
     * @param string                 $key
     */
    public function __construct(CacheItemPoolInterface $cachePool, $url, $key = null)
    {
        $this->cachePool = $cachePool;
        $this->url = $url;
        $this->key = $key ?: $url;
    }

    /**
     * @return CacheItemPoolInterface
     */
    public function getCachePool()
    {
        return $this->cachePool;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return string
     * @throws Exception If problems loading data
     */
    public function get()
    {
        $item = $this->cachePool->getItem($this->key);

        if (! $item->isHit()) {
            // Cache for a day
            $item
                ->set($this->load(), 86400)
                ->save();
        }

        return $item->get();
    }

    /**
     * @return string
     * @throws Exception If problems loading data
     */
    public function load()
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)');

        $data = curl_exec($ch);

        if ($data === false) {
            throw new Exception(
                sprintf('Error %s when loading data from: %s', curl_error($ch), $this->url)
            );
        }

        curl_close($ch);

        return $data;
    }
}
