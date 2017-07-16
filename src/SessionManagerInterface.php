<?php
declare(strict_types=1);

namespace Ojhaujjwal\Session;

use Psr\Http\Message\ResponseInterface;

/**
 * TODO: session garbage collection
 */
interface SessionManagerInterface
{
    /**
     * Starts the session
     * Does nothing if the session has already been started.
     */
    public function start(): void;

    /**
     * Determine if the session has been started.
     *
     * @return bool
     */
    public function isStarted(): bool;

    /**
     * Write the session to cookie
     *
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function write(ResponseInterface $response): ResponseInterface;

    /**
     * Generate a new session identifier for the session.
     *
     * @param  bool  $destroy
     * @return void
     */
    public function regenerate($destroy = false): void;

    /**
     * Gets the session id
     *
     * @return string
     */
    public function getId(): string;

    /**
     * Get all of the session data.
     *
     * @return array
     */
    public function all(): array;

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
    public function get(string $key, $default = null): mixed;

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
