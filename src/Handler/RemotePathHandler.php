<?php

declare(strict_types=1);

namespace Vigihdev\Ssh\Handler;

use Vigihdev\Ssh\Contracts\RemotePathInterface;

/**
 * RemotePathHandler
 *
 * Class untuk handle remote path
 */
final class RemotePathHandler implements RemotePathInterface
{

    /**
     * Membuat instance baru dari RemotePathHandler.
     *
     * @param string $remotePath Path remote direktori.
     */
    public function __construct(
        private readonly string $remotePath
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
}
