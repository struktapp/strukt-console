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

        $before   = get_declared_classes();
        $files    = [];
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

        $classes = array_diff(get_declared_classes(), $before);
        return array_values(array_filter($classes, fn(string $class) => is_subclass_of($class, CommandInterface::class)));
    }
}
