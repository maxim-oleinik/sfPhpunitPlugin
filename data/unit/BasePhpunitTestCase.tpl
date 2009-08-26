<?php
require_once dirname(__FILE__).'/bootstrap/unit.php';

abstract class {baseTestClassName} extends sfBasePhpunitTestCase
{
	/**
	 * Dev hook for custom "setUp" stuff
	 */
	protected function _start()
	{
	}

	/**
	 * Dev hook for custom "tearDown" stuff
	 */
	protected function _end()
	{
	}

	/**
	* Returns application name
	*
	* @return string
	*/
	protected function getApplication()
	{
		return '{application}';
	}

	/**
	* Returns environment name
	*
	* @return string
	*/
	protected function getEnvironment()
	{
		return '{env}';
	}
}