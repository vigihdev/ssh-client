<?php

declare(strict_types=1);

namespace Vigihdev\Ssh\Contracts;

interface RemotePathInterface
{
    public function getRemotePath(): string;
}
