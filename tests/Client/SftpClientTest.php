<?php

namespace Vigihdev\Ssh\Tests\Client;

use phpseclib3\Net\SFTP;
use PHPUnit\Framework\MockObject\MockObject;
use RuntimeException;
use Vigihdev\Ssh\Client\SftpClient;
use Vigihdev\Ssh\Tests\TestCase;

class SftpClientTest extends TestCase
{
    private MockObject $sftpMock;
    private SftpClient $sftpClient;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->sftpMock = $this->createMock(SFTP::class);
        $this->sftpMock->method('isConnected')->willReturn(true);
        
        $this->sftpClient = new SftpClient($this->sftpMock);
    }

    public function testConstructorThrowsExceptionWhenSftpNotConnected(): void
    {
        $disconnectedSftp = $this->createMock(SFTP::class);
        $disconnectedSftp->method('isConnected')->willReturn(false);
        
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Sftp tidak connect');
        
        new SftpClient($disconnectedSftp);
    }

    public function testPwdReturnsCurrentDirectory(): void
    {
        $expectedPath = '/home/user';
        
        $this->sftpMock->expects($this->once())
            ->method('pwd')
            ->willReturn($expectedPath);
        
        $result = $this->sftpClient->pwd();
        
        $this->assertEquals($expectedPath, $result);
    }

    public function testExecExecutesCommandSuccessfully(): void
    {
        $command = 'ls -la';
        $currentPath = '/home/user';
        $expectedOutput = 'file1.txt\nfile2.txt';
        
        $this->sftpMock->method('pwd')->willReturn($currentPath);
        $this->sftpMock->expects($this->once())
            ->method('exec')
            ->with("cd {$currentPath} && {$command}")
            ->willReturn($expectedOutput);
        
        $result = $this->sftpClient->exec($command);
        
        $this->assertEquals($expectedOutput, $result);
    }
}
