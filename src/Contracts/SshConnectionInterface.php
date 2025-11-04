<?php

declare(strict_types=1);

namespace Vigihdev\Ssh\Contracts;

interface SshConnectionInterface
{
    public function getHost(): string;
    public function getPort(): int;
    public function getUser(): string;
    public function getTimeout(): int;
}
