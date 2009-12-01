<?php
require_once(dirname(__FILE__).'/_init.php');



/**
 * Test
 */
class sfPhpunitPlugin_sfPHPUnitTestCaseTest extends sfBasePhpunitTestCase
{
    public function getConnection()
    {

    }


    protected function makeHelper()
    {
        return new sfPhpunitPlugin_TestHelper;
    }


    /**
     * Init helper
     */
    public function testInitHelper()
    {
        $this->assertEquals($this->makeHelper(), $this->helper);
    }

}
