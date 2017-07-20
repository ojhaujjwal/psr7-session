<?php
declare(strict_types=1);

namespace Ojhaujjwal\Session\Exception;

use Throwable;

class PathNotWritableException extends \Exception
{
    public function __construct($message = 'Path not writable', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
