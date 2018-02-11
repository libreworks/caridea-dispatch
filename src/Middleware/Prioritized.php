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

use Psr\Http\Server\MiddlewareInterface;

/**
 * A middleware that specifies a priority number.
 *
 * @copyright 2017-2018 LibreWorks contributors
 * @license   Apache-2.0
 */
interface Prioritized extends MiddlewareInterface
{
    /**
     * Gets the middleware priority; larger means first.
     *
     * @return int  The middleware priority
     */
    public function getPriority(): int;
}
