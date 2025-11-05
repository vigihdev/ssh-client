<?php

declare(strict_types=1);

namespace Vigihdev\Ssh\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputArgument, InputInterface, InputOption};
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\{Filesystem, Path};
use Vigihdev\Ssh\Client\SftpClient;
use Vigihdev\Ssh\Exception\{CommandException, DirectoryException};
use Vigihdev\Ssh\Service\SshConnectionManagerService;

#[AsCommand(name: 'sftp:download', description: 'Download dengan koneksi SSH')]
final class SftpDownloadCommand extends Command
{
    /**
     * @param SshConnectionManagerService $sshConnection
     */
    public function __construct(
        private readonly SshConnectionManagerService $sshConnection
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('connection', 'c', InputOption::VALUE_REQUIRED, 'Nama koneksi SSH (misal: satis, sirent, default)', 'default')
            ->addArgument('remote_path', InputArgument::REQUIRED, 'Path file atau directory di server remote')
            ->addArgument('local_path', InputArgument::OPTIONAL, 'Path lokal untuk menyimpan', '.')
            ->addOption('recursive', 'r', InputOption::VALUE_NONE, 'Download directory secara recursive')
            ->setHelp(
                <<<'HELP'
                Contoh penggunaan:
                  <info>php bin/console sftp:download public feature/</info>
                  <info>php bin/console sftp:download public --recursive</info>
                  <info>php bin/console sftp:download public -r -c satis</info>
                HELP
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $connectionName = $input->getOption('connection');
        $remotePath = $input->getArgument('remote_path');
        $localPath = $input->getArgument('local_path');
        $recursive = $input->getOption('recursive');

        if (! $this->sshConnection->hasServiceConnection($connectionName)) {
            $io->error("Connection {$connectionName} tidak tersedia");
            return Command::FAILURE;
        }

        $sftp = $this->sshConnection->getConnection($connectionName)->getSftpClient();

        $io->title('SFTP Download Command');
        $io->note([
            "Koneksi: {$connectionName}",
            "Remote path: {$remotePath}",
            "Local path: {$localPath}",
            "Recursive: " . ($recursive ? 'Yes' : 'No')
        ]);

        try {
            $this->ensureLocalDirectory($localPath, $input, $io, new Filesystem());
            if ($sftp->isDir($remotePath)) {
                return $this->downloadDirectory($sftp, $remotePath, $localPath, $recursive, $io);
            }
            if ($sftp->isFile($remotePath)) {
                return $this->downloadFile($sftp, $remotePath, $localPath, $io);
            }
            throw new CommandException();
            return Command::FAILURE;
        } catch (\Exception $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Download directory dari server remote ke lokal
     *
     * @param SftpClient $sftp Instance SftpClient untuk koneksi SFTP
     * @param string $remoteDir Path directory di server remote yang akan didownload
     * @param string $localPath Path lokal untuk menyimpan file yang didownload
     * @param bool $recursive Apakah download dilakukan secara recursive
     * @param SymfonyStyle $io Instance SymfonyStyle untuk output console
     *
     * @return int Kode status eksekusi command (SUCCESS atau FAILURE)
     */
    private function downloadDirectory(
        SftpClient $sftp,
        string $remoteDir,
        string $localPath,
        bool $recursive,
        SymfonyStyle $io
    ): int {

        $io->writeln("Downloading directory: <comment>{$remoteDir}</comment>");
        $collection = $sftp->lists(directory: $remoteDir, recursive: $recursive);

        if ($collection->isEmpty()) {
            $io->warning("Directory kosong: {$remoteDir}");
            return Command::SUCCESS;
        }

        // Filter hanya file (bukan directory) dan tanpa hidden files
        $files = $collection->withoutHidden();

        if ($files->isEmpty()) {
            $io->warning("Tidak ada file yang bisa didownload di: {$remoteDir}");
            return Command::SUCCESS;
        }

        $io->writeln("Menemukan <info>{$files->count()} file</info>...");
        $sftp->chdir($remoteDir);

        $fs = new Filesystem();
        $successCount = 0;
        $errorCount = 0;

        foreach ($files as $file) {
            try {
                $localFile = Path::join(getcwd(), $localPath, $remoteDir, $file);
                $dirname = pathinfo($localFile, PATHINFO_DIRNAME);

                // Buat directory jika belum ada
                if (!$fs->exists($dirname)) {
                    $fs->mkdir($dirname, 0755);
                }

                // Validasi directory writable
                if (!is_writable($dirname)) {
                    throw new \RuntimeException("Directory tidak writable: {$dirname}");
                }

                // Download file
                if ($sftp->downloadFile($file, $localFile)) {
                    $fileSize = file_exists($localFile) ? filesize($localFile) : 0;
                    $io->writeln("✓ <info>{$file}</info> (" . $this->formatBytes($fileSize) . ")");
                    $successCount++;
                } else {
                    throw new \RuntimeException("Gagal mendownload file");
                }
            } catch (\Exception $e) {
                $io->warning("Gagal download: {$file} - {$e->getMessage()}");
                $errorCount++;
            }
        }

        // Summary report
        if ($errorCount > 0) {
            $io->warning("Download selesai: {$successCount} berhasil, {$errorCount} gagal");
        } else {
            $io->success("Download selesai: {$successCount} file berhasil didownload");
        }

        return $errorCount === 0 ? Command::SUCCESS : Command::FAILURE;
    }

    /**
     * Download single file dari server remote ke lokal
     *
     * @param SftpClient $sftp Instance SftpClient untuk koneksi SFTP
     * @param string $remoteFile Path file di server remote yang akan didownload
     * @param string $localPath Path lokal untuk menyimpan file yang didownload
     * @param SymfonyStyle $io Instance SymfonyStyle untuk output console
     *
     * @return int Kode status eksekusi command (SUCCESS atau FAILURE)
     */
    private function downloadFile(
        SftpClient $sftp,
        string $remoteFile,
        string $localPath,
        SymfonyStyle $io
    ): int {
        $fs = new Filesystem();

        // pastikan remote file valid
        if (! $sftp->isFile($remoteFile)) {
            $io->error("Remote file tidak ditemukan: {$remoteFile}");
            return Command::FAILURE;
        }

        // jika local path adalah direktori, tambahkan nama file
        if (is_dir($localPath) || str_ends_with($localPath, '/')) {
            $filename = basename($remoteFile);
            $localFile = Path::join($localPath, $filename);
        } else {

            // mungkin user menulis target file langsung (misal: ./featured/custom.html)
            $dirname = pathinfo($localPath, PATHINFO_DIRNAME);
            if (! $fs->exists($dirname)) {
                $io->warning("Directory lokal tidak ditemukan: {$dirname}");
                if ($io->confirm("Buat directory '{$dirname}' sekarang?", true)) {
                    $fs->mkdir($dirname, 0755);
                    $io->success("Directory berhasil dibuat: {$dirname}");
                } else {
                    $io->error("Directory tidak ditemukan: {$dirname}");
                    return Command::FAILURE;
                }
            }
            $localFile = $localPath;
        }

        try {
            if ($sftp->downloadFile($remoteFile, $localFile)) {
                $fileSize = file_exists($localFile) ? filesize($localFile) : 0;
                $io->success("✓ File berhasil diunduh: {$localFile} (" . $this->formatBytes($fileSize) . ")");
                return Command::SUCCESS;
            }

            throw new \RuntimeException("Gagal mendownload file: {$remoteFile}");
        } catch (\Exception $e) {
            $io->error("Gagal mendownload file: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }


    private function ensureLocalDirectory(
        string $path,
        InputInterface $input,
        SymfonyStyle $io,
        Filesystem $fs
    ): void {
        $fullPath = Path::join(getcwd(), $path);

        if (!$fs->exists($fullPath)) {
            $io->warning("Directory lokal tidak ditemukan: {$path}");

            $auto = $input->hasOption('no-interaction') && $input->getOption('no-interaction');
            if ($auto || $io->confirm("Buat directory '{$path}' sekarang?", true)) {
                $fs->mkdir($fullPath, 0755);
                $io->success("Directory berhasil dibuat: {$path}");
            } else {
                throw DirectoryException::directoryNotFound($path);
            }
        }
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
