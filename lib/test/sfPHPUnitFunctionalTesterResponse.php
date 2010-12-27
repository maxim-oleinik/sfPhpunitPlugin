<?php
/**
 * Extended tester for response
 *
 * @author Max <maxim.olenik@gmail.com>
 */

class sfPHPUnitFunctionalTesterResponse extends sfTesterResponse
{
    /**
     * Check redirect
     *
     * @param int    $statusCode
     * @param string $uri
     */
    public function checkRedirect($statusCode, $uri)
    {
        if (strpos($uri, 'http') !== 0) {
            $uri = 'http://localhost' . $uri;
        }

        return $this->begin()
            ->isStatusCode($statusCode)
            ->isHeader('Location', $uri)
        ->end();
    }

}
