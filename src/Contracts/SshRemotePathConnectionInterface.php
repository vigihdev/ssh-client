<?php

declare(strict_types=1);

namespace Vigihdev\Ssh\Contracts;

/**
 * SshRemotePathConnectionInterface
 *
 * Interface untuk koneksi SSH dengan remote path
 */
interface SshRemotePathConnectionInterface extends SshConnectionInterface
{
    /**
     * Mendapatkan remote path.
     *
     * @return string Remote path.
     */
    public function getRemotePath(): string;
}
