<?php
declare(strict_types=1);

namespace Ojhaujjwal\Session\Handler;

interface HandlerInterface
{
    /**
     * @param string $sessionId
     * @return mixed
     */
    public function read(string $sessionId);

    /**
     * @param string $sessionId
     * @param string $sessionData
     * @return void
     */
    public function write(string $sessionId, string $sessionData): void;

    /**
     * Destroy a session
     *
     * @param string $sessionId The session ID being destroyed.
     * @return void
     */
    public function destroy($sessionId): void;
}
