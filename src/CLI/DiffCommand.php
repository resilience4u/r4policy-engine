<?php

declare(strict_types=1);

namespace R4Policy\CLI;

use R4Policy\Validator\PolicyValidator;
use R4Policy\Loader\YamlPolicyLoader;
use R4Policy\Loader\JsonPolicyLoader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class DiffCommand extends Command
{
    protected static $defaultName = 'diff';

    public function __construct(private readonly YamlPolicyLoader $loader)
    {
        parent::__construct('diff');
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Show differences between two policy files (YAML or JSON)')
            ->addArgument('old', InputArgument::REQUIRED, 'Path to the original policy file')
            ->addArgument('new', InputArgument::REQUIRED, 'Path to the updated policy file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $oldPath = (string) $input->getArgument('old');
        $newPath = (string) $input->getArgument('new');

        if (!file_exists($oldPath) || !file_exists($newPath)) {
            $output->writeln("<error>One or both files not found.</error>");
            return Command::FAILURE;
        }

        $validator = new PolicyValidator();
        $loaderOld = str_ends_with($oldPath, '.json')
            ? new JsonPolicyLoader($validator)
            : new YamlPolicyLoader($validator);
        $loaderNew = str_ends_with($newPath, '.json')
            ? new JsonPolicyLoader($validator)
            : new YamlPolicyLoader($validator);

        try {
            $a = array_column(array_map(fn($p) => $p->toArray(), $loaderOld->load($oldPath)), null, 'name');
            $b = array_column(array_map(fn($p) => $p->toArray(), $loaderNew->load($newPath)), null, 'name');

            $removed = array_diff_key($a, $b);
            $added = array_diff_key($b, $a);
            $maybeChanged = array_intersect_key($a, $b);

            foreach ($removed as $name => $_) {
                $output->writeln("<fg=red>- removed</>  {$name}");
            }

            foreach ($added as $name => $_) {
                $output->writeln("<fg=green>+ added</>    {$name}");
            }

            foreach ($maybeChanged as $name => $left) {
                $right = $b[$name];
                if (json_encode($left) !== json_encode($right)) {
                    $output->writeln("<fg=yellow>~ changed</> {$name}");
                }
            }

            if (empty($removed) && empty($added) && empty($maybeChanged)) {
                $output->writeln("<comment>No differences detected.</comment>");
            }

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $output->writeln("<error>Failed to diff policies: {$e->getMessage()}</error>");
            return Command::FAILURE;
        }
    }
}
