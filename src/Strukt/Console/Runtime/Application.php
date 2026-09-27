<?php

declare (strict_types = 1);

namespace Strukt\Console\Runtime;

use RuntimeException;
use Strukt\Console\Discovery\CommandDiscovery;
use Strukt\Console\Parser\DocBlockParser;
use Throwable;

final class Application
{
    public function __construct(
        private string $commandDirectory,
        private ?string $docsDirectory = null,
    ) {}

    public function run(array $argv): int
    {
        try {
            $classes     = (new CommandDiscovery())->discover($this->commandDirectory);
            $parser      = new DocBlockParser();
            $definitions = [];
            foreach ($classes as $class) {$d = $parser->parse($class);
                $definitions[$d->name]        = $d;}
            $command = $argv[1] ?? null;
            if ($command === null || in_array($command, ['list', 'commands'], true)) {echo(new Renderer())->list(array_values($definitions));return 0;}
            if ($command === 'help' || $command === '--help' || $command === '-h') {$target = $argv[2] ?? null;if (! $target) {echo(new Renderer())->list(array_values($definitions));return 0;}$command = $target;}
            if ($command === 'docs') {
                return $this->docs($definitions);
            }

            if (! isset($definitions[$command])) {
                throw new RuntimeException("Unknown command '{$command}'. Use 'list' to see available commands.");
            }

            if (in_array('--help', array_slice($argv, 2), true) || in_array('-h', array_slice($argv, 2), true)) {echo(new Renderer())->help($definitions[$command]);return 0;}
            [$arguments, $options] = (new Input())->parse($definitions[$command], $argv);
            $instance              = new $definitions[$command]->class();
            if (! $instance instanceof CommandInterface) {
                throw new RuntimeException('Command must implement CommandInterface.');
            }

            return $instance->execute($arguments, $options);
        } catch (Throwable $e) {
            fwrite(STDERR, "Error: {$e->getMessage()}\n");
            return 1;
        }
    }

    private function docs(array $definitions): int
    {
        $dir = $this->docsDirectory ?? getcwd() . '/docs/commands';
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $renderer = new Renderer();
        foreach ($definitions as $d) {
            file_put_contents($dir . '/' . str_replace(['/', ' '], ['-', '-'], $d->name) . '.md', $renderer->markdown($d));
        }

        echo 'Generated ' . count($definitions) . " command documentation in {$dir}\n";
        return 0;
    }
}
