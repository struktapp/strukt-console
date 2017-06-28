Strukt Console
==============

[![Latest Stable Version](https://travis-ci.org/pitsolu/strukt-console.svg?branch=master)](https://packagist.org/packages/strukt/console)
[![Latest Stable Version](https://poser.pugx.org/strukt/console/v/stable)](https://packagist.org/packages/strukt/console)
[![Total Downloads](https://poser.pugx.org/strukt/console/downloads)](https://packagist.org/packages/strukt/console)
[![Latest Unstable Version](https://poser.pugx.org/strukt/console/v/unstable)](https://packagist.org/packages/strukt/console)
[![License](https://poser.pugx.org/strukt/console/license)](https://packagist.org/packages/strukt/console)

This is a console framework that utilises docblock to parse command description and format.

## Usage

Sample Command:

```php
namespace Command;

use Strukt\Console\Input;
use Strukt\Console\Output;

/**
* mysql:auth          MySQL Authentication
* 
* Usage:
*   
*      mysql:auth <database> --username <username> --password <password> [--host <127.0.0.1>]
*
* Arguments:
*
*      database  MySQL database name - optional argument
* 
* Options:
* 
*      --username -u   MySQL Username
*      --password -p   MySQL Password
*      --host -h       MySQL Host - optional default 127.0.0.1
*/
class MySQLAuth extends \Strukt\Console\Command{ 

	public function execute(Input $in, Output $out){

		$out->add(sprintf("%s:%s:%s", 
							$in->get("database"), 
							$in->get("username"), 
							$in->get("password")));
	}
}
```

Add this in your executable file:

```php
#!/usr/bin/php
<?php
$app = new Strukt\Console\Application("Strukt Console");
$app->add(new Command\MySQLAuth);
$app->run($_SERVER["argv"]);
```

Call command:

```sh
$ php console mysql:auth payroll -u root -p p@55w0rd
```

Prompt for input and masked input, you may but need not describe promted input 
in command docblock:

```php
...
//prompt for input
$username = $in->getInput("Username:");
$nickname = $in->getInput("Nickname:");
//masked input
$password = $in->getMaskedInput("Password:");
$cpassword = $in->getMaskedInput("Confirm Password:");
...
```
