<?php

declare (strict_types = 1);

namespace Strukt\Console\Runtime;

interface CommandInterface
{
    public function execute(array $arguments, array $options): int;
}
