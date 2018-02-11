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

use Psr\Http\Message\ResponseInterface;
use Caridea\Dispatch\Middleware\Prioritized;
use Caridea\Dispatch\Middleware\PriorityDelegate;

/**
 * Like `Runner`, but runs middleware in order of priority in addition to
 * insertion order.
 *
 * @copyright 2017-2018 LibreWorks contributors
 * @license   Apache-2.0
 */
class PriorityRunner extends Runner
{
    /**
     * {@inheritDoc}
     */
    public function __construct(iterable $middleware, ?ResponseInterface $response = null)
    {
        parent::__construct($middleware, $response);
        $this->middleware = array_map(function ($mw) {
            return $mw instanceof Prioritized ?
                $mw : new PriorityDelegate($mw, PHP_INT_MIN);
        }, $this->middleware);
        // Higher priority should go first; $this->middleware is reversed
        usort($this->middleware, function ($a, $b) {
            return $a->getPriority() <=> $b->getPriority();
        });
    }
}
