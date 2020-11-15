[![Packagist](https://img.shields.io/packagist/v/alexandrmazur/apilayer-coinlayer-php)](https://packagist.org/packages/alexandrmazur/apilayer-coinlayer-php)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

# Coinlayer PHP SDK

PHP SDK for [coinlayer.com](https://coinlayer.com)

## Contents

- [Installing](#installing)
- [Usage](#usage)
- [Authors](#authors)
- [License](#license)

## Getting Started

This is easy in use SDK for Coinlayer.

Original API docs placed [here](https://coinlayer.com/documentation).

### Installing

SDK available via composer:

```
composer require alexandrmazur/apilayer-coinlayer-php
```

### Usage

Code examples can be found in the [example](https://github.com/alexandrmazur96/apilayer-coinlayer-php/tree/main/examples) folder. 

## Running the tests

Tests powered by [PhpUnit](https://github.com/sebastianbergmann/phpunit).

```
php vendor/bin/phpunit tests
```

### Code style tests

This library use [PHP Code Sniffer](https://github.com/squizlabs/PHP_CodeSniffer) for code style tests. 

```
php vendor/bin/phpcs
```

### Code quality tests

This library use [Psalm](https://github.com/vimeo/psalm) for code quality tests.

```
php vendor/bin/psalm --show-info=true
```

## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/alexandrmazur96/apilayer-coinlayer-php/tags). 

## Authors

- **Olexandr Mazur** - *Initial work* - [alexandrmazur96](https://github.com/alexandrmazur96)

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details
