<?php

declare(strict_types=1);

namespace Vigihdev\Ssh\Contracts;

use phpseclib3\Crypt\Common\AsymmetricKey;

interface PublicKeyLoaderInterface
{

    public function getKeyLoader(): AsymmetricKey;
}
