<?php

namespace Strukt\Console\Command\Contract;

use Strukt\Console\Input;
use Strukt\Console\Output;

interface CommandInterface{
	
	public function execute(Input $in, Output $out);
}