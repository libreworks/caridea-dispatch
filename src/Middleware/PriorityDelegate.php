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

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * A middleware that specifies a priority number.
 *
 * Allows you to specify priority for an existing middleware implementation
 * at runtime without having to extend the class.
 *
 * @copyright 2017-2018 LibreWorks contributors
 * @license   Apache-2.0
 */
class PriorityDelegate implements Prioritized
{
    /**
     * @var \Psr\Http\Server\MiddlewareInterface
     */
    private $delegate;
    /**
     * @var int
     */
    private $priority;

    /**
     * Creates a new `PriorityDelegate`.
     *
     * @param \Psr\Http\Server\MiddlewareInterface $delegate  The middleware to delegate
     * @param int $priority  The priority
     */
    public function __construct(MiddlewareInterface $delegate, int $priority)
    {
        $this->delegate = $delegate;
        $this->priority = $priority;
    }

    /**
     * {@inheritDoc}
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * {@inheritDoc}
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $this->delegate->process($request, $handler);
    }
}
