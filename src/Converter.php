<?php

namespace CL\CurrencyConvert;

use SebastianBergmann\Money\Currency;
use SebastianBergmann\Money\Money;
use LogicException;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class Converter
{
    /**
     * @var Converter
     */
    private static $instance;

    /**
     * Create a new Converter object that you can later get through "::get()"
     * @param  SourceInterface $source
     */
    public static function initialize(SourceInterface $source)
    {
        self::$instance = new static($source);
    }

    /**
     * @return Converter
     */
    public static function get()
    {
        if (! self::$instance) {
            throw new LogicException('Converter not initialized, call Converter::initialize(source)');
        }

        return self::$instance;
    }

    /**
     * Clear the object, returned through "::get"
     */
    public static function clear()
    {
        self::$instance = null;
    }

    /**
     * @var SourceInterface
     */
    private $source;

    public function __construct(SourceInterface $source)
    {
        $this->source = $source;
    }

    /**
     * @return SourceInterface
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param  Money    $money
     * @param  Currency $to
     * @param  int      $roundingMode
     * @return Money
     */
    public function convert(Money $money, Currency $to, $roundingMode = PHP_ROUND_HALF_UP)
    {
        $new = new Money($money->getAmount(), $to);

        $conversionRate = $this->source->getRateBetween($money->getCurrency(), $to);

        return $new->multiply($conversionRate, $roundingMode);
    }
}
