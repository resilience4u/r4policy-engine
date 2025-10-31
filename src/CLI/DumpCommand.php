<?php

declare(strict_types=1);

namespace R4Policy\CLI;

use R4Policy\R4PolicyEngine;
use R4Policy\Loader\YamlPolicyLoader;
use R4Policy\Loader\JsonPolicyLoader;
use R4Policy\Evaluator\PolicyEvaluator;
use R4Policy\Telemetry\TelemetryBridge;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class DumpCommand extends Command
{
    protected static $defaultName = 'dump';

    public function __construct(private readonly YamlPolicyLoader $loader)
    {
        parent::__construct(self::$defaultName);
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Dump all policies from a YAML or JSON file')
            ->addArgument('file', InputArgument::REQUIRED, 'Path to the policy file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = (string) $input->getArgument('file');

        if (!file_exists($path)) {
            $output->writeln("<error>File not found: {$path}</error>");
            return Command::FAILURE;
        }

        $loader = str_ends_with($path, '.json')
            ? new JsonPolicyLoader($this->loader->validator)
            : $this->loader;

        $engine = new R4PolicyEngine(
            $loader,
            new PolicyEvaluator(new TelemetryBridge())
        );

        try {
            $engine->loadFrom($path);
            $policies = $engine->all();

            if (empty($policies)) {
                $output->writeln("<comment>No policies found.</comment>");
                return Command::SUCCESS;
            }

            $output->writeln("<info>Loaded policies:</info>");
            foreach ($policies as $p) {
                $output->writeln("- <comment>{$p->name}</comment> [{$p->type}]");
            }

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $output->writeln("<error>Failed to dump policies: {$e->getMessage()}</error>");
            return Command::FAILURE;
        }
    }
}
