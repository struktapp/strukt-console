<?php

declare (strict_types = 1);

namespace Strukt\Console\Parser;

use ReflectionClass;
use RuntimeException;
use Strukt\Console\Definition\ArgumentDefinition;
use Strukt\Console\Definition\CommandDefinition;
use Strukt\Console\Definition\OptionDefinition;

final class DocBlockParser
{
    public function parse(string $class): CommandDefinition
    {
        $reflection = new ReflectionClass($class);
        $doc        = $reflection->getDocComment() ?: '';
        if ($doc === '') {
            throw new RuntimeException("Command class {$class} has no PHPDoc.");
        }

        $lines       = $this->lines($doc);
        $command     = null;
        $description = '';
        $usage       = null;
        $arguments   = [];
        $options     = [];
        $current     = null;

        foreach ($lines as $line) {
            if (preg_match('/^@command\\s+(.+)$/', $line, $m)) {
                $command = trim($m[1]);
                $current = null;
                continue;
            }
            if (preg_match('/^@description\\s*(.*)$/', $line, $m)) {
                if ($current !== null) {
                    $current['description'] = trim($m[1]);
                    if ($current['kind'] === 'argument') {
                        $arguments[$current['name']] = $current;
                    } else {
                        $options[$current['name']] = $current;
                    }

                } else {
                    $description = trim($m[1]);
                }
                continue;
            }
            if (preg_match('/^@usage\\s+(.+)$/', $line, $m)) {
                $usage   = trim($m[1]);
                $current = null;
                continue;
            }
            if (preg_match('/^@argument\\s+([A-Za-z0-9_-]+)$/', $line, $m)) {
                $current          = ['kind' => 'argument', 'name' => $m[1], 'description' => '', 'type' => 'string', 'required' => false, 'default' => null, 'enum' => []];
                $arguments[$m[1]] = $current;
                continue;
            }
            if (preg_match('/^@option\\s+([A-Za-z0-9_-]+)$/', $line, $m)) {
                $current        = ['kind' => 'option', 'name' => $m[1], 'description' => '', 'type' => 'string', 'required' => false, 'default' => null, 'flag' => false, 'short' => null, 'enum' => []];
                $options[$m[1]] = $current;
                continue;
            }
            if ($current !== null) {
                if (preg_match('/^@(required)$/', $line)) {$current['required'] = true;} elseif (preg_match('/^@(flag)$/', $line)) {$current['flag'] = true;
                    $current['type']                            = 'bool';} elseif (preg_match('/^@type\\s+(.+)$/', $line, $m)) {$current['type'] = trim($m[1]);} elseif (preg_match('/^@default\\s+(.+)$/', $line, $m)) {$current['default'] = $this->scalar(trim($m[1]));} elseif (preg_match('/^@short\\s+(.+)$/', $line, $m)) {$current['short'] = ltrim(trim($m[1]), '-');} elseif (preg_match('/^@enum\\s+(.+)$/', $line, $m)) {$current['enum'] = array_values(array_filter(array_map('trim', explode(',', $m[1])), 'strlen'));} elseif (preg_match('/^@description\\s*(.*)$/', $line, $m)) {$current['description'] = trim($m[1]);}
                if ($current['kind'] === 'argument') {$arguments[$current['name']] = $current;} else { $options[$current['name']] = $current;}
            }
        }

        if (! $command) {
            throw new RuntimeException("Command class {$class} is missing @command.");
        }

        if ($description === '') {
            $description = trim((string) preg_replace('/^.*?@command[^\n]*\n?/s', '', $doc));
        }

        return new CommandDefinition(
            $command,
            trim($description),
            $usage,
            $class,
            array_map(fn(array $x) => new ArgumentDefinition($x['name'], $x['description'], $x['type'], $x['required'], $x['default'], $x['enum']), array_values($arguments)),
            array_map(fn(array $x) => new OptionDefinition($x['name'], $x['description'], $x['type'], $x['required'], $x['default'], $x['flag'], $x['short'], $x['enum']), array_values($options)),
        );
    }

    private function lines(string $doc): array
    {
        $doc    = preg_replace('/^\s*\/\*\*?/', '', $doc) ?? $doc;
        $doc    = preg_replace('/\*\/\s*$/', '', $doc) ?? $doc;
        $result = [];
        foreach (preg_split('/\R/', $doc) as $line) {
            $line = preg_replace('/^\\s*\\* ?/', '', $line) ?? $line;
            $line = trim($line);
            if ($line !== '') {
                $result[] = $line;
            }

        }
        return $result;
    }

    private function scalar(string $value): mixed
    {
        return match (strtolower($value)) {
            'null'  => null,
            'true'  => true,
            'false' => false,
            default => is_numeric($value) ? (str_contains($value, '.') ? (float) $value : (int) $value) : trim($value, "\\\"'")
        };
    }
}
