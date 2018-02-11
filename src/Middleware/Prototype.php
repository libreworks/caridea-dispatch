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
namespace Caridea\Dispatch\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Always returns an immutable ResponseInterface
 *
 * @copyright 2017-2018 LibreWorks contributors
 * @license   Apache-2.0
 */
class Prototype implements MiddlewareInterface, RequestHandlerInterface
{
    /**
     * @var \Psr\Http\Message\ResponseInterface
     */
    private $prototype;

    /**
     * Creates a new Prototype middleware
     *
     * @param \Psr\Http\Message\ResponseInterface $prototype  The response prototype
     */
    public function __construct(ResponseInterface $prototype)
    {
        $this->prototype = $prototype;
    }

    /**
     * {@inheritDoc}
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $this->prototype;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->prototype;
    }
}
