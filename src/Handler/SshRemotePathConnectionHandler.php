<?php

declare(strict_types=1);

namespace Vigihdev\Ssh\Handler;

use Vigihdev\Ssh\Contracts\SshRemotePathConnectionInterface;

final class SshRemotePathConnectionHandler implements SshRemotePathConnectionInterface
{
    public function __construct(
        private readonly string $host,
        private readonly string $user,
        private readonly string $remotePath,
        private readonly int $port = 22,
        private readonly int $timeout = 30
    ) {}

    public function getRemotePath(): string
    {
        return $this->remotePath;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }
}
