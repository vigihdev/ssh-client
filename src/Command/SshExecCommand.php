<?php

declare(strict_types=1);

namespace Vigihdev\Ssh\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Vigihdev\Ssh\Service\SshConnectionManagerService;

#[AsCommand(name: 'ssh:exec', description: 'Eksekusi koneksi SSH (interaktif / perintah)')]
final class SshExecCommand extends Command
{

    public function __construct(
        private SshConnectionManagerService $sshConnection
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('cmd', InputArgument::REQUIRED, 'Perintah yang akan dieksekusi di remote host')
            ->addOption(
                'connection',
                'c',
                InputOption::VALUE_REQUIRED,
                'Nama koneksi SSH (misal: satis, sirent, default)',
                'default',
                function () {
                    return $this->sshConnection->getAvailableServiceNames();
                }
            )
            ->getHelp(
                <<<'HELP'
                Perintah ini digunakan untuk mengeksekusi koneksi SSH ke remote host.
                
                Contoh penggunaan:
                
                1. Eksekusi koneksi SSH interaktif:
                   php bin/console ssh:exec --connection=satis
                   
                2. Eksekusi perintah di remote host:
                   php bin/console ssh:exec --connection=satis --cmd="ls -la"
                HELP
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $connectionName = $input->getOption('connection');
        $cmd = $input->getArgument('cmd');

        if (! $this->sshConnection->hasServiceConnection($connectionName)) {
            $io->error(sprintf('Koneksi SSH dengan nama "%s" tidak ditemukan.', $connectionName));
            return Command::FAILURE;
        }

        $ssh = $this->sshConnection->getConnection($connectionName)->getSshClient();
        $io->writeln($ssh->exec($cmd));
        return Command::SUCCESS;
    }
}
