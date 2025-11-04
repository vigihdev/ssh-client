<?php

namespace Vigihdev\Ssh\Tests\Handler;

use Vigihdev\Ssh\Handler\RemotePathHandler;
use Vigihdev\Ssh\Tests\TestCase;

class RemotePathHandlerTest extends TestCase
{
    public function testGetRemotePathReturnsCorrectPath(): void
    {
        $expectedPath = '/home/user/project';
        $handler = new RemotePathHandler($expectedPath);
        
        $this->assertEquals($expectedPath, $handler->getRemotePath());
    }

    public function testGetRemotePathWithDifferentPath(): void
    {
        $expectedPath = '/var/www/html';
        $handler = new RemotePathHandler($expectedPath);
        
        $this->assertEquals($expectedPath, $handler->getRemotePath());
    }
}
