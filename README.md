# caridea-dispatch
Caridea is a miniscule PHP application library. This shrimpy fellow is what you'd use when you just want some helping hands and not a full-blown framework.

![](http://libreworks.com/caridea-100.png)

This is its [PSR-7](http://www.php-fig.org/psr/psr-7/) and [PSR-15](http://www.php-fig.org/psr/psr-15/) compliant request handler, with a few middleware implementations.

[![Packagist](https://img.shields.io/packagist/v/caridea/dispatch.svg)](https://packagist.org/packages/caridea/dispatch)
[![Build Status](https://travis-ci.org/libreworks/caridea-dispatch.svg)](https://travis-ci.org/libreworks/caridea-dispatch)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/libreworks/caridea-dispatch/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/libreworks/caridea-dispatch/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/libreworks/caridea-dispatch/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/libreworks/caridea-dispatch/?branch=master)
[![Documentation Status](http://readthedocs.org/projects/caridea-dispatch/badge/?version=latest)](http://caridea-dispatch.readthedocs.io/en/latest/?badge=latest)

## Installation

You can install this library using Composer:

```console
$ composer require caridea/dispatch
```

* The master branch (version 3.x) of this project requires PHP 7.1 and depends on `psr/http-message`, `psr/http-server-handler`, and `psr/http-server-middleware`.

## Compliance

Releases of this library will conform to [Semantic Versioning](http://semver.org).

Our code is intended to comply with [PSR-1](http://www.php-fig.org/psr/psr-1/), [PSR-2](http://www.php-fig.org/psr/psr-2/), and [PSR-4](http://www.php-fig.org/psr/psr-4/). If you find any issues related to standards compliance, please send a pull request!

## Documentation

* Head over to [Read the Docs](http://caridea-dispatch.readthedocs.io/en/latest/)

## Examples

Just a few quick examples.

### Runner

You can use the `Runner` to give it some middleware and let it handle your request.

```php
$request = new \Zend\Diactoros\ServerRequest();
// I generally use zend-diactoros, but feel free to use whatever PSR-7 library you use

$middleware = [
    // your custom \Psr\Http\Server\MiddlewareInterface objects
];
$runner = new \Caridea\Dispatch\Runner($middleware);
$response = $runner->handle($request);
```

Your final middleware should create and return a PSR-7 `ResponseInterface`. You can also provide one to the `Runner` constructor and it handles it automatically.

```php
$response = new \Zend\Diactoros\Response();
$runner = new \Caridea\Dispatch\Runner($middleware, $response);
$response = $runner->handle($request);
```

A `Runner` is immutable. You can use it more than once.

```php
$runner = new \Caridea\Dispatch\Runner($middleware);
$response1 = $runner->handle($request);
$response2 = $runner->handle($request);
```

### Priority Runner

We included an extension of the `MiddlewareInterface`: `Caridea\Dispatch\Middleware\Prioritized`. Using the `Caridea\Dispatch\PriorityRunner`, you can provide middleware out of order, and they get invoked in order of priority.

```php
$middleware = [
    // your custom \Psr\Http\Server\MiddlewareInterface objects.
    // Any that implement Prioritized will get run in priority order,
    // Any others get run last, in insert order.
];
$runner = new \Caridea\Dispatch\PriorityRunner($middleware);
```

You can also use the `Caridea\Dispatch\Middleware\PriorityDelegate` class to assign priority to an existing middleware implementation.

```php
$middleware = new \Caridea\Dispatch\Middleware\PriorityDelegate($middleware, 123456);
```

### Middleware

Middleware implementations we include.

#### Reporter

Use the `Caridea\Dispatch\Middleware\Reporter` to capture `Throwable`s, log them, and re-throw the exception. PSR-3 required.

#### Prototype

A simple middleware that returns a `ResponseInterface` you provide.

## See Also

* [This blog post](https://mwop.net/blog/2018-01-23-psr-15.html) from Matthew Weier O'Phinney about why PSR-15 ditched the old pattern of "double pass" middleware with anonymous functions.
