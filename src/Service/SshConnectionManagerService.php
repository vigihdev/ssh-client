<?php

declare(strict_types=1);

namespace Vigihdev\Ssh\Service;

use InvalidArgumentException;
use Vigihdev\Ssh\Contracts\{SshConnectionConfigInterface, SshConnectionManagerInterface};

final class SshConnectionManagerService implements SshConnectionManagerInterface
{

    /**
     * @param array<string,SshConnectionConfigInterface> $sshConfigs
     * @return void
     */
    public function __construct(
        private readonly array $sshConfigs
    ) {}


    public function getConnection(string $name): SshConnectionConfigInterface
    {
        if (! $this->hasServiceConnection($name)) {
            throw new InvalidArgumentException("Connection {$name} tidak tersedia");
        }

        return $this->sshConfigs[$name];
    }

    public function getAvailableServiceNames(): array
    {
        return array_keys($this->sshConfigs);
    }

    public function hasServiceConnection(string $name): bool
    {
        $ssh = $this->sshConfigs[$name] ?? null;
        return $ssh instanceof SshConnectionConfigInterface;
    }
}
