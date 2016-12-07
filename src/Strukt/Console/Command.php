<?php

namespace Strukt\Console;

use Strukt\Console\Input;
use Strukt\Console\Output;

/**
* Abstract Command class
*
* @author Moderator <pitsolu@gmail.com>
*/
abstract class Command{

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