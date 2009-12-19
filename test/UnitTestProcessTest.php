<?php
/**
 * Test process test
 *
 * Dev hooks and transaction wrapping
 *
 * @author  Maxim Oleinik <maxim.oleinik@gmail.com>
 */

require_once(dirname(__FILE__).'/_init.php');


/**
 * Test connection
 */
class sfPHPUnitPlugin_sfPHPUnitTestCaseTest_Connction
{
    private $_test;

    public function __construct(sfPHPUnitTestCase $test)
    {
        $this->_test = $test;
    }

    public function beginTransaction()
    {
        $this->_test->log .= ' beginTransaction';
    }

    public function rollback()
    {
        $this->_test->log .= ' rollback';
    }
}


/**
 * Test
 */
class sfPHPUnitPlugin_UnitTestProcessTest extends sfPHPUnitTestCase
{
    public $conn = null;
    public $log  = '';


    /**
     * Get stub connection
     */
    public function getConnection()
    {
        return $this->conn;
    }


    /**
     * Dev SetUp
     */
    public function _start()
    {
        $this->log .= ' start';
        if (!empty($this->startError)) {
            $this->log .= ' startError';
            throw new Exception('Start error');
        }
    }


    /**
     * Dev TearDown
     */
    public function _end()
    {
        $this->log .= ' end';
        if (!empty($this->endError)) {
            $this->log .= ' endError';
            throw new Exception('End error');
        }
    }


    /**
     * Clean up
     */
    protected function _reset()
    {
        $this->log .= ' reset';
    }


    /**
     * stub test: OK
     */
    public function _testOk()
    {
        $this->log .= ' _testOk';
    }


    /**
     * stub test: Fail
     */
    public function _testFail()
    {
        $this->log .= ' _testFail';
        $this->fail('Error');
    }


    /**
     * Make test
     *
     * @param  string $name
     * @return sfBasePhpunitTestCase
     */
    private function _makeTest($name)
    {
        $class = __CLASS__;
        $test = new $class($name);
        $test->conn = new sfPHPUnitPlugin_sfPHPUnitTestCaseTest_Connction($test);

        return $test;
    }


    // Tests
    // -------------------------------------------------------------------------


    /**
     * Dev hooks
     */
    public function testDevHooks()
    {
        $test = $this->_makeTest($name = '_testOk');
        $test->conn = null;

        $test->run();
        $this->assertEquals(" start {$name} end reset", $test->log);
    }


    /**
     * Wrap test with transaction
     */
    public function testWrapTestWithTransaction()
    {
        $test = $this->_makeTest($name = '_testOk');

        $test->run();
        $this->assertEquals(" beginTransaction start {$name} end rollback reset", $test->log, $name);
    }


    /**
     * Wrap failed test with transaction
     */
    public function testWrapFailedTestWithTransaction()
    {
        $test = $this->_makeTest($name = '_testFail');

        $test->run();
        $this->assertEquals(" beginTransaction start {$name} end rollback reset", $test->log, $name);
    }


    /**
     * Start hook exception
     */
    public function testStartHookException()
    {
        $test = $this->_makeTest($name = '_testOk');
        $test->startError = true;

        $test->run($result = new PHPUnit_Framework_TestResult);
        $this->assertEquals(" beginTransaction start startError end rollback reset", $test->log, $name);
        $this->assertEquals(1, $result->errorCount());
    }


    /**
     * End hook exception
     */
    public function testEndHookException()
    {
        $test = $this->_makeTest($name = '_testOk');
        $test->endError = true;

        $test->run($result = new PHPUnit_Framework_TestResult);
        $this->assertEquals(" beginTransaction start _testOk end endError rollback reset", $test->log, $name);
        $this->assertEquals(1, $result->errorCount());
    }

}
