<?php

declare(strict_types=1);

namespace Vigihdev\Ssh\Service;

use phpseclib3\Net\SSH2;
use RuntimeException;
use Vigihdev\Ssh\Contracts\{PublicKeyLoaderInterface, SshConnectionInterface, SshServiceInterface};

final class SshService implements SshServiceInterface
{


    public function __construct(
        private readonly SshConnectionInterface $connection,
        private readonly PublicKeyLoaderInterface $key,
    ) {}

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
