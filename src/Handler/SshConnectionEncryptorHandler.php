<?php

declare(strict_types=1);

namespace Vigihdev\Ssh\Handler;

use Vigihdev\Ssh\Contracts\SshConnectionEncryptorInterface;

/**
 * SshConnectionEncryptorHandler
 *
 * Class untuk handle koneksi SSH
 */
final class SshConnectionEncryptorHandler implements SshConnectionEncryptorInterface
{
    /**
     * Membuat instance baru dari SshConnectionEncryptorHandler.
     *
     * @param string $host Hostname atau IP address.
     * @param string $user Username.
     * @param string $port Port.
     * @param string $timeout Timeout dalam detik.
     */
    public function __construct(
        private readonly string $host,
        private readonly string $user,
        private readonly string $port = '22',
        private readonly string $timeout = '30'
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
     * @return string Port.
     */
    public function getPort(): string
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
    public function getTimeout(): string
    {
        return $this->timeout;
    }
}
