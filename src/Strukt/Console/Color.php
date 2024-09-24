<?php

namespace Strukt\Console;

class Color{

    protected static $halt = false;

    protected static $typeArr = [

        "plain"=>'0',
        "bold"=>'1',
        "dark"=>"2",
        "italic"=>"3",
        "underline"=>'4'
    ];

    /**
     * @var array
     */
    protected static $colorArr = [

        'skyblue'   => '29',
        'default'   => '39',
        'black'     => '30',
        'red'       => '31',
        'green'     => '32',
        'yellow'    => '33',
        'blue'      => '34',
        'purple'    => '35',
        'cyan'      => '36',
        'white'     => '37',

        'dark-gray'     => '90',
        'light-red'     => '91',
        'light-green'   => '92',
        'light-yellow'  => '93',
        'light-blue'    => '94',
        'light-magenta' => '95',
        'light-cyan'    => '96',
        'white'         => '97',

        'bg-default'    => '49',
        'bg-black'      => '40',
        'bg-red'        => '41',
        'bg-green'      => '42',
        'bg-yellow'     => '43',
        'bg-blue'       => '44',
        'bg-magenta'    => '45',
        'bg-cyan'       => '46',

        'bg-light-gray'     => '47',
        'bg-dark-gray'      => '100',
        'bg-light-red'      => '101',
        'bg-light-green'    => '102',
        'bg-light-yellow'   => '103',
        'bg-light-blue'     => '104',
        'bg-light-magenta'  => '105',
        'bg-light-cyan'     => '106',
        'bg-white'          => '107'
    ];

    private static function halt(bool $halt = true){

        static::$halt = $halt;
    }

    private static function format($colorType){

        list($color, $type) = array("default", "plain");
        @list($color, $type) = preg_split("/:/", $colorType);

        $colorCode = @sprintf("%d;%sm", static::$typeArr[$type], static::$colorArr[$color]);

        return sprintf("\033[%s", $colorCode);
    }

    public static function write($colorType, $str){

        if(!static::$halt)
            return sprintf("%s%s\033[0m", static::format($colorType), $str);

        return $str;
    }

    public static function writeln($colorType, $str){

        if(!static::$halt)
            return sprintf("%s%s\033[0m\n", static::format($colorType), $str);

        return $str;
    }
}