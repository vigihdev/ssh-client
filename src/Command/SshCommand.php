<?php

declare(strict_types=1);

namespace Vigihdev\Ssh\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\{InputOption, InputInterface};
use Symfony\Component\Console\Style\SymfonyStyle;
use Vigihdev\Ssh\Service\SshConnectionManagerService;

/**
 * SshCommand
 *
 * Command untuk mengeksekusi perintah SSH di remote host.
 */
final class SshCommand extends Command
{
    protected static $defaultName = 'ssh';

    /**
     * @var SshConnectionManagerService
     */
    private readonly SshConnectionManagerService $sshConnection;

    /**
     * [__construct]
     *
     * @param SshConnectionManagerService $sshConnection
     */
    public function __construct(
        SshConnectionManagerService $sshConnection
    ) {
        parent::__construct();
        $this->sshConnection = $sshConnection;
    }

    /**
     * [configure]
     *
     * Mengkonfigurasi command, menambahkan deskripsi, argumen, dan opsi.
     *
     * @return void
     */
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

    /**
     * [execute]
     *
     * Mengeksekusi command SSH berdasarkan input user.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
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
