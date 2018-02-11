# Priority Runner

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
