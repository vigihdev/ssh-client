<?php

declare(strict_types=1);

namespace Vigihdev\Ssh\Client;

use phpseclib3\Net\SFTP;
use RuntimeException;
use Vigihdev\Ssh\Contracts\SftpClientInterface;

/**
 * SftpClient
 *
 * Class untuk berinteraksi dengan SFTP server
 */
final class SftpClient implements SftpClientInterface
{
    /**
     * @var SFTP Instance dari SFTP client.
     */
    private readonly SFTP $sftp;

    /**
     * Membuat instance baru dari SftpClient.
     *
     * @param SFTP $sftp Instance dari SFTP client yang sudah terhubung.
     * @throws RuntimeException Jika SFTP client tidak terhubung.
     */
    public function __construct(
        SFTP $sftp
    ) {
        if (! $sftp->isConnected()) {
            throw new RuntimeException("Sftp tidak connect");
        }

        $this->sftp = $sftp;
    }

    /**
     * Mendapatkan path direktori saat ini.
     *
     * @return mixed Path direktori saat ini.
     */
    public function pwd()
    {
        return $this->sftp->pwd();
    }

    /**
     * Mengeksekusi perintah di remote server.
     *
     * @param string $command Perintah yang akan dieksekusi.
     * @param callable|null $callback Callback untuk menangani output.
     * @return string|boolean Hasil dari eksekusi perintah.
     * @throws RuntimeException Jika terjadi error saat eksekusi.
     */
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
