<?php

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 30.03.17
 * Time: 9:32
 */
class AuthController extends Phalcon\Mvc\Controller
{


    private $secret_token = 'cXbc6acge34dYXT6oxuWwQoiGkvO7vzynpExsTn1vJ6jorkCFvYvYG6HxrA8yX1J';

    public $lang = 'ru-ru';

    public $uid = null;

    public $userAgent = null;

    public $accessToken = null;

    public $userPermission = 'anonim';

    public $statusApprove = 0;

    public $userProfile = [];

    public $constructor = false;

    public $maxSizeImage = 1000;

    public $basicSizeImage = 200;

    public $response_view = true;

    public $connectId = null;

    public $activate = null;

    public $settings = null;

    public $config = null;

    public $cashe = [];

    public $integration_user = null;

    public $evotor_user = null;



    /**
     * Get hearder Authorization
     * */
    function getAuthorizationHeader(){

        $headers = null;

        if (isset($_SERVER['Authorization'])) {

            $headers = trim($_SERVER["Authorization"]);

        }
        else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI

            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);

        } elseif (function_exists('apache_request_headers')) {

            $requestHeaders = apache_request_headers();

            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));

            //print_r($requestHeaders);

            if (isset($requestHeaders['Authorization'])) {

                $headers = trim($requestHeaders['Authorization']);

            }

        }

        return $headers;

    }


    /**
     * get access token from header
     * */

    function getBearerToken() {

        $headers = $this->getAuthorizationHeader();

        // HEADER: Get the access token from the header

        if (!empty($headers)) {

            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {

                return $matches[1];

            }

        }

        return null;
    }





    /**
     * Basic Auth
     */
    public function UserAuth()
    {

        $auth = null;

        $this->userAgent = $_SERVER['HTTP_USER_AGENT'];


        $raw = JSON::get();

        if(isset($raw->access_token)) {

            $this->accessToken = $raw->access_token;

        }


        if(!$this->accessToken) {

            $get = $this->request->get();

            if(isset($get['access_token'])) {

                $this->accessToken = $get['access_token'];

            }
        }

        if($this->getBearerToken()) {

            $this->accessToken = $this->getBearerToken();

        }



        if($this->accessToken) {

            if($this->accessToken != $this->secret_token) {

                $auth = Auth::findFirstByAccess_token($this->accessToken);



            } else {

                $this->integration_user = true;
            }



        } else {


            if ($this->cookies->has("SID")) {

                $sid = $this->cookies->get("SID");
                $this->sessionId = $sid->getValue();

                //"session_id = :session_id: AND user_agent = :user_agent:",

                $auth = Auth::find(
                    [
                        "session_id = :session_id: AND user_agent = :user_agent:",
                        "bind" => [
                            "session_id" => $this->sessionId,
                            "user_agent" => $this->userAgent,
                        ],
                    ]
                );

            }

        }

        if (isset($auth->id)) {



            $user = Users::find(
                [
                    "id = :id: AND status_approve != 3",
                    "bind" => [
                        "id" => $auth->uid,
                    ],
                ]
            );

            if(count($user) > 0) {

                $this->uid = $auth->uid;



                $user = Users::findFirstById($auth->uid);

                $settings = JSON::decode('['.$user->settings.']');

                foreach ($settings as $value) {

                    $this->lang = $value->localisation;
                }

                if($user->company_id > 0) {

                    $this->settings = Gem_settings::get($user->company_id);

                }


                $this->userPermission = $user->user_level;

                $this->statusApprove = $user->status_approve;

                # Set User Profile

                $this->userProfile['id'] = $user->id;

                $this->userProfile['name'] = $user->name;

                $this->userProfile['full_name'] = $user->full_name;

                $this->userProfile['email'] = $user->email;

                $this->userProfile['photo'] = $user->photo;

                $this->userProfile['birthday'] = $user->birthday;

                $this->userProfile['phone'] = $user->phone;

                $this->userProfile['messanger'] = $user->messanger;

                $this->userProfile['gender'] = $user->gender;

                $this->userProfile['system_id'] = $user->system_id;

                $this->userProfile['dir_section'] = $user->dir_section;

                $this->userProfile['company_id'] = $user->company_id;

                $this->userProfile['rung'] = $user->rung;

                $this->userProfile['start_work'] = $user->start_work;

                $this->userProfile['balance'] = $user->balance;

                $this->userProfile['balance_free'] = $user->balance_free;

                $this->userProfile['rating'] = $user->rating;

                $this->userProfile['game_level'] = $user->game_level;

                $this->userProfile['dep_id'] = $user->dep_id;

                $this->userProfile['team_id'] = $user->team_id;

                $this->userProfile['api_id'] = $user->api_id;

                $this->userProfile['work_position'] = $user->work_position;

                $this->userProfile['text_status'] = $user->text_status;

                $this->userProfile['tasks'] = $user->tasks;



                $this->config = [];

                $com_id = Companies::getDefault($this);

                $conf = Configurate::find(
                    [
                        "status = :status: AND company_id = :company_id: AND permission LIKE :permission:",
                        "bind" => [
                            "status" => 1,
                            "company_id" => $com_id,
                            "permission" => "%".$this->userPermission."%",
                        ],
                    ]
                );

                if(count($conf) > 0) {

                    foreach($conf as $set) {

                        $this->config[$set->method] = [];

                        $this->config[$set->method]['component'] = $set->component;

                        if($set->settings != '') {

                            $this->config[$set->method]['settings'] = $set->settings;

                        }


                    }
                }




            } else {


                JSON::buildJsonContent(
                    [
                        0 => 'access denied',
                        1 => Errors::getCode('access denied')
                    ],
                    'access_error'

                );

            }







        } else {

            if(!$this->integration_user) {


                if($this->accessToken) {

                    JSON::buildJsonContent(
                        'access denied (invalid token)',
                        'error'

                    );
                }

                if($this->sessionId) {

                    $this->logout('auto');

                    JSON::buildJsonContent(
                        'access denied (session lost)',
                        'error'

                    );
                }

            }


        }



    }








    public function authByHash($hash) {


        if(strpos($hash,'.') !== false) {

            $hash_data = explode('.',$hash);

            $auth = Users::find(
                [
                    "hash = :hash:",
                    "bind" => [
                        "hash" => $hash_data[0],
                    ],
                ]
            );



            if(count($auth) > 0) {

                foreach ($auth as $val) {

                    $this->uid = $val->id;

                    $this->connectId = $hash_data[1];

                    $this->userPermission = $val->user_level;

                    $this->userProfile['company_id'] = $val->company_id;

                    return true;

                }

            }

        } else {



            $auth = Auth::findFirstByAccess_token($hash);

            $user = Users::findFirstById($auth->uid);

            $connect = Gem_api_connect::findFirstByCompany_id($user->company_id);

            $this->userProfile['company_id'] = $user->company_id;

            $this->uid = $auth->uid;

            $this->connectId = $connect->id;

            $this->userPermission = $user->user_level;

            return true;

        }




        return false;


    }







    public function accessPermission($use_case, $id = null) {

        $error_message = '';
        $error = true;
        $auth = false;



        $sql = "use_case = :use_case: AND level LIKE :level: AND status = :status:";

        $bind = [

            "use_case" => $use_case,
            "level" => "%".$this->userPermission."%",
            "status" => 1,

        ];


        $data = User_rules::find(
            [
                $sql,
                "bind" => $bind,
            ]
        );




        foreach ($data as $rules) {

            if(isset($rules->id)) {

                if($rules->auth == 1) {

                    if($this->uid){

                        $auth = true;

                    }
                } else {

                    $auth = true;

                }


                if($auth) {

                    if($id) {

                        switch ($use_case) {


                            case 'updateCompanyAction':


                                $error = $this->checkCompany($id);

                                break;



                            case 'getPlayerById':
                            case 'updatePlayerAction':
                            case 'deletePlayerAction':

                                $error = $this->checkIdByCompany($id,'Users');

                                break;



                        }

                    } else {

                        $error = false;
                    }
                }


            }
        }



        if($error === false) {
            return true;
        }
        else {JSON::buildJsonContent(
            'access denied (rules)'.$error_message,
            'error'
        );}

    }











}