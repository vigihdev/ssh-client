<?php

declare(strict_types=1);

namespace Vigihdev\Ssh\Client;

use phpseclib3\Net\SSH2;
use RuntimeException;
use Vigihdev\Ssh\Contracts\{RemotePathInterface, SshClientInterface};

final class SshClient implements SshClientInterface
{

    public function __construct(
        private readonly SSH2 $ssh,
        private readonly RemotePathInterface $remotePath

    ) {
        if (! $ssh->isConnected()) {
            throw new RuntimeException('SSH tidak connect');
        }
    }

    /**
     * Menjalankan perintah di remote server
     */
    public function exec(string $command, callable $callback = null): string|bool
    {
        $remotePath = escapeshellarg($this->remotePath->getRemotePath());
        try {
            $result = $this->ssh->exec("cd {$remotePath} && {$command}", $callback);
            if ($result === false) {
                throw new RuntimeException("Perintah SSH gagal dijalankan: {$command}");
            }
            return $result;
        } catch (\Throwable $e) {
            throw new RuntimeException(
                'Kesalahan saat menjalankan perintah SSH: ' . $e->getMessage(),
                previous: $e
            );
        }
    }


    /**
     * Mengambil direktori kerja saat ini (jika shell login)
     */
    public function pwd(): ?string
    {
        try {
            return trim($this->exec('pwd')) ?: null;
        } catch (\Throwable $e) {
            throw new RuntimeException('Gagal mendapatkan direktori kerja', previous: $e);
        }
    }

    /**
     * Mengambil direktori kerja saat ini (jika shell login)
     */
    public function ls(): ?string
    {
        try {
            return trim($this->exec('ls')) ?: null;
        } catch (\Throwable $e) {
            throw new RuntimeException('Gagal mendapatkan direktori kerja', previous: $e);
        }
    }
}
