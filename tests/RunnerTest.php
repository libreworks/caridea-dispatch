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
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @covers \Caridea\Dispatch\Runner
 */
class RunnerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers \Caridea\Dispatch\Runner
     */
    public function testRun()
    {
        $queue = [
            new class() implements MiddlewareInterface {
            public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
            {
                $a = $request->withAttribute('foo', 'bar');
                $r = $handler->handle($a);
                \PHPUnit\Framework\TestCase::assertTrue($r->hasHeader('X-Out-Second'));
                return $r->withHeader('X-Out-Third', '1');
            }
            },
            new class() implements MiddlewareInterface {
                public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
                {
                    \PHPUnit\Framework\TestCase::assertSame('bar', $request->getAttribute('foo'));
                    $a = $request->withAttribute('bar', 'baz');
                    $r = $handler->handle($a);
                    \PHPUnit\Framework\TestCase::assertTrue($r->hasHeader('X-Out-First'));
                    return $r->withHeader('X-Out-Second', '1');
                }
            },
            new class() implements MiddlewareInterface {
                public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
                {
                    \PHPUnit\Framework\TestCase::assertSame('baz', $request->getAttribute('bar'));
                    $a = $request->withAttribute('baz', 'biz');
                    $r = $handler->handle($a);
                    return $r->withHeader('X-Out-First', '1');
                }
            },
        ];
        $request = new \Zend\Diactoros\ServerRequest();
        $response = new \Zend\Diactoros\Response();
        $runner = new Runner($queue, $response);
        $out = $runner->handle($request);
        $this->assertEquals("1", $out->getHeaderLine('X-Out-Third'));
        $out = $runner->handle($request);
        $this->assertEquals("1", $out->getHeaderLine('X-Out-Third'));
    }

    /**
     * @covers \Caridea\Dispatch\Runner
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Value must be an instance of MiddlewareInterface
     */
    public function testInvalidType()
    {
        new Runner(['foo']);
    }

    /**
     * @covers \Caridea\Dispatch\Runner
     * @expectedException \LengthException
     * @expectedExceptionMessage You must specify at least one middleware or a ResponseInterface
     */
    public function testNoMiddleware()
    {
        new Runner([]);
    }

    /**
     * @covers \Caridea\Dispatch\Runner
     */
    public function testNoMiddleware2()
    {
        $response = new \Zend\Diactoros\Response();
        $runner = new Runner([], $response);
        $this->assertNotNull($runner);
    }

    /**
     * @covers \Caridea\Dispatch\Runner
     * @expectedException \UnderflowException
     * @expectedExceptionMessage No middleware remain
     */
    public function testNoMiddlewareRemain()
    {
        $queue = [
            new class() implements MiddlewareInterface {
            public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
            {
                $a = $request->withAttribute('foo', 'bar');
                $r = $handler->handle($a);
                return $r->withHeader('X-Out-First', '1');
            }
            },
        ];
        $request = new \Zend\Diactoros\ServerRequest();
        $response = new \Zend\Diactoros\Response();
        $runner = new Runner($queue);
        $runner->handle($request);
    }

    /**
     * @covers \Caridea\Dispatch\Runner
     */
    public function testMiddleware()
    {
        $queue = [
            new class() implements MiddlewareInterface {
            public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
            {
                $a = $request->withAttribute('foo', 'bar');
                $r = $handler->handle($a);
                \PHPUnit\Framework\TestCase::assertTrue($r->hasHeader('X-Out-Second'));
                return $r->withHeader('X-Out-Third', '1');
            }
            },
            new class() implements MiddlewareInterface {
                public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
                {
                    \PHPUnit\Framework\TestCase::assertSame('bar', $request->getAttribute('foo'));
                    $a = $request->withAttribute('bar', 'baz');
                    $r = $handler->handle($a);
                    \PHPUnit\Framework\TestCase::assertTrue($r->hasHeader('X-Out-First'));
                    return $r->withHeader('X-Out-Second', '1');
                }
            }
        ];
        $runner = new Runner($queue);
        $queue2 = [
            new class() implements MiddlewareInterface {
            public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
            {
                $r = $handler->handle($request);
                \PHPUnit\Framework\TestCase::assertTrue($r->hasHeader('X-Out-Third'));
                return $r->withHeader('X-Out-Fourth', '1');
            }
            },
            $runner,
            new class() implements MiddlewareInterface {
                public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
                {
                    \PHPUnit\Framework\TestCase::assertSame('baz', $request->getAttribute('bar'));
                    $a = $request->withAttribute('baz', 'biz');
                    $r = $handler->handle($a);
                    return $r->withHeader('X-Out-First', '1');
                }
            }
        ];
        $request = new \Zend\Diactoros\ServerRequest();
        $response = new \Zend\Diactoros\Response();
        $runner2 = new Runner($queue2, $response);
        $out = $runner2->handle($request);
        $this->assertEquals("1", $out->getHeaderLine('X-Out-Fourth'));
    }
}
