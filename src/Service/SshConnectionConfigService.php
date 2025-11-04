<?php

declare(strict_types=1);

namespace Vigihdev\Ssh\Service;

use phpseclib3\Net\{SFTP, SSH2};
use Vigihdev\Ssh\Contracts\{PublicKeyLoaderInterface, RemotePathInterface, SshConnectionInterface, SshConnectionConfigInterface};
use Vigihdev\Ssh\Client\{SftpClient, SshClient};
use Vigihdev\Ssh\Exception\{ConnectionException, DirectoryException};

final class SshConnectionConfigService implements SshConnectionConfigInterface
{

    private ?SSH2 $ssh2 = null;
    private ?SFTP $sftp = null;

    public function __construct(
        private readonly PublicKeyLoaderInterface $key,
        private readonly SshConnectionInterface $connection,
        private readonly RemotePathInterface $remotePath
    ) {}

    public function getSshClient(): SshClient
    {
        return new SshClient(
            ssh: $this->getSsh(),
            remotePath: $this->remotePath
        );
    }

    public function getSftpClient(): SftpClient
    {
        return new SftpClient($this->getSftp());
    }

    private function getSsh(): SSH2
    {

        if ($this->ssh2 === null) {
            $this->ssh2 = new SSH2(
                host: $this->connection->getHost(),
                port: $this->connection->getPort(),
                timeout: $this->connection->getTimeout(),
            );

            if (! $this->ssh2->login($this->connection->getUser(), $this->key->getKeyLoader())) {
                throw ConnectionException::authenticationFailed($this->connection->getHost(), $this->connection->getUser());
            }
        }

        if (! $this->ssh2?->isConnected()) {
            throw ConnectionException::authenticationFailed($this->connection->getHost(), $this->connection->getUser());
        }

        return $this->ssh2;
    }

    private function getSftp(): SFTP
    {

        if ($this->sftp === null) {
            $this->sftp = new SFTP(
                host: $this->connection->getHost(),
                port: $this->connection->getPort(),
                timeout: $this->connection->getTimeout(),
            );

            $this->connectAndValidate($this->sftp);
        }

        if (! $this->sftp->isConnected()) {
            $this->connectAndValidate($this->sftp);
        }

        return $this->sftp;
    }

    private function connectAndValidate(SFTP $sftp): void
    {
        if (! $sftp->login($this->connection->getUser(), $this->key->getKeyLoader())) {
            throw ConnectionException::authenticationFailed($this->connection->getHost(), $this->connection->getUser());
        }

        $remotePath = $this->remotePath->getRemotePath();
        if (! $sftp->is_dir($remotePath)) {
            throw DirectoryException::directoryNotFound($remotePath);
        }

        $sftp->chdir($remotePath);
    }
}
