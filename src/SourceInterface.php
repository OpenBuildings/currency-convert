<?php

namespace CL\CurrencyConvert;

use SebastianBergmann\Money\Currency;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
interface SourceInterface
{
    /**
     * @param  Currency $from
     * @param  Currency $to
     * @return float
     */
    public function getRateBetween(Currency $from, Currency $to);
}
