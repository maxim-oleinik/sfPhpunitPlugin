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
     * Application name
     *
     * Overwrite it in your TestCase
     */
    protected $app = 'frontend';

    /**
     * Environment name
     *
     * Overwrite it in your TestCase
     */
    protected $env = 'test';

    /**
     * myTestObjectHelper
     */
    protected $helper;


    /**
     * Returns database connection
     */
    abstract protected function getConnection();


    /**
     * Returns application name
     *
     * @return string
     */
    protected function getApplication()
    {
        return $this->app;
    }


    /**
     * Returns environment name
     *
     * @return string
     */
    protected function getEnvironment()
    {
        return $this->env;
    }


    /**
     * Returns true if the test should be run in debug mode
     *
     * @return bool
     */
    protected function isDebug()
    {
        return true;
    }


    /**
     * Dev hook for custom "setUp" stuff
     *
     * Overwrite it in your test class, if you have to execute stuff before a test is called.
     */
    protected function _start()
    {
    }


    /**
     * Dev hook for custom "tearDown" stuff
     *
     * Overwrite it in your test class, if you have to execute stuff before a test is called.
     */
    protected function _end()
    {
    }


    /**
     * Custiom setup initialization
     */
    protected function _initialize()
    {
    }


    /**
     * setUp method for PHPUnit
     */
    protected function setUp()
    {
        // Create context once for current app
        $this->getContext();

        // Object helper
        $this->helper = sfBaseTestObjectHelper::getInstance();

        // Custom init
        $this->_initialize();

        // Begin transaction
        if ($conn = $this->getConnection()) {
            $conn->beginTransaction();
        }

        $this->_start();
    }


    /**
     * tearDown method for PHPUnit
     */
    protected function tearDown()
    {
        $this->_end();

        // Rollback transaction
        if ($conn = $this->getConnection()) {
            $conn->rollback();
        }
    }


    /**
     * Creates new context
     */
    protected function createContext()
    {
        $configuration = ProjectConfiguration::getApplicationConfiguration($this->getApplication(), $this->getEnvironment(), $this->isDebug());
        return sfContext::createInstance($configuration);
    }


    /**
     * Returns sfContext
     *
     * @return sfContext
     */
    protected function getContext()
    {
        $app = $this->getApplication();
        if (sfContext::hasInstance($app)) {
            return sfContext::getInstance($app);
        } else {
            return $this->createContext();
        }
    }

}
