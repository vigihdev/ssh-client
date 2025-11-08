<?php

declare(strict_types=1);

namespace Vigihdev\Ssh\Service;

use phpseclib3\Net\{SFTP, SSH2};
use Vigihdev\Encryption\Contracts\EnvironmentEncryptorServiceContract;
use Vigihdev\Ssh\Contracts\{PublicKeyLoaderInterface, RemotePathInterface, SshConnectionConfigInterface, SshConnectionEncryptorInterface};
use Vigihdev\Ssh\Client\{SftpClient, SshClient};
use Vigihdev\Ssh\Exception\{ConnectionException, DirectoryException};
use VigihDev\SymfonyBridge\Config\AttributeInjection\{DependencyInjector, Inject};

/**
 * SshConnectionConfigEncryptorService
 *
 * Class untuk konfigurasi koneksi SSH
 */
final class SshConnectionConfigEncryptorService implements SshConnectionConfigInterface
{

    /**
     * @var SSH2|null Instance dari SSH2 client.
     */
    private ?SSH2 $ssh2 = null;

    /**
     * @var SFTP|null Instance dari SFTP client.
     */
    private ?SFTP $sftp = null;

    /**
     * Membuat instance baru dari SshConnectionConfigService.
     *
     * @param PublicKeyLoaderInterface $key Instance dari PublicKeyLoader.
     * @param SshConnectionEncryptorInterface $connection Instance dari SshConnection.
     * @param RemotePathInterface $remotePath Instance dari RemotePath.
     */
    public function __construct(
        private readonly PublicKeyLoaderInterface $key,
        private readonly SshConnectionEncryptorInterface $connection,
        private readonly RemotePathInterface $remotePath,
        #[Inject(EnvironmentEncryptorServiceContract::class)]
        private ?EnvironmentEncryptorServiceContract $encryptor = null
    ) {
        DependencyInjector::inject($this);
    }

    /**
     * Mendapatkan SshClient.
     *
     * @return SshClient Instance dari SshClient.
     */
    public function getSshClient(): SshClient
    {
        return new SshClient(
            ssh: $this->getSsh(),
            remotePath: $this->remotePath
        );
    }

    /**
     * Mendapatkan SftpClient.
     *
     * @return SftpClient Instance dari SftpClient.
     */
    public function getSftpClient(): SftpClient
    {
        return new SftpClient($this->getSftp());
    }

    /**
     * Mendapatkan atau membuat instance dari SSH2 client.
     *
     * @return SSH2 Instance dari SSH2 client.
     * @throws ConnectionException Jika autentikasi gagal.
     */
    private function getSsh(): SSH2
    {

        if ($this->ssh2 === null) {
            $this->ssh2 = new SSH2(
                host: $this->decrypt($this->connection->getHost()),
                port: (int) $this->decrypt($this->connection->getPort()),
                timeout: (int) $this->decrypt($this->connection->getTimeout()),
            );

            if (! $this->ssh2->login((string) $this->decrypt($this->connection->getUser()), $this->key->getKeyLoader())) {
                throw ConnectionException::authenticationFailed($this->connection->getHost(), $this->connection->getUser());
            }
        }

        if (! $this->ssh2?->isConnected()) {
            throw ConnectionException::authenticationFailed($this->connection->getHost(), $this->connection->getUser());
        }

        return $this->ssh2;
    }

    /**
     * Mendapatkan atau membuat instance dari SFTP client.
     *
     * @return SFTP Instance dari SFTP client.
     */
    private function getSftp(): SFTP
    {

        if ($this->sftp === null) {
            $this->sftp = new SFTP(
                host: $this->decrypt($this->connection->getHost()),
                port: (int) $this->decrypt($this->connection->getPort()),
                timeout: (int) $this->decrypt($this->connection->getTimeout()),
            );

            $this->connectAndValidate($this->sftp);
        }

        if (! $this->sftp->isConnected()) {
            $this->connectAndValidate($this->sftp);
        }

        return $this->sftp;
    }

    /**
     * Menghubungkan dan memvalidasi koneksi SFTP.
     *
     * @param SFTP $sftp Instance dari SFTP client.
     * @return void
     * @throws ConnectionException Jika autentikasi gagal.
     * @throws DirectoryException Jika direktori remote tidak ditemukan.
     */
    private function connectAndValidate(SFTP $sftp): void
    {
        if (! $sftp->login((string) $this->decrypt($this->connection->getUser()), $this->key->getKeyLoader())) {
            throw ConnectionException::authenticationFailed($this->connection->getHost(), $this->connection->getUser());
        }

        $remotePath = $this->decrypt($this->remotePath->getRemotePath());
        if (! $sftp->is_dir($remotePath)) {
            throw DirectoryException::directoryNotFound($remotePath);
        }

        $sftp->chdir($remotePath);
    }

    private function decrypt(string $value): string
    {
        return $this->encryptor->isEncrypted($value) ? $this->encryptor->decrypt($value) : $value;
    }
}
