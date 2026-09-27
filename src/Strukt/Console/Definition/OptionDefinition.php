<?php

declare (strict_types = 1);

namespace Strukt\Console\Definition;

final readonly class OptionDefinition
{
    public function __construct(
        public string $name,
        public string $description = '',
        public string $type = 'string',
        public bool $required = false,
        public mixed $default = null,
        public bool $flag = false,
        public ?string $short = null,
        public array $enum = [],
    ) {}
}
