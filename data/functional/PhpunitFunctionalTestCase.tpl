<?php
require_once dirname(__FILE__).'/../../{baseTestName}.class.php';

/**
* Functional test class for module "{moduleName}" of application "{application}".
*
* Call this test with:
* phpunit test/phpunit/functional/{application}/{testName}.php
*/
class {testName} extends {baseTestName}
{

	public function test1()
	{
		$browser = $this->getBrowser();
		
		$browser->
		get('/{moduleName}/index')->

		with('request')->begin()->
		isParameter('module', '{moduleName}')->
		isParameter('action', 'index')->
		end()->

		with('response')->begin()->
		isStatusCode(200)->
		checkElement('body', '!/This is a temporary page/')->
		end()
		;
	}
	
}