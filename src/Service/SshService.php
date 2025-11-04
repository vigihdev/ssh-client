<?php

declare(strict_types=1);

namespace Vigihdev\Ssh\Service;

use phpseclib3\Net\SSH2;
use RuntimeException;
use Vigihdev\Ssh\Contracts\{PublicKeyLoaderInterface, SshConnectionInterface, SshServiceInterface};

/**
 * SshService
 *
 * Class untuk service SSH
 */
final class SshService implements SshServiceInterface
{


    /**
     * Membuat instance baru dari SshService.
     *
     * @param SshConnectionInterface $connection Instance dari SshConnection.
     * @param PublicKeyLoaderInterface $key Instance dari PublicKeyLoader.
     */
    public function __construct(
        private readonly SshConnectionInterface $connection,
        private readonly PublicKeyLoaderInterface $key,
    ) {}

    /**
     * Membuat dan login ke SSH client.
     *
     * @return SSH2 Instance dari SSH2 client.
     * @throws RuntimeException Jika login gagal.
     */
    private function createAndLoginSshClient(): SSH2
    {

        $ssh = new SSH2(
            host: $this->connection->getHost(),
            port: $this->connection->getPort(),
            timeout: $this->connection->getTimeout(),
        );

        if (! $ssh->login($this->connection->getUser(), $this->key->getKeyLoader())) {
            throw new RuntimeException("Gagal login");
        }

        return $ssh;
    }

    /**
     * Mengeksekusi perintah di remote server.
     *
     * @param string $command Perintah yang akan dieksekusi.
     * @param callable|null $callback Callback untuk menangani output.
     * @return string|boolean Hasil dari eksekusi perintah.
     * @throws RuntimeException Jika terjadi error saat eksekusi.
     */
    public function execute(string $command, callable $callback = null): string|bool
    {
        $sshClient = $this->createAndLoginSshClient();

        try {
            return $sshClient->exec($command, $callback);
        } catch (\Throwable $e) {
            throw new RuntimeException($e->getMessage());
        }
    }
}
