<?php

namespace CL\CurrencyConvert\Test;

use CL\CurrencyConvert\NullSource;
use SebastianBergmann\Money\Currency;

/**
 * @coversDefaultClass CL\CurrencyConvert\NullSource
 */
class NullSourceTest extends AbstractTestCase
{
    /**
     * @covers ::getRateBetween
     */
    public function testConstruct()
    {
        $source = new NullSource();

        $result = $source->getRateBetween(new Currency('GBP'), new Currency('EUR'));

        $this->assertEquals(1, $result);
    }
}
