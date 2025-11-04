<?php

declare(strict_types=1);

namespace App\Command\Ssh;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

#[AsCommand(name: 'ssh:exec', description: 'Eksekusi koneksi SSH (interaktif / perintah)')]
final class SshExecCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addOption('c', null, InputOption::VALUE_REQUIRED, 'Nama koneksi SSH (misal: satis, sirent, default)')
            ->addOption('interactive', null, InputOption::VALUE_NONE, 'Masuk ke mode interaktif (shell)')
            ->addOption('cmd', null, InputOption::VALUE_OPTIONAL, 'Perintah yang akan dieksekusi di remote host');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $connectionName = $input->getOption('c');

        if (!$connectionName) {
            $io->error('Gunakan opsi -c <nama_koneksi> untuk memilih koneksi.');
            return Command::FAILURE;
        }

        // --- ambil konfigurasi koneksi ---
        $configPath = __DIR__ . '/../../../config/ssh.yaml';
        if (!file_exists($configPath)) {
            $io->error("File konfigurasi tidak ditemukan: {$configPath}");
            return Command::FAILURE;
        }

        $configs = yaml_parse_file($configPath);
        if (!isset($configs[$connectionName])) {
            $io->error("Koneksi '{$connectionName}' tidak ditemukan dalam konfigurasi.");
            return Command::FAILURE;
        }

        $conn = $configs[$connectionName];
        $user = $conn['user'] ?? 'root';
        $host = $conn['host'] ?? null;

        if (!$host) {
            $io->error("Konfigurasi koneksi '{$connectionName}' tidak memiliki host.");
            return Command::FAILURE;
        }

        $sshTarget = "{$user}@{$host}";
        $isInteractive = $input->getOption('interactive');
        $cmd = $input->getOption('cmd');

        // --- mode interaktif ---
        if ($isInteractive) {
            $io->section("Menghubungkan ke {$sshTarget}...");
            sleep(1);

            $process = new Process(['ssh', $sshTarget]);
            $process->setTty(true);
            $process->run();
            return Command::SUCCESS;
        }

        // --- mode eksekusi cepat ---
        if ($cmd) {
            $io->section("Menjalankan perintah di {$sshTarget}: {$cmd}");
            $process = new Process(['ssh', $sshTarget, $cmd]);
            $process->run(function ($type, $buffer) use ($output) {
                $output->write($buffer);
            });
            return $process->isSuccessful() ? Command::SUCCESS : Command::FAILURE;
        }

        $io->warning('Tidak ada opsi --interactive atau --cmd diberikan.');
        return Command::SUCCESS;
    }
}
