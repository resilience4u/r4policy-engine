<?php

declare(strict_types=1);

namespace R4Policy\CLI;

use R4Policy\Loader\YamlPolicyLoader;
use R4Policy\Loader\JsonPolicyLoader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ValidateCommand extends Command
{
    protected static $defaultName = 'validate';

    public function __construct(private readonly YamlPolicyLoader $loader)
    {
        parent::__construct(self::$defaultName);
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Validate a policy YAML or JSON file')
            ->addArgument('file', InputArgument::REQUIRED, 'Path to the YAML or JSON file containing policies');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');

        if (!file_exists($file)) {
            $output->writeln("<error>File not found: {$file}</error>");
            return Command::FAILURE;
        }

        $loader = str_ends_with($file, '.json')
            ? new JsonPolicyLoader($this->loader->validator)
            : $this->loader;

        try {
            $loader->load($file);
            $output->writeln("<info>OK</info> - {$file} is valid.");
            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $output->writeln("<error>INVALID - {$e->getMessage()}</error>");
            return Command::FAILURE;
        }
    }
}
