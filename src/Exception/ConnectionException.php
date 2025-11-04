<?php

declare(strict_types=1);

namespace Vigihdev\Ssh\Exception;

/**
 * ConnectionException
 *
 * Exception untuk kesalahan koneksi SSH
 *
 */
class ConnectionException extends SshException
{
    /**
     * Constructor untuk ConnectionException.
     *
     * @param string $message Pesan error koneksi yang spesifik.
     * @param int $code Error code.
     * @param \Throwable|null $previous Previous exception.
     */
    public function __construct(
        string $message = "Gagal melakukan koneksi SSH",
        int $code = 1001,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Membuat exception untuk koneksi timeout.
     *
     * @param string $host Host yang dihubungi.
     * @param int $port Port yang digunakan.
     * @param int $timeout Timeout dalam detik.
     * @return self
     */
    public static function timeout(string $host, int $port = 22, int $timeout = 30): self
    {
        return new self(
            "Koneksi timeout ke {$host}:{$port} setelah {$timeout} detik",
            1002
        );
    }

    /**
     * Membuat exception untuk authentication failed.
     *
     * @param string $host Host yang dihubungi.
     * @param string $username Username yang digunakan.
     * @return self
     */
    public static function authenticationFailed(string $host, string $username): self
    {
        return new self(
            "Autentikasi gagal untuk user '{$username}' di host '{$host}'",
            1003
        );
    }

    /**
     * Membuat exception untuk host tidak ditemukan.
     *
     * @param string $host Host yang tidak ditemukan.
     * @return self
     */
    public static function hostNotFound(string $host): self
    {
        return new self(
            "Host tidak ditemukan: {$host}",
            1004
        );
    }

    /**
     * Membuat exception untuk port tidak tersedia.
     *
     * @param string $host Host yang dihubungi.
     * @param int $port Port yang tidak tersedia.
     * @return self
     */
    public static function portNotAvailable(string $host, int $port): self
    {
        return new self(
            "Port {$port} tidak tersedia di host {$host}",
            1005
        );
    }

    /**
     * Membuat exception untuk private key tidak valid.
     *
     * @param string $keyPath Path ke private key.
     * @return self
     */
    public static function invalidPrivateKey(string $keyPath): self
    {
        return new self(
            "Private key tidak valid atau tidak dapat dibaca: {$keyPath}",
            1006
        );
    }
}
