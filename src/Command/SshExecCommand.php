<?php

declare(strict_types=1);

namespace App\Command\Ssh;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'ssh:exec', description: 'Eksekusi koneksi SSH (interaktif / perintah)')]
final class SshExecCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addOption('connection', 'c', InputOption::VALUE_REQUIRED, 'Nama koneksi SSH (misal: satis, sirent, default)')
            ->addOption('cmd', null, InputOption::VALUE_OPTIONAL, 'Perintah yang akan dieksekusi di remote host');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $connection = $input->getOption('connection');
        return Command::SUCCESS;
    }
}
