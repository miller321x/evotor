<?php

$loader = new \Phalcon\Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */

$loader->registerDirs(
    [
        $config->application->controllersDir,
        $config->application->controllersDirCreate,
        $config->application->controllersDirDelete,
        $config->application->controllersDirUpdate,
        $config->application->controllersDirView,
        $config->application->modelsDir,
        $config->application->commonLib


    ]
)->register();




/**
 * Include System
 */


include '/home/gaming/backend/vendor/autoload.php';


include 'System.php';