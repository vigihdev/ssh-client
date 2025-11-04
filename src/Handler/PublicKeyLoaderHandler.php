<?php

declare(strict_types=1);

namespace Vigihdev\Ssh\Handler;

use phpseclib3\Crypt\Common\AsymmetricKey;
use phpseclib3\Crypt\PublicKeyLoader;
use RuntimeException;
use Vigihdev\Ssh\Contracts\PublicKeyLoaderInterface;

/**
 * PublicKeyLoaderHandler
 *
 * Class untuk handle public key loader
 */
final class PublicKeyLoaderHandler implements PublicKeyLoaderInterface
{

    /**
     * @var string Path ke file key.
     */
    private string $keyPathLoader;

    /**
     * Membuat instance baru dari PublicKeyLoaderHandler.
     *
     * @param string $keyPath Path ke file key.
     * @throws RuntimeException Jika file key tidak tersedia.
     */
    public function __construct(
        private readonly string $keyPath
    ) {

        $this->keyPathLoader = $this->realKeyPath();
        if (!is_file($this->keyPathLoader)) {
            throw new RuntimeException("File {$keyPath} tidak tersedia");
        }
    }

    /**
     * Mendapatkan path asli dari file key.
     *
     * @return string Path asli dari file key.
     */
    private function realKeyPath(): string
    {
        $pathKey = $this->keyPath;
        // Expand tilde
        if (strpos($pathKey, '~') === 0) {
            $home = getenv('HOME') ?: ($_SERVER['HOME'] ?? null);
            if ($home) {
                $pathKey = $home . substr($pathKey, 1);
            }
        }
        return $pathKey;
    }

    /**
     * Mendapatkan key loader.
     *
     * @return AsymmetricKey Instance dari AsymmetricKey.
     */
    public function getKeyLoader(): AsymmetricKey
    {
        $key = file_get_contents($this->keyPathLoader);
        return PublicKeyLoader::load($key);
    }
}
