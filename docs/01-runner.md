# The Runner

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
