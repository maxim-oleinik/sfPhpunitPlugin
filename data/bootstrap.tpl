<?php
require_once(dirname(__FILE__).'/../../config/ProjectConfiguration.class.php');

// Core autoload
new ProjectConfiguration(DIR);

// Test lib
require_once(DIR . '/lib/test/myUnitTestCase.php');
require_once(DIR . '/lib/test/myFunctionalTestCase.php');
