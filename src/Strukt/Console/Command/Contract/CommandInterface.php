<?php

namespace Strukt\Console\Command\Contract;

use Strukt\Console\Input;
use Strukt\Console\Output;

/**
* @author Moderator <pitsolu@gmail.com>
*/
interface CommandInterface{
	
	/**
	 * @param \Strukt\Console\Input $in
	 * @param \Strukt\Console\Output $out
	 */
	public function execute(Input $in, Output $out);
}