<?php

declare(strict_types=1);

namespace Vigihdev\Ssh\Exception;

/**
 * CommandException
 *
 * Exception untuk kesalahan eksekusi command SSH
 *
 */
class CommandException extends SshException
{
    /**
     * Constructor untuk CommandException.
     *
     * @param string $message Pesan error command execution yang spesifik.
     * @param int $code Error code.
     * @param \Throwable|null $previous Previous exception.
     */
    public function __construct(
        string $message = "Gagal mengeksekusi command SSH",
        int $code = 2001,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Membuat exception untuk command execution failed.
     *
     * @param string $command Command yang gagal dieksekusi.
     * @param int $exitCode Exit code dari command.
     * @param string $errorOutput Error output dari command.
     * @return self
     */
    public static function executionFailed(string $command, int $exitCode, string $errorOutput = ''): self
    {
        $message = "Command gagal dieksekusi: {$command} (Exit code: {$exitCode})";
        if (!empty($errorOutput)) {
            $message .= " - Error: {$errorOutput}";
        }

        return new self($message, 2002);
    }

    /**
     * Membuat exception untuk command timeout.
     *
     * @param string $command Command yang timeout.
     * @param int $timeout Timeout dalam detik.
     * @return self
     */
    public static function timeout(string $command, int $timeout): self
    {
        return new self(
            "Command timeout: {$command} setelah {$timeout} detik",
            2003
        );
    }

    /**
     * Membuat exception untuk command tidak ditemukan.
     *
     * @param string $command Command yang tidak ditemukan.
     * @return self
     */
    public static function commandNotFound(string $command): self
    {
        return new self(
            "Command tidak ditemukan di remote server: {$command}",
            2004
        );
    }
}
