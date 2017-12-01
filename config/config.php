<?php
/**
 *  Configurate Application
 *  2017 Sergeo Zach
 */
defined('BASE_PATH') || define('BASE_PATH', getenv('BASE_PATH') ?: realpath(dirname(__FILE__) . '/../..'));
defined('APP_PATH') || define('APP_PATH', BASE_PATH . '/backend');


/**
 * Default
 */
define('USER_DEFAULT_LANG', 'en-us');
define('REGULAR_VARCHAR_NAME', '/^[0-9a-zA-Zа-яёА-ЯЁ _-]+$/u');
define('REGULAR_VARCHAR_NAME_USER', "/^[0-9a-zA-Zа-яёА-ЯЁ '-]+$/u");


$isHttps = !empty($_SERVER['HTTPS']) && 'off' !== strtolower($_SERVER['HTTPS']);

if($isHttps) {
    define('BASIC_DOMAIN', 'https://int.gamificationlab.ru/');
} else {
    define('BASIC_DOMAIN', 'http://int.gamificationlab.ru/');
}






return new \Phalcon\Config([
    'database' => [
        'adapter'     => 'Mysql',
        'host'        => 'localhost',
        'username'    => 'int',
        'password'    => 'qtu5RjPaHtbRHJT6',
        'dbname'      => 'int',
        'charset'     => 'utf8',
    ],
    'application' => [
        'appDir'         => APP_PATH . '/',
        'controllersDir' => APP_PATH . '/controllers/',
        'controllersDirCreate' => APP_PATH . '/controllers/create',
        'controllersDirUpdate' => APP_PATH . '/controllers/update',
        'controllersDirDelete' => APP_PATH . '/controllers/delete',
        'controllersDirView' => APP_PATH . '/controllers/view',
        'commonLib' => APP_PATH . '/common/library',
        'modelsDir'      => APP_PATH . '/models/',
        'migrationsDir'  => APP_PATH . '/migrations/',
        'viewsDir'       => APP_PATH . '/views/',
        'pluginsDir'     => APP_PATH . '/plugins/',
        'libraryDir'     => APP_PATH . '/library/',
        'cacheDir'       => BASE_PATH . '/cache/',
        'websocketPort'       => 8080,

        // This allows the baseUri to be understand project paths that are not in the root directory
        // of the webpspace.  This will break if the public/index.php entry point is moved or
        // possibly if the web server rewrite rules are changed. This can also be set to a static path.
        'baseUri'        => preg_replace('/frontend([\/\\\\])index.php$/', '', $_SERVER["PHP_SELF"]),
    ]
]);
