<?php
declare(strict_types=1);

namespace Ojhaujjwal\SessionTest\Handler;

use Ojhaujjwal\Session\Exception\PathNotWritableException;
use Ojhaujjwal\Session\Handler\FileHandler;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamFile;

class FileHandlerTest extends \PHPUnit\Framework\TestCase
{
    public function testGetExceptionIfDirectoryNotWritable()
    {
        $path = vfsStream::setup('/data/session', 0400);
        $this->expectException(PathNotWritableException::class);
        new FileHandler($path->url());
    }

    public function testRead(): void
    {
        $sessionId = 'awesessid';
        $path = vfsStream::setup('/data/session');
        $handler = new FileHandler($path->url());
        $file = new vfsStreamFile($sessionId);

        $file->setContent('2v3423432v34');
        $path->addChild($file);

        $this->assertEquals('2v3423432v34', $handler->read($sessionId));
    }

    public function testWrite(): void
    {
        $sessionId = 'awesessid';
        $path = vfsStream::setup('/data/session');
        $handler = new FileHandler($path->url());
        $file = new vfsStreamFile($sessionId);

        $path->addChild($file);
        $handler->write($sessionId, 'asdfsadfc234c34534');

        $this->assertEquals('asdfsadfc234c34534', $file->getContent());
    }

    public function testDestroy()
    {
        $sessionId = 'awesessid';
        $path = vfsStream::setup('/data/session');
        $handler = new FileHandler($path->url());
        $file = new vfsStreamFile($sessionId);

        $path->addChild($file);
        $this->assertTrue($path->hasChild($sessionId));

        $handler->destroy($sessionId);
        $this->assertFalse($path->hasChild($sessionId));
    }
}
