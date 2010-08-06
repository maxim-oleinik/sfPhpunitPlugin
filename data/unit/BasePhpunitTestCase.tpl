<?php

/**
 * Base test class for all app unit tests
 */
abstract class {baseTestClassName} extends sfPHPUnitTestCase
{
    /**
     * Returns database connection to wrap tests with transaction
     */
    protected function getConnection()
    {
        return Propel::getConnection();
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
