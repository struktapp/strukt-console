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
	* @return \Strukt\Console\Output
	*/
	public function add($output){

		$this->output[] = $output;

		return $this;
	}

	/**
	* Is output stack empty
	*
	* @return void
	*/
	public function isEmpty(){

		return count($this->output) == 2;
	}

	/**
	* Flush output
	*
	* @return string
	*/
	public function write(){

		return implode("", $this->output);
	}
}