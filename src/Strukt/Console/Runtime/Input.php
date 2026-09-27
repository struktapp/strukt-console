<?php

declare (strict_types = 1);

namespace Strukt\Console\Runtime;

use InvalidArgumentException;
use Strukt\Console\Definition\CommandDefinition;

final class Input
{
    public function parse(CommandDefinition $definition, array $argv): array
    {
        array_shift($argv);
        array_shift($argv);
        $arguments   = [];
        $options     = [];
        $positionals = [];
        for ($i = 0, $n = count($argv); $i < $n; $i++) {
            $token = $argv[$i];
            if ($token === '--') {array_push($positionals, ...array_slice($argv, $i + 1));
                break;}
            if (str_starts_with($token, '--')) {
                $raw            = substr($token, 2);
                [$name, $value] = array_pad(explode('=', $raw, 2), 2, null);
                $def            = $this->option($definition, $name);
                if ($def->flag) {
                    $value = true;
                } elseif ($value === null) {
                    $value = $argv[++$i] ?? throw new InvalidArgumentException("Option --{$name} requires a value.");
                }

                $options[$name] = $this->convert($value, $def->type, $def->enum, "--{$name}");
                continue;
            }
            if (str_starts_with($token, '-') && $token !== '-') {
                $short = substr($token, 1);
                $def   = null;
                foreach ($definition->options as $candidate) {
                    if ($candidate->short === $short) {$def = $candidate;
                        break;}
                }

                if (! $def) {
                    throw new InvalidArgumentException("Unknown option -{$short}.");
                }

                $value               = $def->flag ? true : ($argv[++$i] ?? throw new InvalidArgumentException("Option -{$short} requires a value."));
                $options[$def->name] = $this->convert($value, $def->type, $def->enum, "-{$short}");
                continue;
            }
            $positionals[] = $token;
        }
        foreach ($definition->arguments as $index => $def) {
            if (array_key_exists($index, $positionals)) {
                $arguments[$def->name] = $this->convert($positionals[$index], $def->type, $def->enum, $def->name);
            } elseif ($def->default !== null) {
                $arguments[$def->name] = $def->default;
            } elseif ($def->required) {
                throw new InvalidArgumentException("Argument <{$def->name}> is required.");
            } else {
                $arguments[$def->name] = null;
            }

        }
        if (count($positionals) > count($definition->arguments)) {
            throw new InvalidArgumentException('Too many arguments supplied.');
        }

        foreach ($definition->options as $def) {
            if (! array_key_exists($def->name, $options)) {
                if ($def->default !== null) {
                    $options[$def->name] = $def->default;
                } elseif ($def->flag) {
                    $options[$def->name] = false;
                } elseif ($def->required) {
                    throw new InvalidArgumentException("Option --{$def->name} is required.");
                } else {
                    $options[$def->name] = null;
                }

            }
        }
        return [$arguments, $options];
    }

    private function option(CommandDefinition $definition, string $name): \Strukt\Console\Definition\OptionDefinition
    {
        foreach ($definition->options as $def) {
            if ($def->name === $name) {
                return $def;
            }
        }

        throw new InvalidArgumentException("Unknown option --{$name}.");
    }

    private function convert(mixed $value, string $type, array $enum, string $name): mixed
    {
        $value = match (strtolower($type)) {
            'int', 'integer'  => filter_var($value, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE),
            'float' => filter_var($value, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE),
            'bool', 'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            default => (string) $value,
        };
        if ($value === null) {
            throw new InvalidArgumentException("Invalid value for {$name}.");
        }

        if ($enum && ! in_array((string) $value, array_map('strval', $enum), true)) {
            throw new InvalidArgumentException("Invalid value for {$name}. Allowed: " . implode(', ', $enum));
        }

        return $value;
    }
}
