<?php
declare(strict_types=1);

namespace Ojhaujjwal\Session\Handler;

use Ojhaujjwal\Session\Exception;

final class FileHandler implements HandlerInterface
{
    /**
     * @var string
     */
    private $path;

    /**
     * FileSessionHandler constructor.
     * @param string $path
     * @throws Exception\PathNotWritableException
     */
    public function __construct(string $path)
    {
        if (!is_writable($path)) {
            throw new Exception\PathNotWritableException();
        }

        $this->path = $path;
    }

    /**
     * @param string $sessionId
     * @return string
     */
    private function fp(string $sessionId): string
    {
        return $this->path . '/' . $sessionId;
    }

    /**
     * {@inheritdoc}
     */
    public function read(string $sessionId)
    {
        return file_exists($this->fp($sessionId)) ? file_get_contents($this->fp($sessionId)): '';
    }

    /**
     * {@inheritdoc}
     */
    public function write(string $sessionId, string $sessionData): void
    {
        file_put_contents($this->fp($sessionId), $sessionData);
    }

    /**
     * {@inheritdoc}
     */
    public function destroy($sessionId): void
    {
        if (file_exists($this->fp($sessionId))) {
            unlink($this->fp($sessionId));
        }
    }
}
