<?php

/**
 * sfBasePhpunitUnitTestCase is the super class for all unit
 * tests using PHPUnit.
 *
 * @package    sfPhpunitPlugin
 * @author     Maxim Oleinik <maxim.oleinik@gmail.com>
 */
abstract class sfPHPUnitTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * Do not preserve the global state when running in a separate PHP process.
     * @see PHPUnit_Framework_TestCase::run()
     */
    protected $preserveGlobalState = false;


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
     * Returns database connection
     */
    protected function getConnection()
    {
    }


    /**
     * Creates new test helper
     *
     * @return sfBaseTestObjectHelper
     */
    protected function makeHelper()
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
        // Object helper
        $this->helper = $this->makeHelper();

        // Init concrete test config && autoload
        sfConfig::clear();
        ProjectConfiguration::getApplicationConfiguration($this->getApplication(), $this->getEnvironment(), $debug = true);

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
        try {
            $this->_end();
        } catch (Exception $e) {}


        // Rollback transaction
        if ($conn = $this->getConnection()) {
            $conn->rollback();
        }

        $this->_reset();

        if (!empty($e)) {
            throw $e;
        }
    }


    /**
     * Final clean up
     *
     * @return void
     */
    protected function _reset()
    {
        $this->clearModelsCache();
    }


    /**
     * Clears the model first level cache
     *
     * @see Doctrine_Table::clear()
     */
    protected function clearModelsCache()
    {
        foreach (Doctrine::getLoadedModels() as $modelName) {
            Doctrine::getTable($modelName)->clear();
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


    /**
     * Compare two model objects
     */
    public function assertModels(Doctrine_Record $expected, Doctrine_Record $actual, $message = null)
    {
        $this->assertEquals(get_class($expected), get_class($actual), $message);
        $this->assertEquals($expected->toArray(false), $actual->toArray(false), $message);
    }


    /**
     * Query to find list by conditions
     *
     * @param  string $modelName
     * @param  array  $conditions - array('name' => 'My Article')
     * @return Doctrine_Query
     */
    protected function queryFind($modelName, array $conditions)
    {
        $table = Doctrine_Core::getTable($modelName);
        $query = $table->createQuery('a');

        foreach ($conditions as $column => $condition) {
            $column = $table->getFieldName($column);
            if (null === $condition) {
                $query->andWhere("a.{$column} IS NULL");
            } else {
                $query->andWhere("a.{$column} = ?", $condition);
            }
        }

        return $query;
    }

}
