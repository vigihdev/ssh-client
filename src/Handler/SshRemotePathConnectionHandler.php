<?php

declare(strict_types=1);

namespace Vigihdev\Ssh\Handler;

use Vigihdev\Ssh\Contracts\SshRemotePathConnectionInterface;

/**
 * SshRemotePathConnectionHandler
 *
 * Class untuk handle koneksi SSH dengan remote path
 */
final class SshRemotePathConnectionHandler implements SshRemotePathConnectionInterface
{
    /**
     * Membuat instance baru dari SshRemotePathConnectionHandler.
     *
     * @param string $host Hostname atau IP address.
     * @param string $user Username.
     * @param string $remotePath Path remote direktori.
     * @param int $port Port.
     * @param int $timeout Timeout dalam detik.
     */
    public function __construct(
        private readonly string $host,
        private readonly string $user,
        private readonly string $remotePath,
        private readonly int $port = 22,
        private readonly int $timeout = 30
    ) {}

    /**
     * Mendapatkan remote path.
     *
     * @return string Remote path.
     */
    public function getRemotePath(): string
    {
        return $this->remotePath;
    }

    /**
     * Mendapatkan host.
     *
     * @return string Host.
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * Mendapatkan port.
     *
     * @return int Port.
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * Mendapatkan user.
     *
     * @return string User.
     */
    public function getUser(): string
    {
        return $this->user;
    }

    /**
     * Mendapatkan timeout.
     *
     * @return int Timeout.
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }
}
