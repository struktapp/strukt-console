<?php

namespace Command;

use Strukt\Console\Input;
use Strukt\Console\Output;

/**
* user:add          Add User
*/
class UserAdd extends \Strukt\Console\Command{ 

	public function execute(Input $in, Output $out){

		$username = $in->getInput("Username:");
		$password = trim($in->getMaskedInput("Password:"));
		$confirm = trim($in->getMaskedInput("Confirm Password:"));

		if($password!=$confirm)
			throw new \Exception("Passwords do not match!");

		$out->add(sprintf("%s:%s:%s", $username, $password, $confirm));
	}
}