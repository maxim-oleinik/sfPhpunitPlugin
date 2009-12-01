<?php
require_once(dirname(__FILE__).'/_init.php');



/**
 * Test
 */
class sfPhpunitPlugin_sfPHPUnitTestObjectHelperTest extends PHPUnit_Framework_TestCase
{
    /**
     * Get unique counter
     */
    public function testGetUniqueCounter()
    {
        $helper = new sfBaseTestObjectHelper;
        $this->assertGreaterThan($helper->getUniqueCounter(), $c1 = $helper->getUniqueCounter());

        $helper = new sfBaseTestObjectHelper;
        $this->assertGreaterThan($c1, $c2 = $helper->getUniqueCounter());

        $helper = new sfPhpunitPlugin_TestHelper;
        $this->assertGreaterThan($c2, $helper->getUniqueCounter());
    }


    /**
     * Set up safe classes
     */
    public function testSetupSafeClasses()
    {
        $helper = new sfBaseTestObjectHelper;
        $helper->setSafe(array('one', 'two'));

        $this->assertTrue($helper->isSafe('one'), 'Expected `one` is safe');
        $this->assertTrue($helper->isSafe('two'), 'Expected `two` is safe');
        $this->assertFalse($helper->isSafe('unknown'), 'Expected `unknown` is NOT safe');
    }


    /**
     * Make text
     */
    public function testMakeText()
    {
        $helper = new sfBaseTestObjectHelper;
        $this->assertEquals(
            sprintf('<span class="xss">text %04d</span>', $helper->getUniqueCounter()+1),
            $helper->makeText('text', 'one')
        );


        $helper = new sfBaseTestObjectHelper;
        $helper->setSafe(array('one'));
        $this->assertEquals(
            sprintf('text %04d', $helper->getUniqueCounter()+1),
            $helper->makeText('text', 'one'),
            'Expected no XSS mark'
        );
    }

}
