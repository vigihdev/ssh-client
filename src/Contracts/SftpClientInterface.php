<?php

declare(strict_types=1);

namespace Vigihdev\Ssh\Contracts;

use phpseclib3\Net\SFTP;
use Vigihdev\Ssh\Collections\SftpCollection;

/**
 * SftpClientInterface
 *
 * Interface untuk SFTP client
 */
interface SftpClientInterface
{
    /**
     * Return the current remote working directory.
     *
     * @return string Current working directory path on the remote server.
     */
    public function pwd(): string;

    /**
     * Execute a command on the remote server.
     *
     * @param string $command The command to execute.
     * @param callable|null $callback Optional callback invoked with incremental output chunks: function(string $chunk): void.
     * @return string|bool Full command output as string on success, or false on failure.
     */
    public function exec(string $command, ?callable $callback = null): string|bool;

    /**
     * Change the remote working directory.
     *
     * @param string $directory Target directory path.
     * @return bool True on success, false on failure.
     */
    public function chdir(string $directory): bool;

    /**
     * List files and directories at the given remote path.
     *
     * @param string $directory Directory to list (default: '.').
     * @param bool $recursive Whether to list recursively.
     * @return SftpCollection Collection of directory entries.
     */
    public function lists(string $directory = '.', bool $recursive = false): SftpCollection;

    /**
     * Check if the given remote path is a directory.
     *
     * @param string $path Remote path to check.
     * @return bool True if path is a directory, false otherwise.
     */
    public function isDir(string $path): bool;

    /**
     * Check if the given remote path is a regular file.
     *
     * @param string $path Remote path to check.
     * @return bool True if path is a file, false otherwise.
     */
    public function isFile(string $path): bool;

    /**
     * Check if a file or directory exists at the given remote path.
     *
     * @param string $path Remote path to check.
     * @return bool True if the path exists, false otherwise.
     */
    public function fileExists(string $path): bool;

    /**
     * Get the size of a remote file in bytes.
     *
     * @param string $remoteFile Remote file path.
     * @return int File size in bytes.
     */
    public function getFileSize(string $remoteFile): int;

    /**
     * Download a remote file to a local path.
     *
     * @param string $remoteFile Remote file path.
     * @param string $localFile Local destination path.
     * @return bool True on success, false on failure.
     */
    public function downloadFile(string $remoteFile, string $localFile): bool;

    /**
     * Upload a local file to the remote server.
     *
     * @param string $localFile Local source file path.
     * @param string $remoteFile Remote destination path.
     * @return bool True on success, false on failure.
     */
    public function uploadFile(string $localFile, string $remoteFile): bool;

    /**
     * Create a directory on the remote server.
     *
     * @param string $directory Directory path to create.
     * @param int $mode Permissions mode (default 0755).
     * @return bool True on success, false on failure.
     */
    public function createDirectory(string $directory, int $mode = 0755): bool;

    /**
     * Return the last error message from the SFTP client.
     *
     * @return string Last error message, or empty string if none.
     */
    public function getLastError(): string;

    /**
     * Retrieve the contents of a remote file as a string.
     *
     * @param string $remoteFile Remote file path.
     * @return string|false File contents as string on success, or false on failure.
     */
    public function getFileContents(string $remoteFile): string|false;

    /**
     * Write the given string content to a remote file.
     *
     * @param string $remoteFile Remote file path.
     * @param string $content Content to write.
     * @return bool True on success, false on failure.
     */
    public function putFileContents(string $remoteFile, string $content): bool;

    /**
     * Generic put operation matching phpseclib3 SFTP::put signature.
     *
     * @param string $remote_file Destination remote file path.
     * @param mixed $data Data to write (string, resource, or local filename depending on $mode).
     * @param int $mode One of SFTP::SOURCE_* constants (default SFTP::SOURCE_STRING).
     * @param int $start Remote start offset, or -1 to append/overwrite as per phpseclib behavior.
     * @param int $local_start Local start offset when using a local file, or -1.
     * @param callable|null $progressCallback Optional progress callback function(int $sentBytes, int $totalBytes): void.
     * @return bool True on success, false on failure.
     */
    public function put(
        string $remote_file,
        $data,
        int $mode = SFTP::SOURCE_STRING,
        int $start = -1,
        int $local_start = -1,
        $progressCallback = null
    ): bool;
}
