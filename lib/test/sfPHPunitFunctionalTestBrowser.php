<?php

class sfPHPunitFunctionalTestBrowser extends sfTestFunctional
{
    public function call($uri, $method = 'get', $parameters = array(), $changeStack = true)
    {
        parent::call($uri, $method, $parameters, $changeStack);

        $content = $this->getContext()->getResponse()->getContent();
        if ($pos = strpos($content, 'class="xss"')) {
            $start = ($pos - 150) > 0 ? $pos - 150 : 0;
            throw new Exception("Found XSS tocken: \n\n" . substr($content, $start, 300));
        }

        return $this;
    }
}
