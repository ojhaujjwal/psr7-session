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
     * Closses the session and writes the session to cookie header
     *
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function close(ResponseInterface $response): ResponseInterface;

    /**
     * Generate a new session identifier for the session.
     *
     * @param  bool  $destroy
     * @return void
     */
    public function regenerate($destroy = true): void;

    /**
     * Gets the session id
     *
     * @return string
     */
    public function getId(): string;

    /**
     * Gets the associated session storage
     *
     * @return StorageInterface
     */
    public function getStorage(): StorageInterface;
}
