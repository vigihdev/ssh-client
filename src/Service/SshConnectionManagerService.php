<?php

declare(strict_types=1);

namespace Vigihdev\Ssh\Service;

use InvalidArgumentException;
use Vigihdev\Ssh\Contracts\{SshConnectionConfigInterface, SshConnectionManagerInterface};

/**
 * SshConnectionManagerService
 *
 * Class untuk me-manage koneksi SSH
 */
final class SshConnectionManagerService implements SshConnectionManagerInterface
{

    /**
     * @param array<string,SshConnectionConfigInterface> $sshConfigs
     * @return void
     */
    public function __construct(
        private readonly array $sshConfigs
    ) {}


    /**
     * Mendapatkan koneksi SSH berdasarkan nama.
     *
     * @param string $name Nama koneksi.
     * @return SshConnectionConfigInterface Instance dari SshConnectionConfig.
     * @throws InvalidArgumentException Jika koneksi tidak tersedia.
     */
    public function getConnection(string $name): SshConnectionConfigInterface
    {
        if (! $this->hasServiceConnection($name)) {
            throw new InvalidArgumentException("Connection {$name} tidak tersedia");
        }

        return $this->sshConfigs[$name];
    }

    /**
     * Mendapatkan daftar nama service koneksi yang tersedia.
     *
     * @return array<int, string> Daftar nama service.
     */
    public function getAvailableServiceNames(): array
    {
        return array_keys($this->sshConfigs);
    }

    /**
     * Memeriksa apakah service koneksi tersedia.
     *
     * @param string $name Nama service.
     * @return bool True jika tersedia, false jika tidak.
     */
    public function hasServiceConnection(string $name): bool
    {
        $ssh = $this->sshConfigs[$name] ?? null;
        return $ssh instanceof SshConnectionConfigInterface;
    }
}
