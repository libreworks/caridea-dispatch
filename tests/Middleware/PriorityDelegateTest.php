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
namespace Caridea\Dispatch\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Generated by hand
 */
class PriorityDelegateTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers \Caridea\Dispatch\Middleware\PriorityDelegate
     */
    public function testBasic()
    {
        $request = new \Zend\Diactoros\ServerRequest();
        $response = new \Zend\Diactoros\Response();
        $handler = $this->createMock(RequestHandlerInterface::class);
        $middleware = $this->createMock(MiddlewareInterface::class);
        $middleware->expects($this->once())->method('process')->willReturn($response);
        $priority = 300;
        $object = new PriorityDelegate($middleware, $priority);
        $this->assertEquals($priority, $object->getPriority());
        $this->assertSame($response, $object->process($request, $handler));
        $this->verifyMockObjects();
    }
}
