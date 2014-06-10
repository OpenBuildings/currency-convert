<?php

namespace CL\CurrencyConvert\Test;

use PHPUnit_Framework_TestCase;
use CL\PsrCache\NullItemPool;
use CL\CurrencyConvert\Converter;
use CL\CurrencyConvert\NullSource;

abstract class AbstractTestCase extends \PHPUnit_Framework_TestCase
{
    public function getNullItemPool()
    {
        return new NullItemPool();
    }

    public function getNullSource()
    {
        return new NullSource();
    }

    public function getConverter()
    {
        return new Converter($this->getNullSource());
    }
}
