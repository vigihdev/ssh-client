<?php

declare(strict_types=1);

namespace Vigihdev\Ssh\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Vigihdev\Ssh\Service\SshConnectionManagerService;

final class SshListCommand extends Command
{
    protected static $defaultName = 'ssh:list';

    public function __construct(
        private readonly SshConnectionManagerService $sshConnection
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Menampilkan daftar koneksi SSH yang tersedia');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $connections = $this->sshConnection->getAvailableServiceNames();

        if (empty($connections)) {
            $io->warning('Tidak ada koneksi SSH yang terdaftar.');
            return Command::SUCCESS;
        }

        $io->title('Daftar Koneksi SSH');
        $io->listing($connections);

        $io->success(sprintf('Total koneksi: %d', count($connections)));

        return Command::SUCCESS;
    }
}
