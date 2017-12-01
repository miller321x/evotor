<?php


ini_set('error_reporting', E_ALL);
error_reporting(E_ALL);

if($_REQUEST['sleep']) sleep(1);

use Phalcon\Http\Response;
use Phalcon\Di\FactoryDefault;

$response = new Response();
$response->setHeader("Access-Control-Allow-Origin", "*");
$response->setHeader("Access-Control-Allow-Credentials", "true");
$response->setHeader("Access-Control-Allow-Methods", "GET,HEAD,OPTIONS,POST,PUT");
$response->setHeader("Access-Control-Allow-Headers", "Access-Control-Allow-Headers, Origin,Accept, X-Requested-With, Content-Type, Access-Control-Request-Method, Access-Control-Request-Headers");
$response->setContentType('application/json', 'UTF-8');


define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/backend');
define('SYSTEM_PATH', BASE_PATH . '/backend/vendor/system');
define('UPLOADS_PATH', BASE_PATH . '/public/uploads/usr');
define('UPLOADS_PATH_RELATIVE', 'uploads/usr');
define('INCLUDES_PATH', BASE_PATH . '/backend/vendor/includes');
define('LOCALISATION_PATH', BASE_PATH . '/backend/vendor/localisation');
define('FRONTEND_IMAGES_PATH', 'public/css/images');


try {

    /**
     * The FactoryDefault Dependency Injector automatically registers
     * the services that provide a full stack framework.
     */
    $di = new FactoryDefault();

    /**
     * Read services
     */
    include APP_PATH . '/config/services.php';

    /**
     * Get config service for use in inline setup below
     */
    $config = $di->getConfig();

    /**
     * Include Autoloader
     */
    include APP_PATH . '/config/loader.php';


    /**
     * Handle routes for API
     */
    include APP_PATH . '/config/router.php';




} catch (\Exception $e) {

    echo $e->getMessage() . '<br>';
    echo '<pre>' . $e->getTraceAsString() . '</pre>';

    JSON::buildJsonContent(
        'invalid request',
        'error'

    );
}

