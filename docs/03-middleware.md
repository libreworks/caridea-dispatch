# Middleware

Middleware implementations we include.

## Reporter

Use the `Caridea\Dispatch\Middleware\Reporter` to capture `Throwable`s, log them, and re-throw the exception. PSR-3 required.

## Prototype

A simple middleware that returns a `ResponseInterface` you provide.
