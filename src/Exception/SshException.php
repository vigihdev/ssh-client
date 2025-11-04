<?php

declare(strict_types=1);

namespace Vigihdev\Ssh\Exception;

use Exception;

/**
 * SshException
 *
 * Base exception class untuk semua SSH-related exceptions
 *
 */
class SshException extends Exception
{
    /**
     * Constructor untuk SshException
     *
     * @param string $message Pesan error yang jelas dan informatif
     * @param int $code Error code (default: 0)
     * @param \Throwable|null $previous Previous exception untuk exception chaining
     */
    public function __construct(
        string $message = "Terjadi kesalahan pada operasi SSH",
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
