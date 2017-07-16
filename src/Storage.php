<?php
declare(strict_types=1);

namespace Ojhaujjwal\Session;

use ArrayIterator;
use Traversable;

final class Storage implements StorageInterface
{
    /**
     * @var array
     */
    private $attributes;

    /**
     * {@inheritdoc}
     */
    public function fromArray(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return $this->attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $key): bool
    {
        return isset($this->attributes[$key]);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $key, $default = null)
    {
        return $this->attributes[$key] ?? $default;
    }

    /**
     * {@inheritdoc}
     */
    public function put(string $key, $value): void
    {
        $this->attributes[$key] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function remove(string $key): void
    {
        unset($this->attributes[$key]);
    }

    /**
     * {@inheritdoc}
     */
    public function flush(): void
    {
        $this->attributes = [];
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        return count($this->attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset): bool
    {
        return $this->has($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value): void
    {
        $this->put($offset, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset): void
    {
        $this->remove($offset);
    }

    /**
     * Get Offset
     *
     * @param  mixed $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->offsetGet($key);
    }
    /**
     * Set Offset
     *
     * @param  mixed $key
     * @param  mixed $value
     * @return void
     */
    public function __set($key, $value): void
    {
        $this->offsetSet($key, $value);
    }
    /**
     * Isset Offset
     *
     * @param  mixed   $key
     * @return bool
     */
    public function __isset($key): bool
    {
        return $this->offsetExists($key);
    }
    /**
     * Unset Offset
     *
     * @param  mixed $key
     * @return void
     */
    public function __unset($key): void
    {
        $this->offsetUnset($key);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->attributes);
    }
}
