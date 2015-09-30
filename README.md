# SilexTranslationProvider

[![Build Status](https://img.shields.io/travis/jpcercal/silex-translation-provider/master.svg?style=flat-square)](http://travis-ci.org/jpcercal/silex-translation-provider)
[![Coverage Status](https://coveralls.io/repos/jpcercal/silex-translation-provider/badge.svg)](https://coveralls.io/r/jpcercal/silex-translation-provider)
[![Latest Stable Version](https://img.shields.io/packagist/v/cekurte/silex-translation-provider.svg?style=flat-square)](https://packagist.org/packages/cekurte/silex-translation-provider)
[![License](https://img.shields.io/packagist/l/cekurte/silex-translation-provider.svg?style=flat-square)](https://packagist.org/packages/cekurte/silex-translation-provider)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/49f6bd17-8f56-4887-a254-0ea227fdc30f/mini.png)](https://insight.sensiolabs.com/projects/49f6bd17-8f56-4887-a254-0ea227fdc30f)

- A simple silex service provider (with all methods covered by php unit tests) that adds the Yaml files to the [Silex\Provider\TranslationServiceProvider](https://github.com/silexphp/Silex/blob/1.3/src/Silex/Provider/TranslationServiceProvider.php) to increase the power of your application, **contribute with this project**!

## Installation

The package is available on [Packagist](http://packagist.org/packages/cekurte/silex-translation-provider).
The source files is [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md) compatible.
Autoloading is [PSR-4](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md) compatible.

```shell
composer require cekurte/silex-translation-provider
```

## Documentation

To use this library you need register the [Silex\Provider\TranslationServiceProvider](https://github.com/silexphp/Silex/blob/1.3/src/Silex/Provider/TranslationServiceProvider.php) and the [Cekurte\Silex\Translation\Provider\TranslationServiceProvider](https://github.com/jpcercal/silex-translation-provider/blob/v0.0.1/src/Provider/TranslationServiceProvider.php).

```php
<?php

use Cekurte\Silex\Translation\Provider\TranslationServiceProvider;
use Silex\Provider\TranslationServiceProvider as SilexTranslationServiceProvider;

// ...

$app->register(new SilexTranslationServiceProvider());
$app->register(new TranslationServiceProvider(), [
    'translation.directory' => realpath(__DIR__ . '/../your-translation-directory')
]);

// ...
```

And create in **your-translation-directory/** the translation yaml files, note that the filename must be the locale name. Then, the following filenames are valid:

- en.yaml
- en.yml
- es.yaml
- es.yml
- fr.yaml
- fr.yml
- ...

If you liked of this library, give me a *star* **=)**.

Contributing
------------

1. Fork it
2. Create your feature branch (`git checkout -b my-new-feature`)
3. Make your changes
4. Run the tests, adding new ones for your own code if necessary (`vendor/bin/phpunit`)
5. Commit your changes (`git commit -am 'Added some feature'`)
6. Push to the branch (`git push origin my-new-feature`)
7. Create new Pull Request
