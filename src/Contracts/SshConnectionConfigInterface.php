<?php

declare(strict_types=1);

namespace Vigihdev\Ssh\Contracts;

use Vigihdev\Ssh\Client\{SftpClient, SshClient};

/**
 * SshConnectionConfigInterface
 *
 * Interface untuk konfigurasi koneksi SSH
 */
interface SshConnectionConfigInterface
{

    /**
     * Mendapatkan SshClient.
     *
     * @return SshClient Instance dari SshClient.
     */
    public function getSshClient(): SshClient;

    /**
     * Mendapatkan SftpClient.
     *
     * @return SftpClient Instance dari SftpClient.
     */
    public function getSftpClient(): SftpClient;
}
