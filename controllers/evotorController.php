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


        if(isset($this->raw->userId)) {


            $user = Evotor_users::findFirstByEvotor_uid($this->raw->userId);

            if(isset($user->id)) {

                $token = Evotor_tokens::findFirstByUid($user->uid);

                if(isset($token->id)) {

                    if($token->token != $this->raw->token) {

                        $token->token = $this->raw->token;

                        $token->save();

                    } else {

                        $token = $token->token;

                    }


                } else {

                    $token = Evotor_tokens::addNewTokenApp($this->raw, $api,$user->uid);
                }


                JSON::buildJsonContent(
                    $token,
                    'ok'

                );


            } else {

                JSON::buildJsonContent(
                    'client user undefined',
                    'error'

                );

            }


        } else {

            JSON::buildJsonContent(
                'client id is required',
                'error'

            );
        }


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



        if($App->uid) {


            $App->constructor = 1;

            $App->evotor_user = null;

            $this->com_id = $App->userProfile['company_id'];


            if(count($this->raw) > 0) {

                $this->importUsers($api, $App, $this->raw);


            }


        } else {

            JSON::buildJsonContent(
                'access denied',
                'error'

            );

        }


    }


    public function updateStores($App,$api) {


        if($App->uid) {

            $this->com_id = $App->userProfile['company_id'];

            if(count($this->raw) > 0) {

                $this->importStores($api, $App, $this->raw);

            }


        } else {

            JSON::buildJsonContent(
                'access denied',
                'error'

            );

        }




    }


    private function createClientUser($App,$api) {


        if(isset($this->raw->userId)) {

            $client_id = $this->raw->userId;

            $App->evotor_user = $client_id;

            $user = Evotor_users::findFirstByEvotor_uid($client_id);

            if(!isset($user->id)) {


                $App->constructor = 1;


                # create new user admin



                $data = [

                    "company_id" => 0,

                    "name" => $this->raw->name,

                    "full_name" => $this->raw->name_second,

                    "third_name" => "",

                    "email" => $this->raw->email,

                    "start_work" => "0000-00-00",
                ];

                $opt = JSON::object($data);

                $App->activate = 1;

                $uid = Users::addNewUser($opt, $api, $App, 'admin');





                # create company

                $data = [

                    "owner_id" => $uid,

                    "company_name" => "",

                    "company_image" => "",

                    "global_reting" => 0,

                    "email" => $this->raw->email,

                    "admin_panel" => "http://app.gamificationlab.ru"

                ];

                $opt = JSON::object($data);

                $this->com_id = Companies::addNewCompany($opt, $api, $App);


                $user = Users::findFirstById($uid);

                $user->company_id = $this->com_id;

                $user->save();



                # create configurate

                $this->createConfigurate($api, $App);



                # create categories

                $this->createCategories($api, $App);



                # create evator client

                $opt = [];

                $opt['userId'] = $this->raw->userId;

                $opt['uid'] = $uid;

                $opt['com_id'] = $this->com_id;

                $opt['type'] = 'client';

                $opt = JSON::object($opt);

                Evotor_users::addNewUserApp($opt, $api);



                # import users

                if(isset($this->raw->employees)) {

                    if(count($this->raw->employees) > 0) {

                        $App->evotor_user = null;

                        $this->importUsers($api, $App, $this->raw->employees);

                    }

                }




                # create connect

                $id_conn = $this->createConnect($api);



                # create params

                $this->createParams($api, $App, $id_conn);


                # create points

                $this->createPoints($api, $App);



                # create achieves

                Gem_achieves::exportBasicAchieves($api,$this->com_id);




                # create game products

                Products::exportBasicProducts($api,$this->com_id);



                # create auth key

                $user = Users::findFirstById($uid);

                Auth::createAuth($user,$api,'token',$App);



            } else {

                JSON::buildJsonContent(
                    'client app registred yet',
                    'error'

                );

            }


        } else {

            JSON::buildJsonContent(
                'registration data undefined',
                'error'

            );

        }


    }






    public function loginClientUser($App,$api) {



        $App->evotor_user = $this->raw->userId;

        $App->loginAction($App,$api,'admin');




    }


    public function importStores($api, $App, $data) {


        $App->response_view = false;

        $this->com_id = $App->userProfile['company_id'];


        foreach($data as $storeData) {



            # create store link

            $store = Evotor_stores::findFirstByEvotor_store($storeData->uuid);

            if(!isset($store->id)) {


                # create dep

                $opt = [];

                $opt['dep_name'] = $storeData->name;

                $opt['dep_image'] = "";

                $opt['company_id'] = $this->com_id;

                $opt = JSON::object($opt);

                $dep_id = Departments::addNewDepartment($opt,$api,$App);




                # create link between dep and store

                $opt = [];

                $opt['evotor_store'] = $storeData->uuid;

                $opt['dep_id'] = $dep_id;

                $opt['company_id'] = $this->com_id;



                $opt = JSON::object($opt);

                Evotor_stores::addNewStoreApp($opt, $api);

            } else {

                $store = Departments::findFirstById($store->dep_id);

                $store->dep_name = $storeData->name;

                $store->save();


            }

        }


    }

    public function importUsers($api, $App, $data) {


        $App->response_view = false;

        $this->com_id = $App->userProfile['company_id'];


        foreach($data as $userData) {

            $dep_id = 0;

            $user = Evotor_users::findFirstByEvotor_uid($userData->uuid);

            if(!isset($user->id)) {

                # create store link

                if(count($userData->stores) > 0) {


                    for($i = 0; $i < count($userData->stores); $i++) {



                        $store = Evotor_stores::findFirstByEvotor_store($userData->stores[$i]);

                        if(!isset($store->id)) {


                            # create dep

                            $opt = [];

                            $opt['dep_name'] = $userData->stores[$i];

                            $opt['dep_image'] = "";

                            $opt['company_id'] = $this->com_id;

                            $opt = JSON::object($opt);

                            $dep_id = Departments::addNewDepartment($opt,$api,$App);




                            # create link between dep and store

                            $opt = [];

                            $opt['evotor_store'] = $userData->stores[$i];

                            $opt['dep_id'] = $dep_id;

                            $opt['company_id'] = $this->com_id;



                            $opt = JSON::object($opt);

                            Evotor_stores::addNewStoreApp($opt, $api);

                        } else {

                            $dep_id = $store->dep_id;
                        }

                    }



                }



                $opt = [

                    "company_id" => $this->com_id,

                    "name" => $userData->name,

                    "full_name" => $userData->lastName,

                    "third_name" => "",

                    "email" => "",

                    "dep_id" => $dep_id,

                    "start_work" => "0000-00-00",

                ];

                $opt = JSON::object($opt);




                $uid = Users::addNewUser($opt, $api, $App, 'gamer');



                # create evator player

                if($userData->role != 'ADMIN') {

                    $type = 'player';

                } else {

                    $type = $userData->role;

                }

                $opt = [];

                $opt['userId'] = $userData->uuid;

                $opt['uid'] = $uid;

                $opt['com_id'] = $this->com_id;

                $opt['type'] = $type;

                $opt['user_role'] = $userData->role;

                $opt = JSON::object($opt);

                Evotor_users::addNewUserApp($opt, $api);

            }

        }


        $id_conn = Gem_api_connect::findFirstByCompany_id($this->com_id);

        # create links users connects adn evator

        $this->createLinks($api, $App, $id_conn->id);


    }




    public function createConfigurate($api, $App) {


        $options = [];

        # create in config member menu module

        $options[0] = [];

        $options[0]['component'] = 'member_dashboard';

        $options[0]['method'] = 'categories';

        $options[0]['company'] = $this->com_id;


        # create in config admin menu module

        $options[1] = [];

        $options[1]['component'] = 'admin_dashboard';

        $options[1]['method'] = 'categories';

        $options[1]['company'] = $this->com_id;


        # create in config member ui

        $options[2] = [];

        $options[2]['component'] = 'member_dashboard';

        $options[2]['method'] = 'ui';

        $options[2]['company'] = $this->com_id;


        # create in config admin ui

        $options[3] = [];

        $options[3]['component'] = 'admin_dashboard';

        $options[3]['method'] = 'ui';

        $options[3]['company'] = $this->com_id;


        $options = JSON::object($options);


        ConfigurateController::addConfigurate($api,$options);

    }

    public function createCategories($api, $App) {


        $categories = [];

        # create in menu rating shops

        $categories[0] = [];

        $categories[0]['class'] = 'main_menu';

        $categories[0]['url'] = '/departments_ratings';

        $categories[0]['company'] = $this->com_id;



        # create in menu rating users

        $categories[1] = [];

        $categories[1]['class'] = 'main_menu';

        $categories[1]['url'] = '/rating';

        $categories[1]['company'] = $this->com_id;


        # create in menu shop

        $categories[2] = [];

        $categories[2]['class'] = 'main_menu';

        $categories[2]['url'] = '/shop';

        $categories[2]['company'] = $this->com_id;


        # create in menu achievements

        $categories[3] = [];

        $categories[3]['class'] = 'main_menu';

        $categories[3]['url'] = '/achievements';

        $categories[3]['company'] = $this->com_id;




        # create in admin menu main page

        $categories[4] = [];

        $categories[4]['class'] = 'admin';

        $categories[4]['url'] = '/';

        $categories[4]['company'] = $this->com_id;


        # create in admin menu company page

        $categories[5] = [];

        $categories[5]['class'] = 'admin';

        $categories[5]['url'] = '/company';

        $categories[5]['company'] = $this->com_id;


        # create in admin menu players page

        $categories[6] = [];

        $categories[6]['class'] = 'admin';

        $categories[6]['url'] = '/players';

        $categories[6]['company'] = $this->com_id;


        # create in admin menu shop page

        $categories[7] = [];

        $categories[7]['class'] = 'admin';

        $categories[7]['url'] = '/shop';

        $categories[7]['company'] = $this->com_id;


        # create in admin menu achieves page

        $categories[8] = [];

        $categories[8]['class'] = 'admin';

        $categories[8]['url'] = '/achieves';

        $categories[8]['company'] = $this->com_id;


        $categories = JSON::object($categories);


        ConfigurateController::addBasicCategories($App, $api,$categories);

    }


    public function createConnect($api) {


        $options = [];

        $options['com_id'] = $this->com_id;

        $options['type'] = 2;

        $options['name'] = "Evotor Connect";

        $options['user_id'] = "employeeId";

        $options = JSON::object($options);

        return Gem_api_connect::addNewConnectAuto($options,$api);


    }




    public function createLinks($api, $App, $id_conn) {




        $sql = "user_role != :user_role_client: AND user_role != :user_role_admin: AND company_id = :company_id:";

        $bind = [];

        $bind['user_role_client'] = 'client';

        $bind['user_role_admin'] = 'ADMIN';

        $bind['company_id'] = $this->com_id;


        $users = Evotor_users::find(
            [
                $sql,

                "bind" => $bind,

            ]
        );




        if(count($users) > 0) {



            foreach($users as $user) {


                $options = [];

                $options['uid'] = $user->uid;

                $options['crm_user_id'] = $user->evotor_uid;

                $options['connect_id'] = $id_conn;

                $options = JSON::object($options);


                Gem_api_user_id::addNewUserId($options,$api,$App);

            }





        }


    }


    public function createParams($api, $App, $id_conn) {





    }


    public function createPoints($api, $App) {




    }



}