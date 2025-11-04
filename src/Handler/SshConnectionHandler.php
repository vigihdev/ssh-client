<?php

declare(strict_types=1);

namespace Vigihdev\Ssh\Handler;

use Vigihdev\Ssh\Contracts\SshConnectionInterface;

/**
 * SshConnectionHandler
 *
 * Class untuk handle koneksi SSH
 */
final class SshConnectionHandler implements SshConnectionInterface
{
    /**
     * Membuat instance baru dari SshConnectionHandler.
     *
     * @param string $host Hostname atau IP address.
     * @param string $user Username.
     * @param int $port Port.
     * @param int $timeout Timeout dalam detik.
     */
    public function __construct(
        private readonly string $host,
        private readonly string $user,
        private readonly int $port = 22,
        private readonly int $timeout = 30
    ) {}

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
