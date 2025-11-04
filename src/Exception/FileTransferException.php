<?php

declare(strict_types=1);

namespace Vigihdev\Ssh\Exception;

/**
 * FileTransferException
 *
 * Exception untuk kesalahan transfer file via SFTP
 *
 */
class FileTransferException extends SshException
{
    /**
     * Constructor untuk FileTransferException.
     *
     * @param string $message Pesan error file transfer yang spesifik.
     * @param int $code Error code.
     * @param \Throwable|null $previous Previous exception.
     */
    public function __construct(
        string $message = "Gagal melakukan transfer file",
        int $code = 3001,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Membuat exception untuk file tidak ditemukan.
     *
     * @param string $filePath Path file yang tidak ditemukan.
     * @return self
     */
    public static function fileNotFound(string $filePath): self
    {
        return new self("File tidak ditemukan: {$filePath}", 3002);
    }

    /**
     * Membuat exception untuk upload failed.
     *
     * @param string $localFile File lokal.
     * @param string $remoteFile File remote.
     * @return self
     */
    public static function uploadFailed(string $localFile, string $remoteFile): self
    {
        return new self("Gagal upload file {$localFile} ke {$remoteFile}", 3003);
    }

    /**
     * Membuat exception untuk download failed.
     *
     * @param string $remoteFile File remote.
     * @param string $localFile File lokal.
     * @return self
     */
    public static function downloadFailed(string $remoteFile, string $localFile): self
    {
        return new self("Gagal download file {$remoteFile} ke {$localFile}", 3004);
    }

    /**
     * Membuat exception untuk permission denied.
     *
     * @param string $filePath Path file.
     * @param string $operation Operasi yang dilakukan.
     * @return self
     */
    public static function permissionDenied(string $filePath, string $operation): self
    {
        return new self("Permission denied untuk {$operation} pada file: {$filePath}", 3005);
    }
}
