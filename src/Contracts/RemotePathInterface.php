<?php

declare(strict_types=1);

namespace Vigihdev\Ssh\Contracts;

/**
 * RemotePathInterface
 *
 * Interface untuk remote path
 */
interface RemotePathInterface
{
    /**
     * Mendapatkan remote path.
     *
     * @return string Remote path.
     */
    public function getRemotePath(): string;
}
