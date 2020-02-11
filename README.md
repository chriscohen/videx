# Videx scrape test

## Prerequisites

* PHP >= 7.2
* Composer - either **composer.phar** in the root of the project, or a global installation.

**Note:** PHPUnit 8.5 (as used here) does not officially support PHP 7.2, but for ease of compatibility, I have chosen
to require 7.2 as the minimum version, and there should not be compatibility issues with PHPUnit 8.5.

## Setup

The **composer.lock** file is included for easy installation.

```composer install```

or

```php composer.phar install```

## Run

Please use:

```php main.php```

This is preconfigured with the correct URL and will print the output to stdOut.

If you would like to output this to a file, for example, you could use:

```php main.php > output.json```

## PHPUnit

To run the tests in basic form:

```vendor/bin/phpunit```

Please refer to the [PHPUnit documentation](https://phpunit.readthedocs.io/en/8.5/index.html) if you'd like to
customise which tests are run or the format of the output.

## Test strategy

The code is split up into classes containing easily testable methods.

I have not chosen to exhaustively test each method or component due to the time required, but the overall strategy can
be gleaned from the two classes in **tests/**.

We are aiming to test *units* of code, rather than the lifecycle of the code itself, so I have avoided writing tests for
**main.php**.

## Standards

PHP code is written to [PSR-12](https://www.php-fig.org/psr/psr-12/) standards. PHP_CodeSniffer is installed as a dev
package by composer, to validate these standards.

The **.editorconfig** file defines the indent and line ending style for all code files, which use Symfony standards:
UNIX-style (\n) line endings with 4-space indents.

## Approach and style

There are a number of ways of handling failure, such as when the scraper does not find the appropriate content on the
page, and different methods might be used depending on the desired use for the resulting JSON.

I have favoured an approach which will always return *some* JSON, and will try to return as much as possible, but uses
sensible defaults, failing silently in most cases rather than triggering a runtime error.
