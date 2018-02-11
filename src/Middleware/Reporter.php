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
use Psr\Log\LogLevel;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerAwareInterface;

/**
 * Catches exceptions, logs them, and re-throws.
 *
 * Requires the PSR-3 library.
 *
 * If the logger itself throws an Exception, it's silently discarded.
 *
 * @copyright 2017-2018 LibreWorks contributors
 * @license   Apache-2.0
 */
class Reporter implements MiddlewareInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var array<string,string> Stores the Exception class name to log level
     */
    private $levels;

    /**
     * Creates a new Reporter middleware.
     *
     * If an exception isn't found in the `$levels` map, this class assumes a
     * level of `LogLevel::ERROR`.
     *
     * ```php
     * $elog = new ErrorLogger(
     *     $logger,
     *     ["MyException" => LogLevel::DEBUG, "RuntimeException" => LogLevel::WARN]
     * );
     * ```
     *
     * @param \Psr\Log\LoggerInterface|null $logger  The logger; will use `Psr\Log\NullLogger` by default
     * @param array|null $levels  Map of Exception names to log levels. Order matters!
     */
    public function __construct(LoggerInterface $logger = null, array $levels = null)
    {
        $this->logger = $logger ?? new \Psr\Log\NullLogger();
        $this->levels = $levels === null ? [] : array_map(function ($a) {
            return (string) $a;
        }, $levels);
    }

    /**
     * {@inheritDoc}
     *
     * If the logger throws an exception, it's silently discarded.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (\Throwable $e) {
            $out = LogLevel::ERROR;
            foreach ($this->levels as $class => $level) {
                if ($e instanceof $class) {
                    $out = $level;
                    break;
                }
            }
            try {
                $this->logger->log($out, $e->getMessage(), ['exception' => $e]);
            } catch (\Throwable $_) {
                // Don't worry about errors the logger throws
            }
            throw $e;
        }
    }
}
