<?php

namespace Strukt\Console;

/**
* Strukt Console Application class
*
* @author Moderator <pitsolu@gmail.com>
*/
class Application{

	/**
	* List of commands
	*
	* @var array $commands
	*/
	private $commands;

	/**
	* Name of console application
	*
	* @var string $name 
	*/
	private $name;

	/**
	* Left padding 
	*
	* @var int $padlen
	*/
	private $padlen = 0;

	/**
	* Construct
	*
	* @param string $name console application name
	*
	* @return void
	*/
	public function __construct($name=""){

		$this->name = "Strukt Console";

		if(!empty($name))
			$this->name = $name;

		$this->add(new \Strukt\Console\Command\Console);
	}

	public function addCmdSect($category){

		$this->commands[] = Color::writeln("yellow", $category);
	}

	/**
	* Add commands to console application
	*
	* Utiliese {@link DocBlockParser} to extract documentation
	* from command class
	*
	* @param \Strukt\Console\Command $command
	*
	* @return void
	*/
	public function add(\Strukt\Console\Command $command){

		$class = get_class($command);
		$docBlockParser = new \Strukt\Console\DocBlockParser($class);
		$docList = $docBlockParser->parse();

		$cmdAlias = $docList["command"]["alias"];
		$this->commands[$cmdAlias]["object"] = $command;
		$this->commands[$cmdAlias]["docparser"] = $docBlockParser;
		$this->commands[$cmdAlias]["doclist"] = $docList;

		if($this->padlen == 0 || strlen($docList["command"]["alias"]) > $this->padlen)
			$this->padlen = strlen($docList["command"]["alias"]);
	}

	/**
	* Execute console application
	*
	* @param array $argv parse in commandline parameters
	*
	* @return string
	*/
	public function run($argv){

		$isWin = \Strukt\Fs::isWindows();

		$output = new \Strukt\Console\Output();
		$output
			->add("\n")
			->add(sprintf(($isWin)?"%s\n%s\n":Color::write("green:bold", "%s\n%s\n"), $this->name, str_repeat("=", strlen($this->name))));
		
		try{

			if(empty(@$argv[1]))
				$argv[1] = "-h";

			$switches = array("-h", "--help", "-l", "--list");
			$allow = array_merge(array_keys($this->commands), $switches);
			$allow = array_filter($allow, function($var){

				if(!is_numeric($var))
					return $var;
			});

			if(!in_array(@$argv[1], $allow)){

				$allow = array_filter(array_map(function($val) use ($argv){

					similar_text($val, @$argv[1], $perc);

					if($perc > 50)
						return sprintf(" %s", $val);

					return null;

				}, $allow));

				if(empty($allow))
					throw new \Exception("Command does not exists!");

				$output->add("\nOops! Available commands:\n\n");
				$output->add(implode("\n", $allow));
				
				return $output->add("\n")->write();
			}

			switch(@$argv[1]){

				case "--list":
				case "-l":
					$output->add("\n");
					foreach($this->commands as $key=>$command)
						if(is_string($command))
							$output->add($command); 
						elseif(!$command["object"] instanceof \Strukt\Console\Command\Console)
							$output->add(sprintf(($isWin)?"%s %s\n":sprintf(" %s %s\n", 
																		Color::write("skyblue:bold", "%s"), 
																		Color::write("light-blue:bold", "%s")), 
											str_pad($command["doclist"]["command"]["alias"], $this->padlen), 
											$command["doclist"]["command"]["descr"]));
						

				break;
				case "--help":
				case "-h":
					$command = reset($this->commands);
					if(in_array(@$argv[1], $command["doclist"]["aliases"]) ||
						in_array(@$argv[1], array_keys($command["doclist"]["aliases"])))
							$output->add(sprintf(($isWin)?"%s\n":Color::write("light-yellow:bold", "%s"), $command["docparser"]->getBlock()));
				break;
				default:
					$command = $this->commands[@$argv[1]];
					$askHelp = in_array(@$argv[2], array("-h", "--help"));

					if($askHelp)
						$output->add(sprintf(($isWin)?"%s\n":Color::write("light-cyan:bold", "%s"), $command["docparser"]->getBlock()));
					
					if(!$askHelp){

						$input = new \Strukt\Console\Input($argv, $command["docparser"]);
						$input->getInputs();

						$command["object"]->execute($input, $output);
					}
				break;
			}
		}
		catch(\Exception $e){

			return sprintf(($isWin)?"%s\n":Color::writeln("bg-red:bold", "%s"), $e->getMessage());
		}

		if(!$output->isEmpty())
			return $output->add("\n")->write();
	}
}
