<?php

declare(strict_types=1);

namespace Vigihdev\Ssh\Handler;

use Vigihdev\Ssh\Contracts\SshConnectionInterface;

final class SshConnectionHandler implements SshConnectionInterface
{
    public function __construct(
        private readonly string $host,
        private readonly string $user,
        private readonly int $port = 22,
        private readonly int $timeout = 30
    ) {}

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
