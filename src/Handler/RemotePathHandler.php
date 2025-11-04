<?php

declare(strict_types=1);

namespace Vigihdev\Ssh\Handler;

use Vigihdev\Ssh\Contracts\RemotePathInterface;

final class RemotePathHandler implements RemotePathInterface
{

    public function __construct(
        private readonly string $remotePath
    ) {}

    public function getRemotePath(): string
    {
        return $this->remotePath;
    }
}
