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

    public function pwd(): string;

    public function exec(string $command, ?callable $callback = null): string|bool;

    public function chdir(string $directory): bool;

    public function lists(string $directory = '.', bool $recursive = false): SftpCollection;

    public function isDir(string $path): bool;

    public function isFile(string $path): bool;

    public function fileExists(string $path): bool;

    public function getFileSize(string $remoteFile): int;

    public function downloadFile(string $remoteFile, string $localFile): bool;

    public function uploadFile(string $localFile, string $remoteFile): bool;

    public function createDirectory(string $directory, int $mode = 0755): bool;

    public function getLastError(): string;

    public function getFileContents(string $remoteFile): string|false;

    public function putFileContents(string $remoteFile, string $content): bool;

    public function put(
        string $remote_file,
        $data,
        int $mode = SFTP::SOURCE_STRING,
        int $start = -1,
        int $local_start = -1,
        $progressCallback = null
    ): bool;
}
