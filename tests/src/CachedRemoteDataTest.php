<?php

namespace CL\CurrencyConvert\Test;

use CL\CurrencyConvert\CachedRemoteData;

/**
 * @coversDefaultClass CL\CurrencyConvert\CachedRemoteData
 */
class CachedRemoteDataTest extends AbstractTestCase
{
    /**
     * @covers ::__construct
     * @covers ::getCachePool
     * @covers ::getUrl
     * @covers ::getKey
     */
    public function testConstruct()
    {
        $pool = $this->getNullItemPool();

        $cachedRemoteData = new CachedRemoteData($pool, 'http://url.example.com', 'someKey');
        $this->assertSame($pool, $cachedRemoteData->getCachePool());
        $this->assertSame('http://url.example.com', $cachedRemoteData->getUrl());
        $this->assertSame('someKey', $cachedRemoteData->getKey());

        $cachedRemoteData = new CachedRemoteData($pool, 'http://url.example.com');

        $this->assertSame('http://url.example.com', $cachedRemoteData->getKey());
    }

    /**
     * @covers ::get
     */
    public function testGet()
    {
        $cachedRemoteData = $this->getMock(
            'CL\CurrencyConvert\CachedRemoteData',
            ['load'],
            [$this->getNullItemPool(), 'http://url.example.com']
        );

        $cachedRemoteData
            ->expects($this->once())
            ->method('load')
            ->will($this->returnValue('Some test val'));

        $this->assertEquals('Some test val', $cachedRemoteData->get());
    }

    /**
     * @covers ::load
     */
    public function testLoad()
    {
        $cachedRemoteData = new CachedRemoteData(
            $this->getNullItemPool(),
            'file://'.__DIR__.'/../test_file.txt'
        );

        $this->assertEquals("TEST FILE\n", $cachedRemoteData->load());
    }

    /**
     * @covers ::load
     * @expectedException Exception
     */
    public function testLoadException()
    {
        $cachedRemoteData = new CachedRemoteData(
            $this->getNullItemPool(),
            'file://'.__DIR__.'/../test_file_missing.txt'
        );

        $cachedRemoteData->load();
    }
}
