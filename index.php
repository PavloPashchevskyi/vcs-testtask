<?php

require_once 'vendor/autoload.php';

use Application\Config\Options;
use Application\Core\Application;

$defaultOptions = Options::get();

try {
    Application::start($defaultOptions['default_route']);
} catch (Exception $exception) {
    echo 'Exception #'.$exception->getCode().': '.$exception->getMessage()."<br/>Unable to start the Application!";
}
