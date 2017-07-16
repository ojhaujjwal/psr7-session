<?php
declare(strict_types=1);

namespace Ojhaujjwal\Session;

use ArrayAccess;
use Countable;
use IteratorAggregate;

interface StorageInterface extends IteratorAggregate, ArrayAccess, Countable
{
    /**
     * @param array $attributes
     * @return void
     */
    public function fromArray(array $attributes): void;

    /**
     * Get all of the session data.
     *
     * @return array
     */
    public function toArray(): array;

    /**
     * Check if key exists
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool;

    /**
     * Check if key exists
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * Put a key / value pair or array of key / value pairs in the session.
     *
     * @param string $key
     * @param mixed  $value
     * @return void
     */
    public function put(string $key, $value): void;

    /**
     * Remove an item from the session.
     *
     * @param string $key
     * @return void
     */
    public function remove(string $key): void;

    /**
     * Remove all of the items from the session.
     *
     * @return void
     */
    public function flush(): void;
}
