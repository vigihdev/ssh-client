<?php

namespace Vigihdev\Ssh\Tests\Service;

use PHPUnit\Framework\MockObject\MockObject;
use RuntimeException;
use Vigihdev\Ssh\Contracts\PublicKeyLoaderInterface;
use Vigihdev\Ssh\Contracts\SshConnectionInterface;
use Vigihdev\Ssh\Service\SshService;
use Vigihdev\Ssh\Tests\TestCase;

class SshServiceTest extends TestCase
{
    private MockObject $connectionMock;
    private MockObject $keyLoaderMock;
    private SshService $sshService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->connectionMock = $this->createMock(SshConnectionInterface::class);
        $this->keyLoaderMock = $this->createMock(PublicKeyLoaderInterface::class);
        
        $this->connectionMock->method('getHost')->willReturn('localhost');
        $this->connectionMock->method('getPort')->willReturn(22);
        $this->connectionMock->method('getTimeout')->willReturn(30);
        $this->connectionMock->method('getUser')->willReturn('testuser');
        
        $this->sshService = new SshService($this->connectionMock, $this->keyLoaderMock);
    }

    public function testServiceCanBeInstantiated(): void
    {
        $this->assertInstanceOf(SshService::class, $this->sshService);
    }

    public function testExecuteThrowsExceptionOnConnectionFailure(): void
    {
        $command = 'echo "test"';
        
        // Mock the key loader to return a mock key
        $mockKey = $this->createMock(\phpseclib3\Crypt\Common\AsymmetricKey::class);
        $this->keyLoaderMock->method('getKeyLoader')->willReturn($mockKey);
        
        // This will fail because we can't actually connect to SSH
        $this->expectException(RuntimeException::class);
        
        $this->sshService->execute($command);
    }
}
