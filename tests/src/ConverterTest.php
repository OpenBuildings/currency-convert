<?php

namespace CL\CurrencyConvert\Test;

use CL\CurrencyConvert\Converter;
use SebastianBergmann\Money\Currency;
use SebastianBergmann\Money\Money;

/**
 * @coversDefaultClass CL\CurrencyConvert\Converter
 */
class ConverterTest extends AbstractTestCase
{
    /**
     * @covers ::initialize
     * @covers ::get
     */
    public function testStatic()
    {
        $source = $this->getNullSource();

        Converter::initialize($source);

        $converter = Converter::get();

        $this->assertSame($source, $converter->getSource());
    }

    /**
     * @covers ::clear
     * @covers ::get
     * @expectedException LogicException
     * @expectedExceptionMessage Converter not initialized, call Converter::initialize(source)
     */
    public function testClear()
    {
        Converter::clear();

        Converter::get();
    }

    /**
     * @covers ::__construct
     * @covers ::getSource
     */
    public function testConstruct()
    {
        $source = $this->getNullSource();

        $converter = new Converter($source);
        $this->assertSame($source, $converter->getSource());
    }

    /**
     * @covers ::convert
     */
    public function testConvert()
    {
        $source = $this->getMock('CL\CurrencyConvert\NullSource', ['getRateBetween']);

        $converter = new Converter($source);

        $bgn = new Currency('BGN');
        $gbp = new Currency('GBP');

        $source
            ->expects($this->exactly(3))
            ->method('getRateBetween')
            ->will($this->returnValueMap(array(
                array($bgn, $gbp, 0.5),
                array($gbp, $bgn, 2),
            )));

        $from = new Money(5000, $bgn);
        $result = $converter->convert($from, $gbp);
        $this->assertEquals(new Money(2500, $gbp), $result);


        $from = new Money(5000, $gbp);
        $result = $converter->convert($from, $bgn);
        $this->assertEquals(new Money(10000, $bgn), $result);

        $from = new Money(155, $bgn);
        $result = $converter->convert($from, $gbp, PHP_ROUND_HALF_DOWN);
        $this->assertEquals(new Money(77, $gbp), $result);
    }
}
