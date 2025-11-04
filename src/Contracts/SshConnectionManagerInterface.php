<?php

declare(strict_types=1);

namespace Vigihdev\Ssh\Contracts;

interface SshConnectionManagerInterface
{

    public function getConnection(string $name): SshConnectionConfigInterface;
    public function getAvailableServiceNames(): array;
    public function hasServiceConnection(string $name): bool;
}
