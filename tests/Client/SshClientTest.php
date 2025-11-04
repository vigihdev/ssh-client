<?php

namespace Vigihdev\Ssh\Tests\Client;

use phpseclib3\Net\SSH2;
use PHPUnit\Framework\MockObject\MockObject;
use RuntimeException;
use Vigihdev\Ssh\Client\SshClient;
use Vigihdev\Ssh\Contracts\RemotePathInterface;
use Vigihdev\Ssh\Tests\TestCase;

class SshClientTest extends TestCase
{
    private MockObject $sshMock;
    private MockObject $remotePathMock;
    private SshClient $sshClient;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->sshMock = $this->createMock(SSH2::class);
        $this->remotePathMock = $this->createMock(RemotePathInterface::class);
        
        $this->sshMock->method('isConnected')->willReturn(true);
        $this->remotePathMock->method('getRemotePath')->willReturn('/home/user');
        
        $this->sshClient = new SshClient($this->sshMock, $this->remotePathMock);
    }

    public function testConstructorThrowsExceptionWhenSshNotConnected(): void
    {
        $disconnectedSsh = $this->createMock(SSH2::class);
        $disconnectedSsh->method('isConnected')->willReturn(false);
        
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('SSH tidak connect');
        
        new SshClient($disconnectedSsh, $this->remotePathMock);
    }

    public function testExecExecutesCommandSuccessfully(): void
    {
        $command = 'ls -la';
        $expectedOutput = 'file1.txt\nfile2.txt';
        
        $this->sshMock->expects($this->once())
            ->method('exec')
            ->with("cd '/home/user' && {$command}")
            ->willReturn($expectedOutput);
        
        $result = $this->sshClient->exec($command);
        
        $this->assertEquals($expectedOutput, $result);
    }

    public function testExecThrowsExceptionWhenCommandFails(): void
    {
        $command = 'invalid-command';
        
        $this->sshMock->method('exec')->willReturn(false);
        
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Perintah SSH gagal dijalankan: {$command}");
        
        $this->sshClient->exec($command);
    }

    public function testPwdReturnsCurrentDirectory(): void
    {
        $expectedPath = '/home/user/project';
        
        $this->sshMock->method('exec')
            ->with("cd '/home/user' && pwd")
            ->willReturn($expectedPath . "\n");
        
        $result = $this->sshClient->pwd();
        
        $this->assertEquals($expectedPath, $result);
    }

    public function testLsReturnsFileList(): void
    {
        $expectedList = 'file1.txt file2.txt';
        
        $this->sshMock->method('exec')
            ->with("cd '/home/user' && ls")
            ->willReturn($expectedList . "\n");
        
        $result = $this->sshClient->ls();
        
        $this->assertEquals($expectedList, $result);
    }
}
