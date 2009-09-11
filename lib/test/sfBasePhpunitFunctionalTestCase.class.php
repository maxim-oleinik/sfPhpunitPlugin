<?php
require_once dirname(__FILE__).'/../../../../config/ProjectConfiguration.class.php';

/**
 * sfBasePhpunitFunctionalTestCase is the super class for all functional
 * tests using PHPUnit.
 * The "getBrowser" method provides the current functional test/browser
 * instance of symfony and you can do anything with it you are used from
 * the normal lime based tests.
 *
 * @package    sfPhpunitPlugin
 * @subpackage lib
 * @author     Frank Stelzer <dev@frankstelzer.de>
 */
abstract class sfBasePhpunitFunctionalTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * The sfContext instance
     *
     * @var sfContext
     */
    private $context = null;

    /**
     * The sfTestFunctional instance
     *
     * @var sfTestFunctional
     */
    protected $browser;

    /**
     * Returns application name
     *
     * @return string
     */
    abstract protected function getApplication();


    /**
     * Returns environment name
     *
     * @return string
     */
    abstract protected function getEnvironment();

    /**
     * Returns if the test should be run in debug mode
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
     */
    protected function _start()
    {
    }

    /**
     * Dev hook for custom "tearDown" stuff
     *
     */
    protected function _end()
    {
    }

    /**
     * setUp method for PHPUnit
     *
     */
    protected function setUp()
    {
        // first we need the context and autoloading
        $this->initializeContext();

        // autoloading ready, continue
        $this->browser = new sfTestFunctional(new sfPhpunitTestBrowser, new sfPhpunitTest($this));

        // Initialize SCRIPT_NAME for correct work $this->generateUrl()
        // when $_SERVER is empty before first request
        $_SERVER['SCRIPT_NAME'] = '/index.php';

        $this->_start();
    }

    /**
     * Returns the sfTestFunctional instance
     *
     * @return sfTestFunctional
     */
    public function getBrowser()
    {
        return $this->browser;
    }


    /**
     * tearDown method for PHPUnit
     *
     */
    protected function tearDown()
    {
        $this->_end();
    }

    /**
     * Intializes the context for this test
     *
     */
    private function initializeContext()
    {
        // only initialize the context one time
        if(!$this->context)
        {
            $configuration = ProjectConfiguration::getApplicationConfiguration($this->getApplication(), $this->getEnvironment(), $this->isDebug());
            sfContext::createInstance($configuration);

            // remove all cache
            sfToolkit::clearDirectory(sfConfig::get('sf_app_cache_dir'));
        }
    }

    /*
     * Returns sfContext instance
     *
     * @return sfContext
     */
    protected function getContext()
    {
        if(!$this->context)
        {
            $this->context = sfContext::getInstance();
        }

        return $this->context;
    }


    /**
     * Generate URL from route name
     *
     * Example:
     *   $this->generateUrl('homepage');
     *      -> "/"
     *   $this->generateUrl('article_edit', $articleObject);
     *      -> "/article/1/edit"
     *   $this->generateUrl('custom_route', $arrRouteParams);
     *
     * @see sfPatternRouting::generate()
     *
     * @param  string      $name   - Route name from routing.yml
     * @param  array|Model $params - Routing params
     * @return string
     */
    protected function generateUrl($name, $params = array())
    {
        return $this->browser->getContext()->getRouting()->generate($name, $params);
    }


    /**
     * Run test
     *
     * Catch exception and decorate it with last request data
     */
    protected function runTest()
    {
        try {
            parent::runTest();
        } catch (Exception $e) {
            throw $this->_decorateExeption($e);
        }
    }


    /**
     * Decorate exception with last request data
     *
     * @param  Exception $e
     * @return Exception
     */
    private function _decorateExeption(Exception $e)
    {
        if (!$this->browser->getLastRequestUri()) {
            return $e;
        }

        $className = get_class($e);

        if ($e instanceof PHPUnit_Framework_ExpectationFailedException) {
            if (!$e->getCustomMessage()) {
                return new $className(
                    $this->_makeRequestErrorMessage($e->getDescription()) . PHP_EOL,
                    $e->getComparisonFailure()
                );
            } else {
                return new $className(
                    $e->getDescription(),
                    $e->getComparisonFailure(),
                    $this->_makeRequestErrorMessage($e->getCustomMessage()) . PHP_EOL
                );
            }

        } else if ($e instanceof PHPUnit_Framework_Error) {
            return new $className(
                $this->_makeRequestErrorMessage($e->getMessage()),
                $e->getCode(),
                $e->getFile(),
                $e->getLine(),
                $e->getTrace()
            );

        } else {
            return new $className(
                $this->_makeRequestErrorMessage($e->getMessage()),
                $e->getCode()
            );
        }
    }


    /**
     * Make request error message with last request uri and params
     *
     * @param  string - User defined message
     * @return strung
     */
    private function _makeRequestErrorMessage($mess)
    {
        $result = $mess  . PHP_EOL . PHP_EOL
                . 'Request: ' . $this->browser->getLastRequestUri() . PHP_EOL
                . 'Request params: ' . $this->browser->getLastRequestParams();

        if ($_FILES) {
            $result .= PHP_EOL
                    .  'Submited FILES: ' . var_export($_FILES, true);
        }

        return $result;
    }

}
