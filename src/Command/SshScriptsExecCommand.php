<?php

declare(strict_types=1);

namespace Vigihdev\Ssh\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;
use Vigihdev\Ssh\Service\SshConnectionManagerService;

#[AsCommand(
    name: 'ssh:scripts:exec',
    description: 'Execute predefined SSH scripts/snippets'
)]
final class SshScriptsExecCommand extends Command
{
    private array $scripts = [];

    public function __construct(
        private readonly SshConnectionManagerService $sshConnection,
        private readonly string $scriptsFileYaml
    ) {
        parent::__construct();
        $this->loadScripts();
    }

    protected function configure(): void
    {
        $this
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
            ->addOption(
                'script',
                's',
                InputOption::VALUE_REQUIRED,
                'Nama script/snippet yang akan dijalankan',
                null,
                function () {
                    return array_keys($this->scripts);
                }
            )
            ->addOption('list', 'l', InputOption::VALUE_NONE, 'List semua available scripts')
            ->addOption('info', 'i', InputOption::VALUE_REQUIRED, 'Show detail info tentang script')
            ->addOption('search', null, InputOption::VALUE_REQUIRED, 'Search scripts by keyword')
            ->setHelp(
                <<<'HELP'
                Execute predefined SSH scripts dengan mudah!
                
                Contoh penggunaan:
                  <info>php bin/console ssh:scripts:exec -c sirent -s mycnf</info>
                  <info>php bin/console ssh:scripts:exec --list</info>
                  <info>php bin/console ssh:scripts:exec --info mycnf</info>
                  <info>php bin/console ssh:scripts:exec --search "mysql"</info>
                  
                Scripts didefinisikan di: config/ssh_scripts.yaml
                HELP
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $connectionName = $input->getOption('connection');
        $scriptName = $input->getOption('script');
        $listScripts = $input->getOption('list');
        $scriptInfo = $input->getOption('info');
        $searchTerm = $input->getOption('search');

        // Handle different operations
        if ($searchTerm) {
            return $this->searchScripts($searchTerm, $io);
        }

        if ($scriptInfo) {
            return $this->showScriptInfo($scriptInfo, $io);
        }

        if ($listScripts) {
            return $this->listScripts($io);
        }

        if ($scriptName) {
            return $this->executeScript($scriptName, $connectionName, $io);
        }

        $io->error("Harus provide salah satu option: --script, --list, --info, atau --search");
        $io->writeln("Gunakan <info>--help</info> untuk melihat usage lengkap");
        return Command::FAILURE;
    }

    private function executeScript(string $scriptName, string $connectionName, SymfonyStyle $io): int
    {
        // Validate script exists
        if (!isset($this->scripts[$scriptName])) {
            $io->error("Script '{$scriptName}' tidak ditemukan");
            $io->writeln("Gunakan <info>--list</info> untuk melihat daftar scripts");
            return Command::FAILURE;
        }

        // Validate connection
        if (!$this->sshConnection->hasServiceConnection($connectionName)) {
            $io->error("Connection '{$connectionName}' tidak tersedia");
            return Command::FAILURE;
        }

        $script = $this->scripts[$scriptName];
        $sshClient = $this->sshConnection->getConnection($connectionName)->getSshClient();

        $io->title("Executing Script: {$scriptName}");
        $io->definitionList(
            ['Description' => $script['description'] ?? 'No description'],
            ['Connection' => $connectionName],
            ['Command' => $script['command']]
        );

        $io->writeln('');

        try {
            $result = $sshClient->exec($script['command']);
            $io->writeln($result);

            $io->success("Script '{$scriptName}' executed successfully");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error("Execution failed: " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function listScripts(SymfonyStyle $io): int
    {
        $io->title('Available SSH Scripts');

        if (empty($this->scripts)) {
            $io->warning('Tidak ada scripts yang terdefinisi di config/ssh_scripts.yaml');
            return Command::SUCCESS;
        }

        $rows = [];
        foreach ($this->scripts as $name => $script) {
            $rows[] = [
                "<info>{$name}</info>",
                $script['description'] ?? 'No description',
            ];
        }

        $io->table(['Script Name', 'Description'], $rows);
        $io->note([
            'Gunakan: <info>php bin/console ssh:scripts:exec -c CONNECTION -s SCRIPT_NAME</info>',
            'Contoh: <info>php bin/console ssh:scripts:exec -c sirent -s mycnf</info>'
        ]);

        return Command::SUCCESS;
    }

    private function showScriptInfo(string $scriptName, SymfonyStyle $io): int
    {
        if (!isset($this->scripts[$scriptName])) {
            $io->error("Script '{$scriptName}' tidak ditemukan");
            $io->writeln("Gunakan <info>--list</info> untuk melihat daftar scripts");
            return Command::FAILURE;
        }

        $script = $this->scripts[$scriptName];

        $io->title("Script Info: {$scriptName}");
        $io->definitionList(
            ['Description' => $script['description'] ?? 'No description'],
            ['Command' => "<comment>{$script['command']}</comment>"],
            ['Usage' => "php bin/console ssh:scripts:exec -c CONNECTION -s {$scriptName}"]
        );

        return Command::SUCCESS;
    }

    private function searchScripts(string $searchTerm, SymfonyStyle $io): int
    {
        $foundScripts = [];

        foreach ($this->scripts as $name => $script) {
            $searchableText = strtolower($name . ' ' . $script['description'] . ' ' . $script['command']);
            if (str_contains($searchableText, strtolower($searchTerm))) {
                $foundScripts[$name] = $script;
            }
        }

        if (empty($foundScripts)) {
            $io->warning("Tidak ada scripts yang match dengan: '{$searchTerm}'");
            return Command::SUCCESS;
        }

        $io->title("Search Results for: '{$searchTerm}'");

        $rows = [];
        foreach ($foundScripts as $name => $script) {
            $rows[] = [
                "<info>{$name}</info>",
                $script['description'] ?? 'No description',
            ];
        }

        $io->table(['Script Name', 'Description'], $rows);
        return Command::SUCCESS;
    }

    private function loadScripts(): void
    {

        try {
            $config = Yaml::parseFile($this->scriptsFileYaml);
            $this->scripts = $config['scripts'] ?? [];
        } catch (\Exception $e) {
            $this->scripts = [];
        }
    }
}
