<?php

namespace Vigihdev\Ssh\Tests\Command;

use Vigihdev\Ssh\Command\SshCommand;
use Vigihdev\Ssh\Service\SshConnectionManagerService;
use Vigihdev\Ssh\Tests\TestCase;

class SshCommandTest extends TestCase
{
    public function testCommandCanBeInstantiated(): void
    {
        $sshConnectionManager = new SshConnectionManagerService([]);
        $command = new SshCommand($sshConnectionManager);
        
        $this->assertInstanceOf(SshCommand::class, $command);
        $this->assertEquals('ssh', $command->getName());
    }

    public function testCommandHasCorrectDescription(): void
    {
        $sshConnectionManager = new SshConnectionManagerService([]);
        $command = new SshCommand($sshConnectionManager);
        
        $this->assertEquals('Execute Command and show results', $command->getDescription());
    }
}
