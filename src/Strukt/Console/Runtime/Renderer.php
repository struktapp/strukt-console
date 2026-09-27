<?php

declare (strict_types = 1);

namespace Strukt\Console\Runtime;

use Strukt\Console\Definition\CommandDefinition;

final class Renderer
{
    public function help(CommandDefinition $d): string
    {
        $out = ($d->description ? $d->description . "\n\n" : '') . "Usage:\n  " . ($d->usage ?? $d->name . ($d->arguments ? ' ' . implode(' ', array_map(fn($a) => '<' . $a->name . '>', $d->arguments)) : '') . ($d->options ? ' [options]' : '')) . "\n";
        if ($d->arguments) {$out .= "\nArguments:\n";foreach ($d->arguments as $a) {
            $out .= $this->line($a->name, $a->description . ($a->required ? ' (required)' : '') . ($a->enum ? ' [' . implode(', ', $a->enum) . ']' : ''));
        }
        }
        if ($d->options) {$out .= "\nOptions:\n";foreach ($d->options as $o) {$name = '--' . $o->name . ($o->flag ? '' : '=<' . $o->type . '>');if ($o->short) {
            $name = '-' . $o->short . ', ' . $name;
        }

            $extra  = $o->description . ($o->required ? ' (required)' : '') . ($o->default !== null ? ' [default: ' . var_export($o->default, true) . ']' : '');
            $out   .= $this->line($name, $extra);}}
        return $out;
    }

    public function list(array $definitions): string
    {
        usort($definitions, fn($a, $b) => strcmp($a->name, $b->name));
        $out = "Available commands:\n\n";
        foreach ($definitions as $d) {
            $out .= sprintf("  %-28s %s\n", $d->name, $d->description);
        }

        return $out;
    }

    public function markdown(CommandDefinition $d): string
    {
        $out = '# `' . $d->name . "`\n\n" . $d->description . "\n\n## Usage\n\n```bash\n" . ($d->usage ?? $d->name) . "\n```\n";
        if ($d->arguments) {$out .= "\n## Arguments\n\n| Name | Type | Required | Description |\n|---|---|---|---|\n";foreach ($d->arguments as $a) {
            $out .= "| `{$a->name}` | `{$a->type}` | " . ($a->required ? 'Yes' : 'No') . " | " . str_replace('|', '\\|', $a->description) . " |\n";
        }
        }
        if ($d->options) {$out .= "\n## Options\n\n| Option | Type | Required | Description |\n|---|---|---|---|\n";foreach ($d->options as $o) {
            $out .= '| `--' . $o->name . '` | `' . $o->type . '` | ' . ($o->required ? 'Yes' : 'No') . ' | ' . str_replace('|', '\\|', $o->description) . ' |' . "\n";
        }
        }
        return $out;
    }

    private function line(string $name, string $description): string
    {return sprintf("  %-28s %s\n", $name, $description);}
}
