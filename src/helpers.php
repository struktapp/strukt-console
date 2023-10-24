<?php

use Strukt\Console\DocBlockParser;
use Strukt\Console\Color;

if(!function_exists("docblock")){

	function docblock(string $class){

		if(class_exists($class))
			return (new DocBlockParser($class))->parse();

		return null;
	}
}

if(!function_exists("color")){

	function color(string $color_type, string $content){

		return Color::write($color_type, $content);
	}
}

if(!function_exists("colorln")){

	function colorln(string $color_type, string $content){

		return Color::writeln($color_type, $content);
	}
}