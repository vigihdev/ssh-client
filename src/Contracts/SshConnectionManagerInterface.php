<?php

declare(strict_types=1);

namespace Vigihdev\Ssh\Contracts;

/**
 * SshConnectionManagerInterface
 *
 * Interface untuk me-manage koneksi SSH
 */
interface SshConnectionManagerInterface
{

    /**
     * Mendapatkan koneksi SSH berdasarkan nama.
     *
     * @param string $name Nama koneksi.
     * @return SshConnectionConfigInterface Instance dari SshConnectionConfig.
     */
    public function getConnection(string $name): SshConnectionConfigInterface;

    /**
     * Mendapatkan daftar nama service koneksi yang tersedia.
     *
     * @return array<int, string> Daftar nama service.
     */
    public function getAvailableServiceNames(): array;

    /**
     * Memeriksa apakah service koneksi tersedia.
     *
     * @param string $name Nama service.
     * @return bool True jika tersedia, false jika tidak.
     */
    public function hasServiceConnection(string $name): bool;
}
