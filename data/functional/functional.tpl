<?php
// calculate sf_root_dir by hand, sfConfig isn't available yet
// we assume that this file is located in test/phpunit/bootstrap
define('SFPHPUNIT_F_ROOT', $sf_root = realpath(dirname(__FILE__).'/../../..'));

$app = '{application}';

// We are including the standard functional bootstrap file here
// other developers may execute custom stuff there.
// This bootstrap file loads the project configuration the initializes
// the complete context, so we do not have to include our custom
// classes on our own.
include($sf_root.'/test/bootstrap/functional.php');

// include phpunit
require_once 'PHPUnit/Framework.php';