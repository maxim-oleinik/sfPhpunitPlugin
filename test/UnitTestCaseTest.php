<?php
/**
 * Base TestCase test
 *
 * @author  Maxim Oleinik <maxim.oleinik@gmail.com>
 */
require_once(dirname(__FILE__).'/_init.php');


/**
 * Test
 */
class sfPhpunitPlugin_UnitTestCaseTest extends sfBasePhpunitTestCase
{
    /**
     * Make test helper
     */
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
