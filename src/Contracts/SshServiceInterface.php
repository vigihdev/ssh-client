<?php

declare(strict_types=1);

namespace Vigihdev\Ssh\Contracts;

interface SshServiceInterface
{

    public function execute(string $command, callable $callback = null): string|bool;
}
