<?php

declare(strict_types=1);

namespace Vigihdev\Ssh\Contracts;

/**
 * SshServiceInterface
 *
 * Interface untuk service SSH
 */
interface SshServiceInterface
{

    /**
     * Mengeksekusi perintah di remote server.
     *
     * @param string $command Perintah yang akan dieksekusi.
     * @param callable|null $callback Callback untuk menangani output.
     * @return string|boolean Hasil dari eksekusi perintah.
     */
    public function execute(string $command, callable $callback = null): string|bool;
}
