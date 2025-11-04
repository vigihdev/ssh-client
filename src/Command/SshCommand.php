<?php

declare(strict_types=1);

namespace Vigihdev\Ssh\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\{InputOption, InputInterface};
use Symfony\Component\Console\Style\SymfonyStyle;
use Vigihdev\Ssh\Service\SshConnectionManagerService;

final class SshCommand extends Command
{
    protected static $defaultName = 'ssh';

    public function __construct(
        private readonly SshConnectionManagerService $sshConnection
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {

        $this
            ->setDescription('Execute Command and show results')
            ->addArgument('cmd', null, 'Command to execute')
            ->addOption(
                'connection',
                'c',
                InputOption::VALUE_OPTIONAL,
                'Nama koneksi SSH yang terdaftar',
                'default',
                function () {
                    return $this->sshConnection->getAvailableServiceNames();
                }
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $cmd = $input->getArgument('cmd');
        $connection = $input->getOption('connection');

        if (! $cmd) {
            $io->note('Command wajib');
            return Command::FAILURE;
        }

        if (! $this->sshConnection->hasServiceConnection($connection)) {
            $io->error("Connection {$connection} tidak tersedia");
            return Command::FAILURE;
        }

        $ssh = $this->sshConnection->getConnection($connection);
        $io->writeln($ssh->getSshClient()->exec($cmd));

        return Command::SUCCESS;
    }
}
