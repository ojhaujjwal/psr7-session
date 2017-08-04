<?php
declare(strict_types=1);

namespace Ojhaujjwal\SessionTest;

use Ojhaujjwal\Session\Storage;
use PHPUnit\Framework\TestCase;

class StorageTest extends TestCase
{
    /**
     * @var Storage
     */
    private $storage;

    public function setUp(): void
    {
        $this->storage = new Storage();
        parent::setUp();
    }

    public function testFromArray(): void
    {
        $this->storage->fromArray(['a' => 'def']);
        $this->assertEquals(['a' => 'def'], $this->storage->toArray());
    }

    public function testAddToStorageAndGetFromStorage(): void
    {
        $this->storage->fromArray(['a' => 'def']);
        $this->storage['eaf'] = 'a';
        $this->storage->def = 'b';

        $this->assertTrue($this->storage->has('a'));
        $this->assertTrue(isset($this->storage->a));
        $this->assertEquals('a', $this->storage->eaf);
        $this->assertEquals('b', $this->storage['def']);
        $this->assertEquals(['a' => 'def', 'eaf' => 'a', 'def' => 'b'], $this->storage->toArray());
        $this->assertCount(3, $this->storage);
    }

    public function testRemoveKey(): void
    {
        $this->storage->fromArray(['a' => 'def']);
        $this->storage['qwerty'] = 'zxcvb';
        $this->assertCount(2, $this->storage);

        unset($this->storage->qwerty);
        $this->assertCount(1, $this->storage);

        $this->storage['uiop'] = 'hjkl';
        $this->assertCount(2, $this->storage);

        unset($this->storage['uiop']);
        $this->assertCount(1, $this->storage);



        $this->storage['rtyu'] = 'fghj';
        $this->assertCount(2, $this->storage);

        $this->storage->remove('rtyu');
        $this->assertCount(1, $this->storage);
    }

    public function testFlush(): void
    {
        $this->storage->fromArray(['a' => 'def']);
        $this->storage['eaf'] = 'a';
        $this->assertCount(2, $this->storage);

        $this->storage->flush();
        $this->assertEmpty($this->storage->toArray());
    }

    public function testGetIterator()
    {
        $this->storage->fromArray(['a' => 'def']);
        $this->storage['eaf'] = 'a';
        foreach ($this->storage as $key => $value) {
            $this->assertEquals($this->storage->{$key}, $value);
        }
    }
}
