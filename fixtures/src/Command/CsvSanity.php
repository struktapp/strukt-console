<?php

namespace Command;

use Strukt\Console\Input;
use Strukt\Console\Output;

/**
* csv:run          Sums up amount in rows of input csv file and gives out an output file
* 
* Usage:
*   
*      csv:run <csv_in> <csv_out>
*
* Arguments:
*
*      csv_in   Input CSV file
*      csv_out  Ouput CSV file
*/
class CsvSanity extends \Strukt\Console\Command{ 

	public function execute(Input $in, Output $out){

		// $csvIn = $in->get("csv_in");

		// $csvOut = $in->get("csv_out");

		// print_r(array(

		// 	"csvIn"=>$csvIn,
		// 	"csvOut"=>$csvOut
		// ));

		// $out->add("Command was successful.");
	}
}