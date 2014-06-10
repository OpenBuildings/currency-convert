<?php

namespace CL\CurrencyConvert\Test;

use CL\CurrencyConvert\ECBSource;
use SebastianBergmann\Money\Currency;

/**
 * @coversDefaultClass CL\CurrencyConvert\ECBSource
 */
class ECBSourceTest extends AbstractTestCase
{
    /**
     * @covers ::__construct
     * @covers ::getCachedRemoteData
     */
    public function testConstruct()
    {
        $pool = $this->getNullItemPool();

        $source = new ECBSource($pool, 'someKey');
        $this->assertSame($pool, $source->getCachedRemoteData()->getCachePool());
        $this->assertSame('someKey', $source->getCachedRemoteData()->getKey());
    }

    /**
     * @covers ::getData
     */
    public function testGetData()
    {
        $source = new ECBSource($this->getNullItemPool());

        $data = $source->getData();

        $this->assertContains('<gesmes:name>European Central Bank</gesmes:name>', $data);
    }

    /**
     * @covers ::getRates
     */
    public function testGetRates()
    {
        $pool = $this->getNullItemPool();
        $source = $this->getMock('CL\CurrencyConvert\ECBSource', ['getData'], [$pool]);
        $data = file_get_contents(__DIR__.'/../example_ecb.xml');

        $source
            ->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $expected = array(
            'EUR' => '1',
            'USD' => '1.3608',
            'GBP' => '0.80985',
        );

        $this->assertEquals($expected, $source->getRates());

        $this->assertEquals($expected, $source->getRates(), 'getData should be called only once');
    }

    /**
     * @covers ::getRates
     * @expectedException Exception
     * @expectedExceptionMessage Invalid data returned from source. Must have "Cube" tag
     */
    public function testGetRatesError()
    {
        $pool = $this->getNullItemPool();
        $source = $this->getMock('CL\CurrencyConvert\ECBSource', ['getData'], [$pool]);
        $data = file_get_contents(__DIR__.'/../error_ecb.xml');

        $source
            ->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $source->getRates();
    }

    /**
     * @covers ::getRate
     */
    public function testGetRate()
    {
        $pool = $this->getNullItemPool();
        $source = $this->getMock('CL\CurrencyConvert\ECBSource', ['getRates'], [$pool]);

        $rates = array(
            'EUR' => '1',
            'USD' => '1.3608',
            'GBP' => '0.80985',
        );

        $source
            ->expects($this->any())
            ->method('getRates')
            ->will($this->returnValue($rates));

        $this->assertEquals('1', $source->getRate(new Currency('EUR')));
        $this->assertEquals('1.3608', $source->getRate(new Currency('USD')));
        $this->assertEquals('0.80985', $source->getRate(new Currency('GBP')));

        $this->setExpectedException('Exception', 'Bulgarian Lev (BGN) is not supported by this source');

        $source->getRate(new Currency('BGN'));
    }

    /**
     * @covers ::getRateBetween
     */
    public function testGetRateBetween()
    {
        $pool = $this->getNullItemPool();
        $source = $this->getMock('CL\CurrencyConvert\ECBSource', ['getRate'], [$pool]);

        $eur = new Currency('EUR');
        $usd = new Currency('USD');
        $gbp = new Currency('GBP');

        $source
            ->expects($this->any())
            ->method('getRate')
            ->will($this->returnValueMap(array(
                array($eur, 1.0),
                array($usd, 1.3608),
                array($gbp, 0.80985),
            )));

        $this->assertEquals(0.80985, $source->getRateBetween($gbp, $eur));
        $this->assertEquals(1.680311168734951, $source->getRateBetween($usd, $gbp));
    }
}
