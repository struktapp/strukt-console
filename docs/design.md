# Design

CommandSpec follows a documentation-first pipeline:

```text
PHPDoc
  ↓
DocBlockParser
  ↓
CommandDefinition
  ├── CLI parser / validation
  ├── help renderer
  └── Markdown renderer
```

The important boundary is `CommandDefinition`. Once parsed, runtime code does not need to know whether the metadata came from PHPDoc, attributes, a generated file, or another source.

This makes Attributes a natural future addition without changing the runtime contract.
