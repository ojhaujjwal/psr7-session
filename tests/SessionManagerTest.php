<?php
declare(strict_types=1);

namespace Ojhaujjwal\SessionTest;

use Mockery as m;
use Ojhaujjwal\Session\Handler\HandlerInterface;
use Ojhaujjwal\Session\SessionManager;
use Ojhaujjwal\Session\Storage;
use Ojhaujjwal\Session\StorageInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class SessionManagerTest extends TestCase
{
    /** @var m\MockInterface */
    private $handler;
    /** @var m\MockInterface */
    private $request;
    /** @var m\MockInterface */
    private $storage;
    /** @var  SessionManager */
    private $manager;

    public function setUp(): void
    {
        $this->handler = m::mock(HandlerInterface::class);
        $this->request = m::mock(ServerRequestInterface::class);
        $this->storage = m::mock(StorageInterface::class);
        $this->manager = new SessionManager($this->handler, $this->request, [
            'name' => 'aws_cookie',
            'cookie' => [
                'domain' => 'app1.com',
                'secure_only' => false,
            ]
        ], $this->storage);
        parent::setUp();
    }

    public function testItShouldUseDefaultStorageIfNotProvided(): void
    {
        $manager = new SessionManager($this->handler, $this->request, [
            'name' => 'aws_cookie',
            'cookie' => [
                'domain' => 'app1.com',
                'secure_only' => false,
            ]
        ]);
        $this->assertInstanceOf(Storage::class, $manager->getStorage());
    }

    private function startDefaultSession(): void
    {
        $this->request->shouldReceive('getCookieParams')
            ->once()
            ->withNoArgs()
            ->andReturn(['aws_cookie' => '344a']);


        $this->handler->shouldReceive('read')
            ->once()
            ->with('344a')
            ->andReturn(null);

        $this->storage->shouldReceive('fromArray')
            ->once()
            ->with([]);

        $this->manager->start();
    }

    public function testItShouldStartSessionIfNotAlreadyStarted(): void
    {
        $this->assertFalse($this->manager->isStarted());
        $this->startDefaultSession();
        $this->assertTrue($this->manager->isStarted());
        $this->manager->start();
    }

    public function testItShouldStartSession(): void
    {
        $data = ['abc' => 'def',];

        $this->request->shouldReceive('getCookieParams')
            ->once()
            ->withNoArgs()
            ->andReturn([]);


        $this->handler->shouldReceive('read')
            ->once()
            ->with(m::on(function ($sessionId) {
                $this->assertEquals(SessionManager::DEFAULT_SID_LENGTH, strlen($sessionId));
                return true;
            }))
            ->andReturn(serialize($data));

        $this->storage->shouldReceive('fromArray')
            ->once()
            ->with($data);

        $this->assertFalse($this->manager->isStarted());
        $this->manager->start();
        $this->assertTrue($this->manager->isStarted());
    }

    public function testItShouldRegenerateWithoutDestroying(): void
    {
        $this->handler->shouldReceive('destroy')
            ->never();
        $this->assertNull($this->manager->regenerate(false));
    }

    public function testItShouldRegenerateAndDestroy(): void
    {
        $this->startDefaultSession();
        $this->handler->shouldReceive('destroy')
            ->with('344a');
        $this->assertNull($this->manager->regenerate());
        $this->assertEquals(SessionManager::DEFAULT_SID_LENGTH, strlen($this->manager->getId()));
    }

    public function testItShouldNotRegenerateIfSessionNotStarted(): void
    {
        $this->handler->shouldReceive('destroy')
            ->never();
        $this->expectException(\TypeError::class);
        $this->manager->getId();
    }

    public function testItShouldNotAddHeaderDuringCloseIfSessionNotStarted(): void
    {
        $response = m::mock(ResponseInterface::class);

        $response->shouldReceive('withHeader')
            ->never();

        $this->assertEquals($response, $this->manager->close($response));
    }

    public function testItShouldCloseSession(): void
    {
        $response = m::mock(ResponseInterface::class);
        $newResponse = m::mock(ResponseInterface::class);
        $data = ['c' => 'gh'];

        $this->storage->shouldReceive('toArray')
            ->once()
            ->withNoArgs()
            ->andReturn($data);

        $this->handler->shouldReceive('write')
            ->once()
            ->with('344a', serialize($data));

        $response->shouldReceive('withHeader')
            ->once()
            ->with('Set-Cookie', m::type('string'))
            ->andReturn($newResponse);

        $this->startDefaultSession();
        $this->assertTrue($this->manager->isStarted());
        $this->assertEquals($newResponse, $this->manager->close($response));
        $this->assertFalse($this->manager->isStarted());
    }

    public function tearDown(): void
    {
        m::close();
        parent::tearDown();
    }
}
