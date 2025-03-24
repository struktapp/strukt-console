<?php

use Strukt\Console\DocBlockParser;
use Strukt\Console\Color;

helper("console");

if(helper_add("docblock")){

	/**
	 * @param string $class
	 * 
	 * @return array|null
	 */
	function docblock(string $class):array|null{

		if(class_exists($class))
			return (new DocBlockParser($class))->parse();

		return null;
	}
}

if(helper_add("color")){

	/**
	 * @param string $color_type
	 * @param string $content
	 * 
	 * @return string
	 */
	function color(string $color_type, string $content):string{

		return Color::write($color_type, $content);
	}
}

if(helper_add("colorln")){

	/**
	 * @param string $color_type
	 * @param string $content
	 * 
	 * @return string
	 */
	function colorln(string $color_type, string $content):string{

		return Color::writeln($color_type, $content);
	}
}