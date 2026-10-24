<?php

declare (strict_types = 1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Strukt\Console\Definition\CommandDefinition;
use Strukt\Console\Runtime\Renderer;

final class RendererTest extends TestCase
{
    public function testListSortsCommandsAlphabetically(): void
    {
        $renderer = new Renderer();

        $out = $renderer->list([
            $this->definition('zeta:run', 'Runs zeta'),
            $this->definition('alpha:run', 'Runs alpha'),
        ]);

        self::assertStringContainsString('Available commands:', $out);
        self::assertLessThan(strpos($out, 'zeta:run') ?: 0, strpos($out, 'alpha:run') ?: 0);
    }

    public function testSectionsRenderTitlesAndCommands(): void
    {
        $renderer = new Renderer();

        $out = $renderer->sections([
            [
                'title' => null,
                'commands' => [$this->definition('app:make', 'Generate scaffolding')],
            ],
            [
                'title' => 'Package Publisher',
                'commands' => [$this->definition('package:ls', 'List packages')],
            ],
        ]);

        self::assertStringContainsString('Available commands:', $out);
        self::assertStringContainsString('app:make', $out);
        self::assertStringContainsString('Package Publisher', $out);
        self::assertStringContainsString('package:ls', $out);
        self::assertGreaterThan(strpos($out, 'Package Publisher') ?: 0, strpos($out, 'package:ls') ?: 0);
    }

    public function testSectionsSkipBlankTitles(): void
    {
        $renderer = new Renderer();

        $out = $renderer->sections([
            [
                'title' => '',
                'commands' => [$this->definition('cache:clear', 'Clear the cache')],
            ],
        ]);

        $expected = "Available commands:\n\n" . sprintf("  %-28s %s\n", 'cache:clear', 'Clear the cache');

        self::assertSame($expected, $out);
    }

    private function definition(string $name, string $description): CommandDefinition
    {
        return new CommandDefinition($name, $description, null, 'Some\\Command\\Class', [], []);
    }
}