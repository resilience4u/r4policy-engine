<?php

declare(strict_types=1);

namespace R4Policy\CLI;

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
        parent::__construct(self::$defaultName);
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Show differences between two policy files (YAML or JSON)')
            ->addArgument('old', InputArgument::REQUIRED, 'Path to the original file')
            ->addArgument('new', InputArgument::REQUIRED, 'Path to the updated file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $old = (string) $input->getArgument('old');
        $new = (string) $input->getArgument('new');

        if (!file_exists($old) || !file_exists($new)) {
            $output->writeln("<error>One or both files not found.</error>");
            return Command::FAILURE;
        }

        $validator = $this->loader->validator;
        $loaderOld = str_ends_with($old, '.json')
            ? new JsonPolicyLoader($validator)
            : $this->loader;
        $loaderNew = str_ends_with($new, '.json')
            ? new JsonPolicyLoader($validator)
            : $this->loader;

        $a = array_column(array_map(fn($p) => $p->toArray(), $loaderOld->load($old)), null, 'name');
        $b = array_column(array_map(fn($p) => $p->toArray(), $loaderNew->load($new)), null, 'name');

        $removed = array_diff_key($a, $b);
        $added   = array_diff_key($b, $a);
        $maybe   = array_intersect_key($a, $b);

        foreach ($removed as $name => $_) $output->writeln("<fg=red>- removed</>  {$name}");
        foreach ($added as $name => $_)   $output->writeln("<fg=green>+ added</>   {$name}");
        foreach ($maybe as $name => $left) {
            $right = $b[$name];
            if (json_encode($left) !== json_encode($right)) {
                $output->writeln("<fg=yellow>~ changed</> {$name}");
            }
        }

        if (empty($removed) && empty($added) && empty($maybe)) {
            $output->writeln("<comment>No differences detected.</comment>");
        }

        return Command::SUCCESS;
    }
}
