<?php

declare(strict_types=1);

namespace Vigihdev\Ssh\Exception;

/**
 * DirectoryException
 *
 * Exception untuk kesalahan operasi directory via SFTP
 *
 */
class DirectoryException extends SshException
{
    /**
     * Constructor untuk DirectoryException
     *
     * @param string $message Pesan error directory operation yang spesifik
     * @param int $code Error code (default: 4001)
     * @param \Throwable|null $previous Previous exception
     */
    public function __construct(
        string $message = "Gagal melakukan operasi directory",
        int $code = 4001,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Create exception untuk directory tidak ditemukan
     *
     * @param string $dirPath Path directory yang tidak ditemukan
     * @return self
     */
    public static function directoryNotFound(string $dirPath): self
    {
        return new self("Directory tidak ditemukan: {$dirPath}", 4002);
    }

    /**
     * Create exception untuk gagal membuat directory
     *
     * @param string $dirPath Path directory yang gagal dibuat
     * @return self
     */
    public static function createFailed(string $dirPath): self
    {
        return new self("Gagal membuat directory: {$dirPath}", 4003);
    }

    /**
     * Create exception untuk gagal menghapus directory
     *
     * @param string $dirPath Path directory yang gagal dihapus
     * @return self
     */
    public static function deleteFailed(string $dirPath): self
    {
        return new self("Gagal menghapus directory: {$dirPath}", 4004);
    }

    /**
     * Create exception untuk directory tidak kosong
     *
     * @param string $dirPath Path directory yang tidak kosong
     * @return self
     */
    public static function notEmpty(string $dirPath): self
    {
        return new self("Directory tidak kosong dan tidak dapat dihapus: {$dirPath}", 4005);
    }

    /**
     * Create exception untuk gagal mengubah directory
     *
     * @param string $dirPath Path directory yang gagal diakses
     * @return self
     */
    public static function changeFailed(string $dirPath): self
    {
        return new self("Gagal mengubah ke directory: {$dirPath}", 4006);
    }
}
