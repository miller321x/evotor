<?php

define('BASE_PATH', dirname(__DIR__));
define('SYSTEM_PATH', BASE_PATH . '/backend/vendor/system');


include SYSTEM_PATH . '/system.php';

$token = '';

$res = System::curl('http://88.208.23.76/api/cron');

echo $res;