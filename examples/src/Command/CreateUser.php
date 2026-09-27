<?php

declare (strict_types = 1);

namespace Example\Command;

use Strukt\Console\Runtime\CommandInterface;

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
        echo json_encode([
            'email'    => $arguments['email'],
            'name'     => $options['name'],
            'admin'    => $options['admin'],
            'password' => '[redacted]',
        ], JSON_PRETTY_PRINT) . PHP_EOL;
        return 0;
    }
}
