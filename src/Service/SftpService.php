<?php

declare(strict_types=1);

namespace Vigihdev\Ssh\Service;

use phpseclib3\Net\SFTP;
use RuntimeException;
use Vigihdev\Ssh\Contracts\{PublicKeyLoaderInterface, RemotePathInterface, SshConnectionInterface};

final class SftpService
{

    public function __construct(
        private readonly SshConnectionInterface $connection,
        private readonly RemotePathInterface $remotePath,
        private readonly PublicKeyLoaderInterface $key,
    ) {}

    public function getSftp(): SFTP
    {

        $sftp = new SFTP(
            host: $this->connection->getHost(),
            port: $this->connection->getPort(),
            timeout: $this->connection->getTimeout(),
        );

        if (! $sftp->login($this->connection->getUser(), $this->key->getKeyLoader())) {
            throw new RuntimeException("Gagal login");
        }

        $remotePath = $this->remotePath->getRemotePath();
        if (! $sftp->is_dir($remotePath)) {
            throw new RuntimeException("{$remotePath} bukan directory");
        }

        $sftp->chdir($remotePath);
        return $sftp;
    }
}
