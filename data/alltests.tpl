<?php
require_once(dirname(__FILE__).'/bootstrap/all.php');


class AllTests extends PHPUnit_Framework_TestSuite
{
    /**
     * SetUp
     *
     * Run once before all tests (even if run in isolated processes)
     */
    public function setUp()
    {
        // Clear logs
        sfToolkit::clearDirectory(sfConfig::get('sf_log_dir'));

        // Rebuild DB
        $task = new sfDoctrineRebuildDbTask(new sfEventDispatcher, new sfFormatter);
        $task->run($args = array(), $options = array(
            '--env=test',
            '--no-confirmation',
        ));

        // Load test fixtures
        Doctrine::loadData(sfConfig::get('sf_test_dir').'/fixtures');
    }


    /**
     * TestSuite
     */
    public static function suite()
    {
        $suite = new AllTests('PHPUnit');

        $base  = dirname(__FILE__);
        $files = sfFinder::type('file')->name('*Test.php')->in(array(
            $base.'/functional',
            $base.'/unit',
        ));

        foreach ($files as $file) {
            $suite->addTestFile($file);
        }

        return $suite;
    }

}
