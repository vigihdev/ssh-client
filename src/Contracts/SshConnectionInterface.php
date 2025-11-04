<?php

declare(strict_types=1);

namespace Vigihdev\Ssh\Contracts;

/**
 * SshConnectionInterface
 *
 * Interface untuk koneksi SSH
 */
interface SshConnectionInterface
{
    /**
     * Mendapatkan host.
     *
     * @return string Host.
     */
    public function getHost(): string;

    /**
     * Mendapatkan port.
     *
     * @return int Port.
     */
    public function getPort(): int;

    /**
     * Mendapatkan user.
     *
     * @return string User.
     */
    public function getUser(): string;

    /**
     * Mendapatkan timeout.
     *
     * @return int Timeout.
     */
    public function getTimeout(): int;
}
