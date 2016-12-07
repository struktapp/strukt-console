<?php

namespace Command;

use Strukt\Console\Input;
use Strukt\Console\Output;

/**
* mysql:login          MySQL Authentication
* 
* Usage:
*   
*      mysql:login <database> --username <username> [--host <127.0.0.1>]
*
* Arguments:
*
*      database  MySQL database name - optional argument
* 
* Options:
* 
*      --username -u   MySQL Username
*      --host -h       MySQL Host - optional default 127.0.0.1
*/
class MySQLAuth extends \Strukt\Console\Command{ 

	public function execute(Input $in, Output $out){

		$password = $in->getMaskedInput("Password:");
		$database = $in->get("database");
		$username = $in->get("username");
		$host = $in->get("host");

		if(empty($host))
			$host = "localhost";

		$out->add(sprintf("mysql://%s:%s@%s/%s", $username, $password, $host, $database));
	}
}