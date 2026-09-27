# `user:create`

Creates a new application user.

## Usage

```bash
user:create <email> [options]
```

## Arguments

| Name | Type | Required | Description |
|---|---|---|---|
| `email` | `string` | Yes | The user's email address. |

## Options

| Option | Type | Required | Description |
|---|---|---|---|
| `--name` | `string` | No | User's display name. |
| `--admin` | `bool` | No | Create the user as an administrator. |
| `--password` | `string` | Yes | Initial password. |
