<?php




define('SYSTEM_PATH', BASE_PATH . '/backend/vendor/system');
define('UPLOADS_PATH', BASE_PATH . '/public/uploads/usr');
define('UPLOADS_PATH_RELATIVE', 'uploads/usr');
define('INCLUDES_PATH', BASE_PATH . '/backend/vendor/includes');
define('LOCALISATION_PATH', BASE_PATH . '/backend/vendor/localisation');
define('FRONTEND_IMAGES_PATH', 'public/css/images');


class ImportTask extends \Phalcon\Cli\Task
{


    public function mainAction() {


        $lf = '/tmp/import.lock';

        if(file_exists($lf) && filemtime($lf) > time() - 300) return;

        file_put_contents('/tmp/import.lock', 1);

        include SYSTEM_PATH.'/System.php';

        $api = $this;

        $App = new AuthController();


        $Controller = new dataHandlerController();

        $Controller->startCron($App,$api);


        sleep(2);

        unlink($lf);



    }

}