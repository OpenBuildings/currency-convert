Currency Convert
================

[![Build Status](https://travis-ci.org/clippings/currency-convert.png?branch=master)](https://travis-ci.org/clippings/currency-convert)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/clippings/currency-convert/badges/quality-score.png)](https://scrutinizer-ci.com/g/clippings/currency-convert/)
[![Code Coverage](https://scrutinizer-ci.com/g/clippings/currency-convert/badges/coverage.png)](https://scrutinizer-ci.com/g/clippings/currency-convert/)
[![Latest Stable Version](https://poser.pugx.org/clippings/currency-convert/v/stable.png)](https://packagist.org/packages/clippings/currency-convert)

Currency Conversion for the Money package

Usage
-----

Convert £100 to corresponding € amount, based on the daily rate of the European Central Bank

```php
use CL\CurrencyConvert\Converter;
use CL\FileCache\ItemPool;
use SebastianBergmann\Money\GBP;
use SebastianBergmann\Money\Currency;

Converter::initialize(new ECBSource(new ItemPool()));

$hundred_pounds = new GBP(10000);

$converted = Converter::get()->convert($hundred_pounds, new Currency('EUR'));
```

You have to initialize the converter with a source object - default is ECBSource. It also requires a cache pool object (based on [PSR Cache](https://github.com/clippings/psr-cache))

License
-------

Copyright (c) 2014, Clippings Ltd. Developed by Ivan Kerin

Under BSD-3-Clause license, read LICENSE file.
