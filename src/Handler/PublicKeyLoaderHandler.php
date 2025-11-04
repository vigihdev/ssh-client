<?php

declare(strict_types=1);

namespace Vigihdev\Ssh\Handler;

use phpseclib3\Crypt\Common\AsymmetricKey;
use phpseclib3\Crypt\PublicKeyLoader;
use RuntimeException;
use Vigihdev\Ssh\Contracts\PublicKeyLoaderInterface;

final class PublicKeyLoaderHandler implements PublicKeyLoaderInterface
{

    private string $keyPathLoader;

    public function __construct(
        private readonly string $keyPath
    ) {

        $this->keyPathLoader = $this->realKeyPath();
        if (!is_file($this->keyPathLoader)) {
            throw new RuntimeException("File {$keyPath} tidak tersedia");
        }
    }

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

    public function getKeyLoader(): AsymmetricKey
    {
        $key = file_get_contents($this->keyPathLoader);
        return PublicKeyLoader::load($key);
    }
}
