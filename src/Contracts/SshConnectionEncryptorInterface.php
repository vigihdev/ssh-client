<?php

declare(strict_types=1);

namespace Vigihdev\Ssh\Contracts;

/**
 * SshConnectionInterface
 *
 * Interface untuk koneksi SSH
 */
interface SshConnectionEncryptorInterface
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
     * @return string Port.
     */
    public function getPort(): string;

    /**
     * Mendapatkan user.
     *
     * @return string User.
     */
    public function getUser(): string;

    /**
     * Mendapatkan timeout.
     *
     * @return string Timeout.
     */
    public function getTimeout(): string;
}
