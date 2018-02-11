<?php
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
 * @copyright 2015-2018 LibreWorks contributors
 * @license   Apache-2.0
 */
namespace Caridea\Dispatch;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @covers \Caridea\Dispatch\Runner
 */
class PriorityRunnerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers \Caridea\Dispatch\PriorityRunner
     */
    public function testConstruct()
    {
        $queue = [
            $this->getMiddleware(300),
            $this->getMiddleware(500),
            $this->getMiddleware(100),
        ];
        $uri = new \Zend\Diactoros\Uri('https://example.com/foo/bar');
        $request = new \Zend\Diactoros\ServerRequest([], [], $uri, 'POST');
        $response = new \Zend\Diactoros\Response();
        $object = new PriorityRunner($queue, $response);
        $out = $object->handle($request);
        $this->assertEquals('500', $out->getHeaderLine('X-Priority'));
    }

    private function getMiddleware($priority)
    {
        return new class($priority) implements Middleware\Prioritized
        {
            private $priority;

            public function __construct($priority)
            {
                $this->priority = $priority;
            }

            public function getPriority(): int
            {
                return $this->priority;
            }

            public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
            {
                $response = $handler->handle($request);
                return $response->withHeader('X-Priority', (string) $this->priority);
            }
        };
    }
}
