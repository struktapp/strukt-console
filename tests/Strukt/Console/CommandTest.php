<?php

class CommandTest extends PHPUnit_Framework_TestCase{

	public function setUp(){

		$this->app = new Strukt\Console\Application();
		$this->app->add(new Command\DoctrineGenerateEntities);

		$name = "Strukt Console";
		$isWin = $this->app->isWindows();
		$this->$name = sprintf(($isWin)?"\n%s\n":"\033[1;32m%s\n%s\033[0m\n", $name, str_repeat("=", strlen($name)));
	}

	public function testRunFullValidCommand(){

		$mysqlCmd = "console orm:convert-mapping --from-database --namespace Payroll\AuthModule\Model xml app/src/Payroll/AuthModule/Model";

		$result = $this->app->run(explode(" ", $mysqlCmd));
		$hash = end(explode("\n", trim((string)$result)));
		
		$this->assertEquals("from-db:ns[Payroll\AuthModule\Model]:type[xml]:path[app/src/Payroll/AuthModule/Model]", $hash);
	}

	public function testWithoutBooleanSwithFromDataBase(){

		$mysqlCmd = "console orm:convert-mapping --namespace Payroll\AuthModule\Model xml app/src/Payroll/AuthModule/Model";

		$result = $this->app->run(explode(" ", $mysqlCmd));
		$hash = end(explode("\n", trim((string)$result)));
		
		$this->assertEquals("ns[Payroll\AuthModule\Model]:type[xml]:path[app/src/Payroll/AuthModule/Model]", $hash);
	}

	public function testWithoutInputSwitchNamespace(){

		$mysqlCmd = "console orm:convert-mapping xml app/src/Payroll/AuthModule/Model";

		$result = $this->app->run(explode(" ", $mysqlCmd));
		$hash = end(explode("\n", trim((string)$result)));
		
		$this->assertEquals("type[xml]:path[app/src/Payroll/AuthModule/Model]", $hash);
	}

	public function testValidationWrongFileGenerationType(){

		$mysqlCmd = "console orm:convert-mapping json app/src/Payroll/AuthModule/Model";

		$result = $this->app->run(explode(" ", $mysqlCmd));
		$hash = end(explode("\n", trim((string)$result)));
		
		$this->assertEquals(sprintf("\033[1;41m%s\033[0m", "Invalid type [json]! Supported types are (xml|yaml|annotation)!"), $hash);
	}

	public function testValidationNoInputOrInsufficientInput(){

		$mysqlCmd = "console orm:convert-mapping";

		$result = $this->app->run(explode(" ", $mysqlCmd));
		$hash = end(explode("\n", trim((string)$result)));
		
		$this->assertEquals(sprintf("\033[1;41m%s\033[0m", "Argument [path_to_entities] is required!"), $hash);
	}
}