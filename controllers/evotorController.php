<?php

/**
 * Created by PhpStorm.
 * User: miller321x
 * Date: 21.11.17
 * Time: 14:49
 */
use Phalcon\Mvc\Controller;

class evotorController extends Controller
{



    private $raw = null;

    private $com_id = 0;

    public $token = null;


    public function appAction($App,$api,$type) {


        $this->raw = JSON::object($api->request->getJsonRawBody(true));

        $this->token = $App->accessToken;


        if($App->integration_user) {


            switch ($type) {


                case 'app':


                    $this->installApp($App,$api);


                    break;

                case 'token':

                    $this->createTokenApp($App,$api);


                    break;

                case 'register':

                    $this->createClientUser($App,$api);

                    break;


            }


        } else {


            if($App->uid) {


                switch ($type) {

                    case 'login':

                        $this->loginClientUser($App,$api);


                        break;


                    case 'importEmployers':

                        $this->updateEmployers($App,$api);

                        break;


                    case 'importStores':

                        $this->updateStores($App,$api);

                        break;


                    case 'statDataEmployers':

                        $this->statDataEmployers($App,$api);

                        break;

                    case 'statDataStores':

                        $this->statDataStores($App,$api);

                        break;


                    case 'profileEmployer':

                        $this->profileEmployer($App,$api);

                        break;
                }

            } else {

                JSON::buildJsonContent(
                    'access denied',
                    'error'

                );

            }




        }



    }


    public function statDataEmployers($App,$api) {


    }


    public function statDataStores($App,$api) {





    }


    public function profileEmployer($App,$api) {





    }



    private function createTokenApp($App,$api) {





    }

    private function installApp($App,$api) {


        if(isset($this->raw->data)) {

            $client_id = $this->raw->data->userId;


            $installed = Evotor_app_clients::findFirstByClient_id($client_id);

            if(!isset($installed->id)) {

                Evotor_app_clients::addNewInstallApp($this->raw, $api, $App);

            } else {

                JSON::buildJsonContent(
                    'client app installed',
                    'error'

                );

            }


        } else {

            JSON::buildJsonContent(
                'install data undefined',
                'error'

            );

        }


    }




    public function updateEmployers($App,$api) {






    }


    public function updateStores($App,$api) {





    }


    private function createClientUser($App,$api) {




    }






    public function loginClientUser($App,$api) {







    }


    public function importStores($api, $App, $data) {




    }

    public function importUsers($api, $App, $data) {




    }




    public function createConfigurate($api, $App) {




    }

    public function createCategories($api, $App) {




    }


    public function createConnect($api) {





    }


    public function createLinks($api, $App, $id_conn) {




    }


    public function createParams($api, $App, $id_conn) {





    }


    public function createPoints($api, $App) {




    }



}