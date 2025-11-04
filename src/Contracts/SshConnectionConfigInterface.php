<?php

declare(strict_types=1);

namespace Vigihdev\Ssh\Contracts;

use Vigihdev\Ssh\Client\{SftpClient, SshClient};

interface SshConnectionConfigInterface
{

    public function getSshClient(): SshClient;
    public function getSftpClient(): SftpClient;
}
