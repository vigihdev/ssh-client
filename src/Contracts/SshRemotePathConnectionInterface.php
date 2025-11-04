<?php

declare(strict_types=1);

namespace Vigihdev\Ssh\Contracts;

interface SshRemotePathConnectionInterface extends SshConnectionInterface
{
    public function getRemotePath(): string;
}
