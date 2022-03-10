<?php

namespace Strukt\Console;

use Strukt\Console\Input;
use Strukt\Console\Output;
use Strukt\Console\Command\Contract\CommandInterface;

/**
* Abstract Command class
*
* @author Moderator <pitsolu@gmail.com>
*/
abstract class Command implements CommandInterface{

	/**
	* Execute command implementation
	*
	* @param \Strukt\Console\Input $in
	* @param \Strukt\Console\Output $out
	*/
	public function execute(Input $in, Output $out){

		//Implementation
	}
}