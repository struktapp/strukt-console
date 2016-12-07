<?php

namespace Command;

use Strukt\Console\Input;
use Strukt\Console\Output;

/**
* orm:convert-mapping       Generate Annotation Mappings
* 
* Usage:
*	
*      orm:convert-mapping [--from-database] [--namespace <namespace>] [<type_to_generate>] <path_to_entities>
*
* Arguments:
*
*      type_to_generate     Argument options (xml|yaml|annotation)
*      path_to_entities     Path to generate entities
*
* Options:
*
*      --from-database      Database name
*      --namespace          Namespace
*/
class DoctrineGenerateEntities extends \Strukt\Console\Command{

	public function execute(Input $in, Output $out){
		
		$genTypes = array("xml","yaml","annotation");

		$type = $in->get("type_to_generate");
		$fromDb = $in->get("from-database");
		$path = $in->get("path_to_entities");
		$ns = $in->get("namespace");

		if(!empty($type))
			if(!in_array($type, $genTypes))
				throw new \Exception(sprintf("Invalid type [%s]! Supported types are (%s)!", $type, implode("|", $genTypes)));

		if(!empty($ns))
			$result[] = sprintf("ns[%s]", $ns);

		if(!empty($type))
			$result[] = sprintf("type[%s]", $type);

		if(!empty($path))
			$result[] = sprintf("path[%s]", $path);

		if($fromDb)
			$out->add(sprintf("from-db:%s", implode(":", $result)));

		if(!$fromDb)
			$out->add(implode(":", $result));
	}
}