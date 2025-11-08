<?php

declare(strict_types=1);

namespace Vigihdev\Ssh\Client;

use phpseclib3\Net\SSH2;
use RuntimeException;
use Vigihdev\Encryption\Contracts\EnvironmentEncryptorServiceContract;
use Vigihdev\Ssh\Contracts\{RemotePathInterface, SshClientInterface};
use VigihDev\SymfonyBridge\Config\AttributeInjection\{DependencyInjector, Inject};

/**
 * SshClient
 *
 * Class untuk berinteraksi dengan SSH server
 */
final class SshClient implements SshClientInterface
{
    /**
     * @var SSH2 Instance dari SSH2 client.
     */
    private readonly SSH2 $ssh;

    /**
     * @var RemotePathInterface Instance dari RemotePath.
     */
    private readonly RemotePathInterface $remotePath;

    /**
     * Membuat instance baru dari SshClient.
     *
     * @param SSH2 $ssh Instance dari SSH2 client yang sudah terhubung.
     * @param RemotePathInterface $remotePath Path remote direktori.
     * @throws RuntimeException Jika SSH client tidak terhubung.
     */
    public function __construct(
        SSH2 $ssh,
        RemotePathInterface $remotePath,
        #[Inject(EnvironmentEncryptorServiceContract::class)]
        private ?EnvironmentEncryptorServiceContract $encryptor = null

    ) {
        if (! $ssh->isConnected()) {
            throw new RuntimeException('SSH tidak connect');
        }

        DependencyInjector::inject($this);

        $this->ssh = $ssh;
        $this->remotePath = $remotePath;
    }

    /**
     * Mengeksekusi perintah SSH di remote host.
     *
     * @param string $command Perintah yang akan dieksekusi.
     * @param callable|null $callback Callback untuk menangani output secara real-time.
     * @return string|boolean Output dari perintah atau false jika gagal.
     * @throws RuntimeException Jika perintah SSH gagal dijalankan.
     */
    public function exec(string $command, callable $callback = null): string|bool
    {
        $remotePath = escapeshellarg($this->decrypt($this->remotePath->getRemotePath()));
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
     * Mendapatkan direktori kerja saat ini di remote host.
     *
     * @return string|null Direktori kerja saat ini atau null jika gagal.
     * @throws RuntimeException Jika gagal mendapatkan direktori kerja.
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
     * Mendapatkan daftar file dan direktori di direktori kerja saat ini di remote host.
     *
     * @return string|null Daftar file dan direktori atau null jika gagal.
     * @throws RuntimeException Jika gagal mendapatkan daftar file dan direktori.
     */
    public function ls(): ?string
    {
        try {
            return trim($this->exec('ls')) ?: null;
        } catch (\Throwable $e) {
            throw new RuntimeException('Gagal mendapatkan direktori kerja', previous: $e);
        }
    }

    private function decrypt(string $value): string
    {
        return $this->encryptor->isEncrypted($value) ? $this->encryptor->decrypt($value) : $value;
    }
}
