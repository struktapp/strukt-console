<?php

declare (strict_types = 1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Strukt\Console\Parser\DocBlockParser;

/**
 * @command test:example
 * @description Example command.
 * @usage test:example <name> [options]
 *
 * @argument name
 * @required
 * @description A name.
 *
 * @option verbose
 * @flag
 * @description Verbose output.
 *
 * @option count
 * @type int
 * @default 3
 * @description Number of times.
 */
final class DocBlockParserTestCommand
{
}

final class DocBlockParserTest extends TestCase
{
    public function testParsesCommandDocumentation(): void
    {
        $d = (new DocBlockParser())->parse(DocBlockParserTestCommand::class);
        self::assertSame('test:example', $d->name);
        self::assertSame('Example command.', $d->description);
        self::assertCount(1, $d->arguments);
        self::assertTrue($d->arguments[0]->required);
        self::assertCount(2, $d->options);
        self::assertTrue($d->options[0]->flag);
        self::assertSame(3, $d->options[1]->default);
    }
}
