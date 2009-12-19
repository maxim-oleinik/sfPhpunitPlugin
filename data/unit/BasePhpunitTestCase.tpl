<?php

/**
 * Base test class for all app unit tests
 */
abstract class {baseTestClassName} extends sfPHPUnitTestCase
{
    /**
     * SetUp
     */
    final public function setUp()
    {
        parent::setUp();

        // Your code
    }


    /**
     * TearDown
     */
    final public function tearDown()
    {
        // Your code

        parent::tearDown();
    }


    /**
     * Returns database connection to wrap tests with transaction
     */
    protected function getConnection()
    {
        return Doctrine_Manager::getInstance()->getConnection('doctrine');
    }


    /**
     * Creates new helper
     *
     * @return myTestObjectHelper
     */
    protected function makeHelper()
    {
        return new myTestObjectHelper;
    }

}
