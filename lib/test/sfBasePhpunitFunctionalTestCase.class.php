<?php
/**
 * sfBasePhpunitFunctionalTestCase is the super class for all functional
 * tests using PHPUnit.
 * The "getBrowser" method provides the current functional test/browser
 * instance of symfony and you can do anything with it you are used from
 * the normal lime based tests.
 *
 * @package    sfPhpunitPlugin
 * @subpackage lib
 * @author     Frank Stelzer <dev@frankstelzer.de>
 */
abstract class sfBasePhpunitFunctionalTestCase extends PHPUnit_Framework_TestCase
{
	/**
	 * The sfContext instance
	 *
	 * @var sfContext
	 */
	private $context = null;

	/**
	 * The sfBrowser instance
	 *
	 * @var sfBrowser
	 */
	private $testBrowser;

	/**
	 * Dev hook for custom "setUp" stuff
	 *
	 */
	protected function _start()
	{
	}

	/**
	 * Dev hook for custom "tearDown" stuff
	 *
	 */
	protected function _end()
	{
	}

	/**
	 * setUp method for PHPUnit
	 *
	 */
	protected function setUp()
	{
		#$this->testBrowser = new sfPhpunitTestFunctional(new sfBrowser(), $this );
		$this->testBrowser = new sfTestFunctional(new sfBrowser(), new sfPhpunitTest($this));
		$this->_start();
	}

	/**
	 * Returns the sfBrowser instance
	 *
	 * @return sfBrowser
	 */
	public function getBrowser()
	{
		return $this->testBrowser;
	}

	/**
	 * tearDown method for PHPUnit
	 *
	 */
	protected function tearDown()
	{
		$this->_end();
	}

	/*
	 * Returns sfContext instance
	 *
	 * @return sfContext
	 */
	protected function getContext()
	{
		// The sfContext instance is already created in the functional bootstrap file.
		// Therefore we have to fetch only the instance here.
		// Dependency injection would be best, but there is no clean way to do that in this case.
		if(!$this->context)
		{
			$this->context = sfContext::getInstance();
		}

		return $this->context;
	}

}