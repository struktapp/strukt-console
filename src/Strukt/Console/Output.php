<?php

namespace Strukt\Console;

/**
* Console Output class
*
* @author Moderator <pitsolu@gmail.com>
*/
class Output{

	/**
	* @var array $output
	*/
	private $output;

	/**
	* Constructor
	*/
	public function __construct(){

		$this->output = array();
	}

	/**
	* Buffered output/stacked output
	*
	* @param string $output
	*
	* @return static
	*/
	public function add($output):static{

		$this->output[] = $output;

		return $this;
	}

	/**
	* Is output stack empty
	*
	* @return bool
	*/
	public function isEmpty():bool{

		return count($this->output) == 2;
	}

	/**
	* Flush output
	*
	* @return string
	*/
	public function write():string{

		return implode("", $this->output);
	}
}