<?php

declare (strict_types = 1);

namespace Strukt\Console\Definition;

final readonly class CommandDefinition
{
    public function __construct(
        public string $name,
        public string $description,
        public ?string $usage,
        public string $class,
        public array $arguments = [],
        public array $options = [],
    ) {}
}
