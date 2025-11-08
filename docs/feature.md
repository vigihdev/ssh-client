```php
use VigihDev\SymfonyBridge\Config\ConfigBridge;
use VigihDev\SymfonyBridge\Config\Service\ServiceLocator;
use Vigihdev\Ssh\Contracts\PublicKeyLoaderInterface;
use Vigihdev\Ssh\Contracts\SshConnectionManagerInterface;

ConfigBridge::boot(__DIR__);

/** @var SshConnectionManagerInterface $connection  */
$connection = ServiceLocator::get(SshConnectionManagerInterface::class);
$sftp = $connection->getConnection('satis')->getSftpClient();
$sftp->chdir('public');
$lists = $sftp->lists(directory: '.', recursive: true);

var_dump(
    $connection->getConnection('okkarent.org')->getSshClient()->pwd(),
    $connection->getConnection('dotrentcar.com')->getSshClient()->pwd(),
    $connection->getConnection('omahtrans.com')->getSshClient()->pwd(),
    $connection->getConnection('meccarentcar.com')->getSshClient()->pwd(),
    $connection->getConnection('satis')->getSshClient()->pwd(),
);


$finder = new Finder();
$finder
    ->files()
    ->in(Path::join(__DIR__, 'feature'))
    ->name('/\.html/')
;

foreach ($finder->files() as $file) {
    var_dump($file->getRealPath());
}

```
