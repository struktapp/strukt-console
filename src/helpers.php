<?php

use Strukt\Console\DocBlockParser;
use Strukt\Console\Color;

helper("console");

if(helper_add("docblock")){

	function docblock(string $class){

		if(class_exists($class))
			return (new DocBlockParser($class))->parse();

		return null;
	}
}

if(helper_add("color")){

	function color(string $color_type, string $content){

		return Color::write($color_type, $content);
	}
}

if(helper_add("colorln")){

	function colorln(string $color_type, string $content){

		return Color::writeln($color_type, $content);
	}
}