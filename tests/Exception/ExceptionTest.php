<?php

namespace Vigihdev\Ssh\Tests\Exception;

use Vigihdev\Ssh\Exception\CommandException;
use Vigihdev\Ssh\Exception\ConfigurationException;
use Vigihdev\Ssh\Exception\ConnectionException;
use Vigihdev\Ssh\Exception\DirectoryException;
use Vigihdev\Ssh\Exception\FileTransferException;
use Vigihdev\Ssh\Exception\SshException;
use Vigihdev\Ssh\Tests\TestCase;

class ExceptionTest extends TestCase
{
    public function testSshExceptionCanBeThrown(): void
    {
        $this->expectException(SshException::class);
        $this->expectExceptionMessage('SSH error occurred');
        
        throw new SshException('SSH error occurred');
    }

    public function testConnectionExceptionCanBeThrown(): void
    {
        $this->expectException(ConnectionException::class);
        $this->expectExceptionMessage('Connection failed');
        
        throw new ConnectionException('Connection failed');
    }

    public function testCommandExceptionCanBeThrown(): void
    {
        $this->expectException(CommandException::class);
        $this->expectExceptionMessage('Command execution failed');
        
        throw new CommandException('Command execution failed');
    }

    public function testConfigurationExceptionCanBeThrown(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Configuration error');
        
        throw new ConfigurationException('Configuration error');
    }

    public function testDirectoryExceptionCanBeThrown(): void
    {
        $this->expectException(DirectoryException::class);
        $this->expectExceptionMessage('Directory operation failed');
        
        throw new DirectoryException('Directory operation failed');
    }

    public function testFileTransferExceptionCanBeThrown(): void
    {
        $this->expectException(FileTransferException::class);
        $this->expectExceptionMessage('File transfer failed');
        
        throw new FileTransferException('File transfer failed');
    }
}
