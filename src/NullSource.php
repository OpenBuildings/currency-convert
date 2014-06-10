<?php

namespace CL\CurrencyConvert;

use SebastianBergmann\Money\Currency;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class NullSource implements SourceInterface
{
    public function getRateBetween(Currency $from, Currency $to)
    {
        return 1;
    }
}
