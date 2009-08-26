<?php
/**
 * sfBasePhpunitUnitTestCase is the super class for all unit
 * tests using PHPUnit.
 *
 * @package    sfPhpunitPlugin
 * @subpackage lib
 * @author     Frank Stelzer <dev@frankstelzer.de>
 */
abstract class sfBasePhpunitTestCase extends PHPUnit_Framework_TestCase
{
	/**
	* The sfContext instance
	*
	* @var sfContext
	*/
	private $context = null;

	/**
	 * Dev hook for custom "setUp" stuff
	 * Overwrite it in your test class, if you have to execute stuff before a test is called.
	 */
	protected function _start()
	{
	}

	/**
	 * Dev hook for custom "tearDown" stuff
	 * Overwrite it in your test class, if you have to execute stuff after a test is called.
	 */
	protected function _end()
	{
	}

	/**
	* Please do not touch this method and use _start directly!
	*/
	public function setUp()
	{
		$this->_start();
	}

	/**
	* Please do not touch this method and use _end directly!
	*/
	public function tearDown()
	{
		$this->_end();
	}

	/**
	* A unit test does not have loaded the whole symfony context on start-up, but
	* you can create a working instance if you need it with this method
	* (taken from the bootstrap file).
	*
	* @return sfContext
	*/
	protected function getContext()
	{
		if (!$this->context)
		{
			// ProjectConfiguration is already required in the bootstrap file
			//require_once( dirname( __FILE__ ) . '/../../../config/ProjectConfiguration.class.php' );
			$configuration = ProjectConfiguration::getApplicationConfiguration($this->getApplication(), $this->getEnvironment(), true);
			$this->context = sfContext::createInstance($configuration);
		}

		return $this->context;
	}

	/**
	* Returns application name
	*
	* @return string
	*/
	protected function getApplication()
	{
		throw new sfException('getApplication implementation is missing');
	}

	/**
	* Returns environment name
	*
	* @return string
	*/
	protected function getEnvironment()
	{
		throw new sfException('getEnvironment implementation is missing');
	}
}