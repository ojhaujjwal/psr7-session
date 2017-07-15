<?php
declare(strict_types=1);

namespace Ojhaujjwal\Session;

final class FileSessionHandler implements SessionHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function read(string $sessionId)
    {
        //TODO: implement
    }

    /**
     * {@inheritdoc}
     */
    public function write(string $sessionId, string $sessionData): void
    {
        //TODO: implement
    }

    /**
     * {@inheritdoc}
     */
    public function destroy($sessionId): void
    {
        //TODO: implement
    }
}
