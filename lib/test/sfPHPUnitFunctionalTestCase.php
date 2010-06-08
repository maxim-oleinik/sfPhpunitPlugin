<?php

/**
 * sfBasePhpunitFunctionalTestCase is the super class for all functional
 * tests using PHPUnit.
 *
 * @package    sfPhpunitPlugin
 * @author     Maxim Oleinik <maxim.oleinik@gmail.com>
 */
abstract class sfPHPUnitFunctionalTestCase extends myUnitTestCase
{
    /**
     * The sfTestFunctional instance
     *
     * @var sfTestFunctional
     */
    protected $browser;


    /**
     * Inject your own functional testers
     *
     * @see sfTestFunctionalBase::setTesters()
     *
     * @return array
     *          'request'  => 'sfTesterRequest',
     *          'response' => 'sfTesterResponse',
     *          'user'     => 'sfTesterUser',
     */
    protected function getFunctionalTesters()
    {
        return array();
    }


    /**
     * Custiom setup initialization
     *
     * @see parent::setUp
     */
    protected function _initialize()
    {
        sfConfig::clear();

        // Initialize SCRIPT_NAME for correct work $this->generateUrl()
        // when $_SERVER is empty before first request
        $_SERVER['SCRIPT_NAME'] = '/index.php';

        // Init context once for each app
        $this->getContext($this->getApplication());

        // Init test browser
        $this->browser = new sfTestFunctional(new sfPhpunitTestBrowser, new sfPHPUnitLimeAdapter($this), $this->getFunctionalTesters());
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
                    $this->_makeRequestErrorMessage($e->getDescription(), $e) . PHP_EOL,
                    $e->getComparisonFailure()
                );
            } else {
                return new $className(
                    $e->getDescription(),
                    $e->getComparisonFailure(),
                    $this->_makeRequestErrorMessage($e->getCustomMessage(), $e) . PHP_EOL
                );
            }

        } else if ($e instanceof PHPUnit_Framework_Error) {
            return new $className(
                $this->_makeRequestErrorMessage($e->getMessage(), $e),
                $e->getCode(),
                $e->getFile(),
                $e->getLine(),
                $e->getTrace()
            );
        }

        return $e;
    }


    /**
     * Make request error message with last request uri and params
     *
     * @param  string - User defined message
     * @return strung
     */
    private function _makeRequestErrorMessage($mess, Exception $e)
    {
        $result = $mess  . PHP_EOL . PHP_EOL
                . 'Request: ' . $this->browser->getLastRequestUri() . PHP_EOL
                . 'Request params: ' . PHP_EOL . $this->browser->getLastRequestParams();

        if ($_FILES) {
            $result .= PHP_EOL . PHP_EOL
                    .  'Submited FILES: '  . PHP_EOL
                    .  var_export($_FILES, true);
        }

        $result .= PHP_EOL . PHP_EOL
                .  'Trace: ' . PHP_EOL
                .  PHPUnit_Util_Filter::getFilteredStacktrace($e, false);

        return $result;
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
     * @param  string      $name     - Route name from routing.yml
     * @param  array|Model $params   - Routing params
     * @param  bool        $absolute - Make absolute url
     * @return string
     */
    protected function generateUrl($name, $params = array(), $absolute = false)
    {
        return $this->browser->getContext()->getRouting()->generate($name, $params, $absolute);
    }

}
