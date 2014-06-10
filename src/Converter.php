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
    private static $instance;

    public static function initialize(SourceInterface $source)
    {
        self::$instance = new static($source);
    }

    public static function get()
    {
        if (! self::$instance) {
            throw new LogicException('Converter not initialized, call Converter::initialize(source)');
        }

        return self::$instance;
    }

    public static function clear()
    {
        self::$instance = null;
    }

    private $source;

    public function __construct(SourceInterface $source)
    {
        $this->source = $source;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function convert(Money $money, Currency $to, $roundingMode = PHP_ROUND_HALF_UP)
    {
        $new = new Money($money->getAmount(), $to);

        $conversionRate = $this->source->getRateBetween($money->getCurrency(), $to);

        return $new->multiply($conversionRate, $roundingMode);
    }
}
