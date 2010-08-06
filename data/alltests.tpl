<?php
require_once dirname(__FILE__).'/bootstrap/all.php';


/**
 * AllTests
 */
class AllTests extends PHPUnit_Framework_TestSuite
{
    /**
     * SetUp
     */
    public function setUp()
    {
        $dir = getcwd();
        chdir(sfConfig::get('sf_root_dir'));

        // Clear logs
        sfToolkit::clearDirectory(sfConfig::get('sf_log_dir'));

        // Remove cache
        sfToolkit::clearDirectory(sfConfig::get('sf_cache_dir'));

        // Rebuild DB
        $task = new sfPropelBuildTask(new sfEventDispatcher, new sfFormatter);
        $task->run($args = array(), $options = array(
            'application' => 'frontend',
            'env' => 'test',
            'db' => true,
            'no-confirmation' => true,
        ));
        // Rebuild config after task run
        ProjectConfiguration::getApplicationConfiguration('frontend', 'test', $debug = true);

        chdir($dir);
    }


    /**
     * TestSuite
     */
    public static function suite()
    {
        $suite = new AllTests('PHPUnit');

        $base  = dirname(__FILE__);
        $files = sfFinder::type('file')->name('*Test.php')->in(array(
            $base.'/unit',
            $base.'/functional',
        ));

        foreach ($files as $file) {
            $suite->addTestFile($file);
        }

        return $suite;
    }

}
