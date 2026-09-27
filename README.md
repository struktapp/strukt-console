# CommandSpec

**Documentation-first PHP CLI commands.**

CommandSpec turns a structured PHPDoc block on a command class into a runnable CLI command, automatic argument/option validation, `--help` output, and generated Markdown documentation.

The goal is simple: **define the command once, close to the code that implements it.**

## Requirements

- PHP 8.2+
- Composer

## Install

```bash
composer require strukt/console
```

Copy the example configuration:

```bash
cp console.php.example console.php
```

Create `src/Command/CreateUser.php`:

```php
<?php

declare(strict_types=1);

namespace App\Command;

use CommandSpec\Runtime\CommandInterface;

/**
 * @command user:create
 * @description Creates a new application user.
 * @usage user:create <email> [options]
 *
 * @argument email
 * @type string
 * @required
 * @description The user's email address.
 *
 * @option name
 * @type string
 * @default Anonymous
 * @description User's display name.
 *
 * @option admin
 * @flag
 * @description Create the user as an administrator.
 *
 * @option password
 * @type string
 * @required
 * @description Initial password.
 */
final class CreateUser implements CommandInterface
{
    public function execute(array $arguments, array $options): int
    {
        // Application logic here.
        return 0;
    }
}
```

Run it:

```bash
vendor/bin/console user:create john@example.com --name="John Doe" --admin --password=secret
```

Get help:

```bash
vendor/bin/console user:create --help
```

List commands:

```bash
vendor/bin/console
vendor/bin/console list
```

Generate Markdown documentation:

```bash
vendor/bin/console docs
```

## Command specification

### `@command`

Required. The public CLI command name.

```text
@command user:create
```

### `@description`

Short command description.

### `@usage`

Optional explicit usage line. If omitted, CommandSpec builds one from the arguments and options.

### Arguments

```text
@argument email
@type string
@required
@description The user's email address.
```

Supported types currently include `string`, `int`, `integer`, `float`, `bool`, and `boolean`.

Optional defaults:

```text
@default 10
```

Enumerations:

```text
@enum admin,editor,viewer
```

### Options

A normal value option:

```text
@option name
@type string
@description Display name.
```

A flag:

```text
@option verbose
@flag
@description Enable verbose output.
```

Short option:

```text
@option output
@short o
@type string
@description Output file.
```

Required option:

```text
@option password
@required
@description Initial password.
```

## Runtime contract

Every command implements:

```php
interface CommandInterface
{
    public function execute(array $arguments, array $options): int;
}
```

Returning `0` means success. Any non-zero return value is propagated to the shell.

## Configuration

Create `command-spec.php` in the consuming project:

```php
<?php

return [
    'commands' => __DIR__.'/src/Command',
    'docs' => __DIR__.'/docs/commands',
];
```

## Design philosophy

CommandSpec intentionally uses an explicit mini-DSL instead of trying to infer executable behavior from arbitrary prose. This keeps documentation human-readable while making the executable specification deterministic.

The same command definition is the source for:

- CLI routing
- argument parsing
- option parsing
- validation
- `--help`
- command listing
- generated Markdown documentation

## Roadmap

Planned extensions include:

- PHP Attributes as an alternative to PHPDoc
- shell completion for Bash, Zsh and Fish
- nested command groups
- aliases
- hidden/deprecated commands
- interactive prompts
- input/output abstractions
- richer validation rules
- automatic API documentation
- command discovery through Composer metadata
- `--version`
- colored terminal rendering

## License

MIT. See `LICENSE`.
