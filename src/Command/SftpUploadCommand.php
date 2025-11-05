<?php

declare(strict_types=1);

namespace Vigihdev\Ssh\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputArgument, InputInterface, InputOption};
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Vigihdev\Ssh\Client\SftpClient;
use Vigihdev\Ssh\Service\SshConnectionManagerService;

#[AsCommand(name: 'sftp:upload', description: 'Upload file menggunakan koneksi SFTP/SSH')]
final class SftpUploadCommand extends Command
{

    /**
     * @var SplFileInfo[] $localFiles
     */
    private array $localFiles = [];

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
            ->setName('sftp:upload')
            ->setDescription('Upload file atau direktori ke server remote lewat SSH')
            ->addArgument('source', InputArgument::REQUIRED, 'File atau direktori sumber yang akan diupload (bisa lebih dari satu)')
            ->addArgument('destination', InputArgument::REQUIRED, 'Path tujuan di server remote')
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
            ->addOption('recursive', 'r', InputOption::VALUE_NONE, 'Upload direktori secara rekursif')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Tampilkan rencana upload tanpa mengirim file')
            ->addOption('pattern', 'p', InputOption::VALUE_OPTIONAL, 'Pattern file (regex)')
            ->addOption('include', 'i', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Include pattern', [])
            ->addOption('exclude', 'e', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Exclude pattern', [])

            ->setHelp(
                <<<'HELP'
                Contoh penggunaan:
                  <info>php bin/console sftp:upload feature public/backup</info>
                  <info>php bin/console sftp:upload feature public -p "/\.html$/"</info>
                  <info>php bin/console sftp:upload feature public -r -i "*.json" -i "*.html"</info>
                  <info>php bin/console sftp:upload feature public -r -e "*.tmp" -e "*.log"</info>
                HELP
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $connectionName = $input->getOption('connection');
        $destination = $input->getArgument('destination');
        $source = $input->getArgument('source');
        $recursive = $input->getOption('recursive');
        $pattern = $input->getOption('pattern');
        $includePatterns = $input->getOption('include');
        $excludePatterns = $input->getOption('exclude');
        $dryRun = $input->getOption('dry-run');

        if ($pattern && !str_starts_with($pattern, '/')) {
            $pattern = '/' . str_replace('.', '\.', str_replace('*', '.*', $pattern)) . '/';
        }


        if (!$this->sshConnection->hasServiceConnection($connectionName)) {
            $io->error("Connection {$connectionName} tidak tersedia");
            return Command::FAILURE;
        }

        $sftp = $this->sshConnection->getConnection($connectionName)->getSftpClient();

        $io->title('SFTP Upload Command');
        $io->note([
            "Koneksi: {$connectionName}",
            "Destination: {$destination}",
            "Remote Path : {$sftp->pwd()}",
            "Recursive: " . ($recursive ? 'Yes' : 'No'),
            "Dry run: " . ($dryRun ? 'Yes' : 'No'),
        ]);

        // Resolve sumber
        $this->resolveSource(
            sourceDir: $source,
            pattern: $pattern,
            recursive: $recursive,
            includePatterns: $includePatterns,
            excludePatterns: $excludePatterns,
            io: $io
        );

        if (empty($this->localFiles)) {
            $io->warning("Tidak ada file yang ditemukan di: {$this->localFiles}");
            return Command::FAILURE;
        }

        $this->uploadDirectory(
            sftp: $sftp,
            destination: $destination,
            io: $io
        );

        return Command::SUCCESS;
    }

    private function uploadDirectory(
        SftpClient $sftp,
        string $destination,
        SymfonyStyle $io
    ) {

        $successCount = 0;
        $errorCount = 0;

        foreach ($this->localFiles as $file) {
            try {
                $relativePath = $file->getRelativePathname();
                $remotePath = rtrim($destination, '/') . '/' . $relativePath;

                $remoteDir = dirname($remotePath);
                if (!$sftp->isDir($remoteDir) && !$sftp->createDirectory($remoteDir)) {
                    throw new \RuntimeException("Gagal membuat remote directory: {$remoteDir}");
                }

                // Upload file
                $remoteFile = Path::join($sftp->pwd(), $remoteDir, $file->getFilename());
                $stream = fopen($file->getRealPath(), 'r');
                $data = file_get_contents($file->getRealPath());

                if ($sftp->put($remoteFile, $data)) {
                    fclose($stream);
                    $fileSize = $file->getSize();
                    $io->writeln("✓ <info>{$file->getRelativePathname()}</info> (" . $this->formatBytes($fileSize) . ")");
                    $successCount++;
                } else {
                    throw new \RuntimeException("Gagal upload file");
                }
            } catch (\Exception $e) {
                $io->warning("Gagal upload: {$relativePath} - {$e->getMessage()}");
                $errorCount++;
            }
        }

        // Summary
        if ($errorCount > 0) {
            $io->warning("Upload selesai: {$successCount} berhasil, {$errorCount} gagal");
        } else {
            $io->success("Upload selesai: {$successCount} file berhasil diupload");
        }

        return $errorCount === 0 ? Command::SUCCESS : Command::FAILURE;
    }

    private function resolveSource(
        string $sourceDir,
        ?string $pattern,
        bool $recursive,
        array $includePatterns,
        array $excludePatterns,
        SymfonyStyle $io
    ): void {

        $finder = new Finder();
        $finder->files()->in($sourceDir);

        // Konfigurasi finder
        if ($pattern) {
            $finder->name($pattern);
        }

        if ($recursive) {
            $finder->depth('>= 0');
        } else {
            $finder->depth(0);
        }

        foreach ($includePatterns as $include) {
            $finder->name($include);
        }

        foreach ($excludePatterns as $exclude) {
            $finder->notName($exclude);
        }

        if (!$finder->hasResults()) {
            return;
        }

        $io->writeln("Menemukan <info>{$finder->count()} file</info>...");
        foreach ($finder as $file) {
            $this->localFiles[] = $file;
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
