<?php
declare(strict_types=1);
/**
 * Caridea
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 *
 * @copyright 2017-2018 LibreWorks contributors
 * @license   Apache-2.0
 */
namespace Caridea\Dispatch;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;

/**
 * Runs a request through the middleware.
 *
 * You can use this class itself as middleware, too. For example, you declare
 * this runner to be the second middleware of another runner. If this runner has
 * three middleware objects, it will participate in the containing runner like
 * this:
 *
 * ```
 * |→ Outer runner middleware 1
 * |  → Inner runner middleware 1
 * |    → Inner runner middleware 2
 * |      → Inner runner middleware 3
 * |        → Outer runner middleware 3
 * |        ← Outer runner middleware 3
 * |      ← Inner runner middleware 3
 * |    ← Inner runner middleware 2
 * |  ← Inner runner middleware 1
 * |← Outer runner middleware 1
 * ```
 *
 * @copyright 2017-2018 LibreWorks contributors
 * @license   Apache-2.0
 */
class Runner implements RequestHandlerInterface, MiddlewareInterface
{
    /**
     * @var \Psr\Http\Server\MiddlewareInterface[]
     */
    protected $middleware;

    /**
     * Creates a new Runner.
     *
     * @param iterable $middleware  Any traversable value containing MiddlewareInterface objects.
     * @param \Psr\Http\Message\ResponseInterface|null $response  Optional. The response for the innermost middleware to receive.
     * @throws \InvalidArgumentException if one of the items in `$middleware` is not a `MiddlewareInterface`
     * @throws \LengthException if no middleware are provided and no default `ResponseInterface` is provided
     */
    public function __construct(iterable $middleware, ?ResponseInterface $response = null)
    {
        $wares = [];
        foreach ($middleware as $mw) {
            if (!($mw instanceof MiddlewareInterface)) {
                throw new \InvalidArgumentException("Value must be an instance of MiddlewareInterface");
            }
            $wares[] = $mw;
        }
        if ($response !== null) {
            $wares[] = new Middleware\Prototype($response);
        }
        if (empty($wares)) {
            throw new \LengthException("You must specify at least one middleware or a ResponseInterface");
        }
        $this->middleware = array_reverse($wares);
    }

    /**
     * {@inheritDoc}
     *
     * @throws \UnderflowException if a middleware calls the provided handler and no middleware remain
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->run()->handle($request);
    }

    /**
     * {@inheritDoc}
     *
     * In order for the `$handler` provided to be used, the innermost middleware
     * of this `Runner` needs to use the `RequestHandlerInterface` provided to
     * it. If you've supplied a `ResponseInterface` in this `Runner`'s
     * constructor, that isn't the case.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $this->run($handler)->handle($request);
    }

    /**
     * @param \Psr\Http\Server\RequestHandlerInterface|null $delegateHandler
     * @return \Psr\Http\Server\RequestHandlerInterface
     */
    protected function run($delegateHandler = null)
    {
        return new class($this->middleware, $delegateHandler) implements RequestHandlerInterface
        {
            /**
             * @var \Psr\Http\Server\MiddlewareInterface[]
             */
            private $toRun;
            /**
             * @var \Psr\Http\Server\RequestHandlerInterface|null
             */
            private $andFinally;

            public function __construct($reversedMiddleware, $delegateHandler)
            {
                $this->toRun = $reversedMiddleware;
                $this->andFinally = $delegateHandler;
            }

            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                if (empty($this->toRun)) {
                    if ($this->andFinally !== null) {
                        return $this->andFinally->handle($request);
                    }
                    throw new \UnderflowException("No middleware remain");
                }
                $c = array_pop($this->toRun);
                return $c->process($request, $this);
            }
        };
    }
}
