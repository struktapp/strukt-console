<?php

namespace Strukt\Console;

/**
* Console Input class
*
* @author Moderator <pitsolu@gmail.com>
*/
class Input{

	/**
	* DocBlock
	*
	* @var \Strukt\Console\DocBlockParser $docBlock
	*/
	private $docBlock = null;

	/**
	* Synthesised input arguments
	*
	* @var array $args
	*/
	private $args = null;

	/**
	* Raw input arguments
	*
	* @var array $argv
	*/
	private $argv;

	/**
	* Constructor
	*
	* @param array $argv raw arguments
	* @param \Strukt\Console\DocBlockParser $parser
	*/
	public function __construct($argv, \Strukt\Console\DocBlockParser $parser){

		$this->argv = $argv;
		$this->docBlock = $parser;
	}

	/**
	* @throws \Exception
	*
	* @return void
	*/
	private function parse(){

		$docList = $this->docBlock->parse();

		$filename = array_shift($this->argv);
		$command = array_shift($this->argv);

		/**
		* Aquire parameters
		*/
		while($arg = array_shift($this->argv)){

			$quotes = array("'",'"');

			$optionsExists = preg_match("/-[-\w]+/", $arg);
			$argsExists = !empty(key($docList["arguments"]));

			if($optionsExists){

				$optionExists = in_array($arg, array_keys($docList["options"]));
				$optionAliasExists = @in_array($arg, array_keys($docList["aliases"]));

				if($optionExists){

					$args[$arg] = 1;

					if($docList["usage"][$arg]["input"])
						$args[$arg] = str_replace($quotes, "", array_shift($this->argv));	
				}
				
				if(!$optionExists && $optionAliasExists){

					$arg = $docList["aliases"][$arg];
					$args[$arg] = 1;

					if($docList["usage"][$arg]["input"])
						$args[$arg] = str_replace($quotes, "", array_shift($this->argv));	
				}
			}
			
			if(!$optionsExists && $argsExists){

				$args[key($docList["arguments"])] = $arg;
				next($docList["arguments"]);	
			}
			
			if(!$optionsExists && !$argsExists)
				$unknowns[] = $arg;
		}

		/**
		* Validate parameters
		*/
		if(!empty($docList["usage"])){

			foreach($docList["usage"] as $param=>$usage){

				$name = trim(trim($param, "-"),"--");

				$isParamInArgs = @in_array($param, array_keys($args));

				if($isParamInArgs){

					if(empty($args[$param]))
						if($usage["input"])
							throw new \Exception(sprintf("Input required for [%s]!", $param));

					$this->args[$name] = $args[$param];
				}

				$isArgsInDocList = in_array("arguments", array_keys($docList));

				if(!$isParamInArgs && $isArgsInDocList){

					$isParamInDocList = in_array($param, array_keys($docList["arguments"]));

					if($isParamInDocList)
						if($usage["required"])
							throw new \Exception(sprintf("Argument [%s] is required!", $param));

					if($usage["required"])
						throw new \Exception(sprintf("Option [%s] is required!", $param));
				}
			}
		}

		if(!empty($unknowns))
			throw new \Exception(sprintf("Unknown parameter/input [%s]!", current($unknowns)));
	}

	/**
	* get synthesised input arguments
	*
	* @return array
	*/
	public function getInputs(){

		if(is_null($this->args))
			$this->parse();

		return $this->args;
	}

	/**
	* get single input value
	*
	* @param string $key
	*
	* @return string
	*/
	public function get($key){

		if(!is_null($this->args))
			if(in_array($key, array_keys($this->args)))
				return $this->args[$key];

		return null;
	}

	/**
	* Interactive unmasked input
	*
	* Note: uses readline extenstion
	*		history enabled
	*
	* @param string $query prompt text
	*
	* @return string
	*/
	public function getInput($query){

		if(function_exists('readline'))
           $line = readline($query);

        if(!function_exists('readline')){

			echo($query);

			$stdin = fopen('php://stdin', 'r');
			$line = fgets($stdin);
		}

		if(function_exists("readline_add_history"))
			readline_add_history($line);

		return $line;
	}

	/**
	 * MaskedInput by Troels Knak-Nielsen - sitepoint
	 *
	 * Interactively prompts for input without echoing to the terminal.
	 * Requires a bash shell or Windows and won't work with
	 * safe_mode settings (Uses `shell_exec`)
	 *
	 * @param string $prompt
	 *
	 * @return string
	 */
	public function getMaskedInput($prompt = "Enter Password:") {

		$isWin = preg_match('/^win/i', PHP_OS);

		if($isWin){

		    $vbscript = sys_get_temp_dir() . 'prompt_password.vbs';

		    file_put_contents($vbscript, implode(" ", array(
		    		
	    		'wscript.echo(InputBox("',
	      		addslashes($prompt),
	      		'", "", "password here"))'
	      	)));

		    $command = "cscript //nologo " . escapeshellarg($vbscript);
		    $password = rtrim(shell_exec($command));
		    unlink($vbscript);

		    return $password;
	  	} 
	  	
	  	// Else check for Linux
	    $command = "/usr/bin/env bash -c 'echo OK'";
	    if (rtrim(shell_exec($command)) !== 'OK') {

			trigger_error("Can't invoke bash");
			return;
	    }

	    $command = implode(" ", array(

			"/usr/bin/env bash -c 'read -s -p \"",
			addslashes($prompt),
			"\" mypassword && echo \$mypassword'"
		));

	    $password = rtrim(shell_exec($command));
	    echo "\n";
	    
	    return $password;
	}
}