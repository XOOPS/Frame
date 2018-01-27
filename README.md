# Frame

Frame implements a simple [PSR-15 compatible](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-15-request-handlers.md) middleware dispatcher.

Frame is composer managed, so the easiest way to install it is to require `xoops/frame`.

[![Software License](https://img.shields.io/badge/license-GPL-brightgreen.svg?style=flat)](LICENSE)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/XOOPS/Frame.svg?style=flat)](https://scrutinizer-ci.com/g/XOOPS/Frame/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/XOOPS/Frame.svg?style=flat)](https://scrutinizer-ci.com/g/XOOPS/Frame)
[![Latest Version](https://img.shields.io/github/release/XOOPS/Frame.svg?style=flat)](https://github.com/XOOPS/Frame/releases)

## Xoops/Frame/Rack

**Rack** is a middleware dispatcher. Instances of `Psr\Http\Server\MiddlewareInterface` can be added
to the rack. After the desired middlewares have been added, it is dispatched by passing a
`Psr\Http\Message\ServerRequestInterface` object to the rack run() method.

```php
$rack = new \Xoops\Frame\Rack();
$rack->add($someMiddleware);
$rack->add($anotherMiddleware);
$response = $rack->run($request);
```

**Rack** will invoke each middleware in the order it was added. **Rack** will pass itself to
each middleware as the `Psr\Http\Server\RequestHandlerInterface` *$handler* parameter,
and when called as such will dispatch the next middleware in its queue.

The racked middleware is expected to ultimately return a `Psr\Http\Message\ResponseInterface`
object, and as per PSR-15, it will be returned from the run() invocation.

As an alternative to the run() method, you can depend on the __invoke() method to allow
calling the **Rack** instance as a function.

```php
$response = $rack($request);
```

A **Rack** instance is also a `Psr\Http\Server\MiddlewareInterface` object. This
allows a **Rack** instance to be used as middleware, allowing a racks to be stacked.

## Xoops/Frame/Exception

This namespace defines the following exceptions which can be thrown by **Rack**:

### InvalidHandlerException

This results from either trying to add a **Rack** instance to itself as middleware, or from
forcinging something other than a valid `Psr\Http\Server\MiddlewareInterface` object to
the middleware queue.

### RackExhaustedException

This exception results from attempting to advance past the end of the middleware queue.
This condition indicates that no response message has been returned and there is no
handler on the middleware queue to delegate response creation to.

## Xoops\Frame\Panel

The `Panel` namespace contains several small *rack panels* that might be useful middleware
in a duct tape sort of role.

### Blank

`Blank` is just a placeholder. It does nothing other than delegate and return the response.

```
$rack->addToQueue(new Blank);
```

### ClosureToMiddleware

`ClosureToMiddleware` lets you build middleware from a [PHP Closure](http://php.net/manual/en/class.closure.php) or [anonymous function](http://php.net/manual/en/functions.anonymous.php).

```php
$callable = function (RequestInterface $request, RequestHandlerInterface $handler) {
    $response = $handler->handle($request);
    $modifiedResponse = $response->withHeader('X-Powered-By', 'black coffee');
    return $modifiedResponse;
};
$middleware = new ClosureToMiddleware($callable);

$rack->add($middleware);
```

### FixedResponse

`FixedResponse` returns the `Psr\Http\Message\ResponseInterface` passed to it at instantiation.

```
$response = $myResponseFactory->createResponse(200);
$rack->add(new FixedResponse($response));
```
