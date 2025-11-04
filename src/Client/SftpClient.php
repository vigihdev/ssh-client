<?php

declare(strict_types=1);

namespace Vigihdev\Ssh\Client;

use phpseclib3\Net\SFTP;
use RuntimeException;
use Vigihdev\Ssh\Contracts\SftpClientInterface;

final class SftpClient implements SftpClientInterface
{

    public function __construct(
        private readonly SFTP $sftp
    ) {
        if (! $sftp->isConnected()) {
            throw new RuntimeException("Sftp tidak connect");
        }
    }

    public function pwd()
    {
        return $this->sftp->pwd();
    }

    public function exec(string $command, callable $callback = null): string|bool
    {
        $sftp = $this->sftp;
        try {
            return $sftp->exec("cd {$sftp->pwd()} && {$command}", $callback);
        } catch (\Throwable $e) {
            throw new RuntimeException($e->getMessage());
        }
    }
}
