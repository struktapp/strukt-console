<?php

declare (strict_types = 1);

namespace Strukt\Console\Discovery;

use RuntimeException;
use Strukt\Console\Runtime\CommandInterface;

final class CommandDiscovery
{
    public function discover(string $directory): array
    {
        if (! is_dir($directory)) {
            throw new RuntimeException("Command directory does not exist: {$directory}");
        }

        $before = get_declared_classes();
        $files  = [];
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory));
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $files[] = $file->getPathname();
            }
        }

        sort($files);
        foreach ($files as $file) {
            require_once $file;
        }

        $commands = array_values(array_filter(
            array_diff(get_declared_classes(), $before),
            fn(string $class) => is_subclass_of($class, CommandInterface::class),
        ));

        // Requiring the same files again yields no new classes, so when the
        // commands were already loaded in this process derive the candidates
        // from the file layout instead of relying on the declared class diff.
        if ($commands === []) {
            foreach ($files as $file) {
                $source = (string) file_get_contents($file);
                if (!preg_match('/namespace\s+([^;]+);/', $source, $namespace)) {
                    continue;
                }

                $candidate = rtrim($namespace[1], " \t\n\r") . '\\' . basename($file, '.php');
                if (is_subclass_of($candidate, CommandInterface::class)) {
                    $commands[] = $candidate;
                }
            }

            $commands = array_values(array_unique($commands));
        }

        return $commands;
    }
}
