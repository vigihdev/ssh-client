# SSH Client Library

[![PHP Version](https://img.shields.io/badge/php-%5E8.0-blue.svg)](https://php.net)
[![Tests](https://img.shields.io/badge/tests-passing-brightgreen.svg)](https://github.com/vigihdev/ssh-client)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

A lightweight and robust SSH client for PHP to easily connect, execute commands, and manage remote shells.

## Installation

```bash
composer require vigihdev/ssh-client
```

## Quick Start

```php
use Vigihdev\Ssh\Client\SshClient;
use Vigihdev\Ssh\Handler\RemotePathHandler;
use phpseclib3\Net\SSH2;

// Create SSH connection
$ssh = new SSH2('hostname', 22);
$ssh->login('username', $privateKey);

// Create client
$remotePath = new RemotePathHandler('/home/user');
$client = new SshClient($ssh, $remotePath);

// Execute commands
$result = $client->exec('ls -la');
echo $result;
```

## Features

- ✅ SSH command execution
- ✅ SFTP file operations  
- ✅ Remote path management
- ✅ Console commands
- ✅ Exception handling
- ✅ Comprehensive testing

## Console Usage

```bash
# Execute SSH command
php bin/console ssh "ls -la" --connection=default

# List available connections
php bin/console ssh:list
```

## Configuration

Create SSH configuration in `config/packages/ssh.yaml`:

```yaml
ssh:
  connections:
    default:
      host: 'localhost'
      port: 22
      user: 'username'
      timeout: 30
      key_path: '/path/to/private/key'
```

## Testing

```bash
composer test
```

## License

MIT License
