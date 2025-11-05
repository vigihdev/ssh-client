<?php

declare(strict_types=1);

namespace Vigihdev\Ssh\Client;

use phpseclib3\Net\SFTP;
use RuntimeException;
use Vigihdev\Ssh\Collections\SftpCollection;
use Vigihdev\Ssh\Contracts\SftpClientInterface;
use Vigihdev\Ssh\Exception\DirectoryException;

/**
 * SftpClient
 *
 * Class untuk berinteraksi dengan SFTP server
 */
final class SftpClient implements SftpClientInterface
{
    /**
     * @var SFTP Instance dari SFTP client.
     */
    private readonly SFTP $sftp;

    /**
     * Membuat instance baru dari SftpClient.
     *
     * @param SFTP $sftp Instance dari SFTP client yang sudah terhubung.
     * @throws RuntimeException Jika SFTP client tidak terhubung.
     */
    public function __construct(
        SFTP $sftp
    ) {
        if (! $sftp->isConnected()) {
            throw new RuntimeException("Sftp tidak connect");
        }

        $this->sftp = $sftp;
    }

    /**
     * Mendapatkan path direktori saat ini.
     *
     * @return mixed Path direktori saat ini.
     */
    public function pwd()
    {
        return $this->sftp->pwd();
    }

    /**
     * Mengeksekusi perintah di remote server.
     *
     * @param string $command Perintah yang akan dieksekusi.
     * @param callable|null $callback Callback untuk menangani output.
     * @return string|boolean Hasil dari eksekusi perintah.
     * @throws RuntimeException Jika terjadi error saat eksekusi.
     */
    public function exec(string $command, callable $callback = null): string|bool
    {
        $sftp = $this->sftp;
        try {
            return $sftp->exec("cd {$sftp->pwd()} && {$command}", $callback);
        } catch (\Throwable $e) {
            throw new RuntimeException($e->getMessage());
        }
    }


    /**
     * Change directory
     *
     * @param string $directory Target directory path
     * @return bool True jika berhasil change directory
     */
    public function chdir(string $directory): bool
    {
        return $this->sftp->chdir($directory);
    }

    public function lists(string $directory = '.', bool $recursive = false): SftpCollection
    {
        $items = $this->sftp->nlist($directory, $recursive) ?? [];
        return new SftpCollection(
            sftp: $this->sftp,
            items: $items
        );
    }

    /**
     * Check if path is directory
     *
     * @param string $path Path to check
     * @return bool True jika path adalah directory
     */
    public function isDir(string $path): bool
    {
        return $this->sftp->is_dir($path);
    }

    /**
     * Check if path is a file
     *
     * @param string $path Path to check
     * @return bool True if path is a file
     */
    public function isFile(string $path): bool
    {
        return $this->sftp->is_file($path);
    }

    /**
     * Check if file exists
     *
     * @param string $path File path to check
     * @return bool True jika file exists
     */
    public function fileExists(string $path): bool
    {
        return $this->sftp->file_exists($path);
    }

    /**
     * Get file size
     *
     * @param string $remoteFile Remote file path
     * @return int File size in bytes
     */
    public function getFileSize(string $remoteFile): int
    {
        return $this->sftp->filesize($remoteFile);
    }

    /**
     * Download file from remote to local
     *
     * @param string $remoteFile Remote file path
     * @param string $localFile Local file path
     * @return bool True jika download berhasil
     */
    public function downloadFile(string $remoteFile, string $localFile): bool
    {
        return $this->sftp->get($remoteFile, $localFile);
    }

    /**
     * Upload file from local to remote
     *
     * @param string $localFile Local file path
     * @param string $remoteFile Remote file path
     * @return bool True jika upload berhasil
     */
    public function uploadFile(string $localFile, string $remoteFile): bool
    {
        return $this->sftp->put($remoteFile, $localFile, SFTP::SOURCE_LOCAL_FILE);
    }

    /**
     * Create directory
     *
     * @param string $directory Directory path to create
     * @param int $mode Directory permissions (default: 0755)
     * @return bool True jika directory berhasil dibuat
     * @throws DirectoryException Jika directory sudah ada
     */
    public function createDirectory(string $directory, int $mode = 0755): bool
    {
        if ($this->sftp->is_dir($directory)) {
            throw DirectoryException::notEmpty(
                $this->pwd() . DIRECTORY_SEPARATOR . $directory
            );
        }

        return $this->sftp->mkdir($directory, $mode, true); // recursive
    }

    /**
     * Get last error message
     *
     * @return string Last error message
     */
    public function getLastError(): string
    {
        return $this->sftp->getLastError();
    }

    /**
     * Get file contents as string
     *
     * @param string $remoteFile Remote file path
     * @return string|false File contents atau false jika gagal
     */
    public function getFileContents(string $remoteFile): string|false
    {
        return $this->sftp->get($remoteFile);
    }

    /**
     * Put string content to remote file
     *
     * @param string $remoteFile Remote file path
     * @param string $content Content to write
     * @return bool True jika berhasil write content
     */
    public function putFileContents(string $remoteFile, string $content): bool
    {
        return $this->sftp->put($remoteFile, $content);
    }

    /**
     *
     * SftpClient - Class for interacting with SFTP servers
     * 
     * Provides functionality for:
     * - File operations (upload, download, read, write)
     * - Directory operations (create, change, list)
     * - File/directory checks (exists, is file/dir)
     * - Remote command execution
     * - Error handling
     * 
     * @param string $remote_file
     * @param string|resource $data
     * @param int $mode
     * @param int $start
     * @param int $local_start
     * @param callable|null $progressCallback
     * @throws \UnexpectedValueException on receipt of unexpected packets
     * @throws \BadFunctionCallException if you're uploading via a callback and the callback function is invalid
     * @throws FileNotFoundException if you're uploading via a file and the file doesn't exist
     * @return bool
     */
    public function put(
        string $remote_file,
        $data,
        int $mode = SFTP::SOURCE_STRING,
        int $start = -1,
        int $local_start = -1,
        $progressCallback = null
    ): bool {
        return $this->sftp->put($remote_file, $data, $mode, $start, $local_start, $progressCallback);
    }
}
