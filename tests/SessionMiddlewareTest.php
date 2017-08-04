<?php
declare(strict_types=1);

namespace Ojhaujjwal\SessionTest;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Mockery as m;
use Ojhaujjwal\Session\Handler\HandlerInterface;
use Ojhaujjwal\Session\SessionMiddleware;
use Ojhaujjwal\Session\StorageInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class SessionMiddlewareTest extends TestCase
{
    public function testProcess(): void
    {
        $manager = m::mock('overload:Ojhaujjwal\Session\SessionManager');
        $handler = m::mock(HandlerInterface::class);
        $request = m::mock(ServerRequestInterface::class);
        $storage = m::mock(StorageInterface::class);
        $response = m::mock(ResponseInterface::class);
        $delegate = m::mock(DelegateInterface::class);

        $middleware = new SessionMiddleware($handler, []);
        $manager->shouldReceive('start')->once()->withNoArgs();
        $manager->shouldReceive('getStorage')->once()->withNoArgs()->andReturn($storage);

        $request->shouldReceive('withAttribute')
            ->once()
            ->with('sessionManager', m::type('Ojhaujjwal\Session\SessionManager'))
            ->andReturnSelf();

        $request->shouldReceive('withAttribute')
            ->once()
            ->with('session', $storage)
            ->andReturnSelf();
        $delegate->shouldReceive('process')
            ->once()
            ->with($request)
            ->andReturn($response);

        $manager->shouldReceive('close')
            ->once()
            ->with($response)
            ->andReturn($response);

        $this->assertEquals($response, $middleware->process($request, $delegate));
    }
}
