<?php

namespace Strukt\Console;

/**
* DocBlockParser class
* 
* Extract docblock from class
*
* @author Moderator <pitsolu@gmail.com>
*/
class DocBlockParser{

	/**
	* Documentation block
	*
	* @var string $block
	*/
	private $block;

	/**
	* Constructor
	*
	* @param string $class class name
	*/
	public function __construct($class){

		$reflector = new \ReflectionClass($class);

		$this->block = $reflector->getDocComment();
	}

	/**
	* get DocBlock without comment tokens
	*
	* This function is used to display contents of the 
	* help in the commandline
	*
	* @return string
	*/
	public function getBlock(){

		return implode("\n ", array_map(function($line){

			return trim(trim($line, "*"), "/");

		}, explode("\n", $this->block)));
	}

	/**
	* Break up and clean up DocBlock
	*
	* @param string $rawBlock
	*
	* @return array
	*/
	private function sanitize($rawBlock){

		return array_map(function($line){ 

			return preg_replace('!\s+!', ' ', trim(ltrim($line, "*")));

		}, explode("\n", trim(trim(trim($rawBlock, "/**"), "*/"))));
	}

	/**
	* Analyze and get info from DocBlock
	*
	* Get useful elements from DocBlock i.e
	* usage, args, options, aliases and descriptors
	*
	* @return array
	*/
	public function parse(){

		$docBlockList = $this->sanitize($this->block);

		foreach($docBlockList as $line){

			if(!empty($line)){

				if(empty($case))
					$case = "command";
				
				if(!empty($case) && in_array(strtolower($line), array(

					"usage:", 
					"arguments:", 
					"options:"

				))){

					$case = trim(strtolower($line),":");
					continue;
				}

				switch($case){

					case "command":

						list($cmdName, $cmdDescr) = explode(" ", $line, 2);

						$blockList["command"] = array(

							"alias"=>$cmdName, 
							"descr"=>$cmdDescr
						);

					break;
					case "usage":

						$parts = explode(" ", $line);
						$command = array_shift($parts);

						while($part = array_shift($parts)){

							if(preg_match("/^\[[-\w]+$/", $part)){

								$part = str_replace(array("["), "", $part);
								array_shift($parts);
								$blockList["usage"][$part] = array(

									"required"=>false, 
									"input"=>true
								);
							}							
							elseif(preg_match("/^\[[-\w]+\]$/", $part)){

								$part = str_replace(array("[","]"), "", $part);
								$blockList["usage"][$part] = array(

									"required"=>false, 
									"input"=>false
								);
							}							
							elseif(preg_match("/^(-|--)\w+$/", $part)){

								array_shift($parts);
								$blockList["usage"][$part] = array(

									"required"=>true, 
									"input"=>true
								);
							}							
							elseif(preg_match("/^<\w+>$/", $part)){

								$part = str_replace(array("<",">"), "", $part);
								$blockList["usage"][$part] = array(

									"required"=>true
								);
							}							
							elseif(preg_match("/^\[<\w+>\]$/", $part)){

								$part = str_replace(array("[","]","<",">"), "", $part);
								$blockList["usage"][$part] = array(

									"required"=>false
								);
							}
						}

					break;
					case "arguments":

						list($argument, $descr) = explode(" ", $line, 2);

						$blockList["arguments"][$argument]["descr"] = $descr;
						$blockList["arguments"][$argument]["optional"] = (strpos($line, "optional") !== false);

					break;
					case "options":

						$parts = explode(" ", $line, 3);

						$hasAlias=false;
						if(count($parts) == 3){

							$hasAlias = preg_match("/^-\w$/", next($parts));
							if($hasAlias)
								list($option, $alias, $descr) = $parts;

							if(!$hasAlias)
								list($option, $input, $descr) = $parts;
						}
							
						if(count($parts) == 2)
							list($option, $input) = $parts;

						$details["descr"] = @$descr;

						if($hasAlias){

							$details["alias"] = $alias;
							$blockList["aliases"][$alias] = $option;
						}

						$blockList["options"][$option] = $details;

					break;	
				}
			}
		}

		return $blockList;
	}
}