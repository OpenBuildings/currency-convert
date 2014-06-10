<?php

namespace CL\CurrencyConvert;

use SebastianBergmann\Money\Currency;
use CL\PsrCache\CacheItemPoolInterface;
use SimpleXMLElement;
use Exception;
use InvalidArgumentException;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class ECBSource implements SourceInterface
{
    /**
     * @var array
     */
    private $rates;

    /**
     * @var CachedRemoteData
     */
    private $cachedRemoteData;

    /**
     * @param CacheItemPoolInterface $cachePool
     * @param string                 $key
     */
    public function __construct(CacheItemPoolInterface $cachePool, $key = 'ECBSource')
    {
        $this->cachedRemoteData = new CachedRemoteData(
            $cachePool,
            'http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml',
            $key
        );
    }

    /**
     * @return CachedRemoteData
     */
    public function getCachedRemoteData()
    {
        return $this->cachedRemoteData;
    }

    /**
     * @return string
     * @throws Exception If error retrieving remote data
     */
    public function getData()
    {
        return $this->cachedRemoteData->get();
    }

    /**
     * @return array
     * @throws Exception If error retrieving remote data
     */
    public function getRates()
    {
        if (! $this->rates) {
            $xml = new SimpleXMLElement($this->getData());

            $rates = json_decode(json_encode($xml));

            $this->rates = array('EUR' => '1');

            if (! isset($rates->Cube->Cube->Cube)) {
                throw new Exception('Invalid data returned from source. Must have "Cube" tag');
            }

            foreach ($rates->Cube->Cube->Cube as $rate) {
                $this->rates[$rate->{'@attributes'}->currency] = $rate->{'@attributes'}->rate;
            }
        }

        return $this->rates;
    }

    /**
     * @param  Currency $currency
     * @return float
     * @throws Exception If error retrieving remote data
     */
    public function getRate(Currency $currency)
    {
        $rates = $this->getRates();
        $code = $currency->getCurrencyCode();

        if (empty($rates[$code])) {
            throw new InvalidArgumentException(
                sprintf('%s (%s) is not supported by this source', $currency->getDisplayName(), $code)
            );
        }

        return (float) $rates[$code];
    }

    /**
     * @param  Currency $from
     * @param  Currency $to
     * @return float
     */
    public function getRateBetween(Currency $from, Currency $to)
    {
        $from = $this->getRate($from);
        $to = $this->getRate($to);

        return $from / $to;
    }
}
