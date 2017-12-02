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
     * Basic login
     */

    public function LoginAction($App,$api, $rules)
    {



        $raw = JSON::get();

        $error_exception = Auth::validFormLogin($App,$raw);

        if (count($error_exception) > 0) {

            JSON::buildJsonContent(
                $error_exception,
                'error'

            );

        } else {


            $login = System::strSpecialClear($raw->login);

            $pass = System::strSpecialClear($raw->pass);

            if(isset($raw->mode)) {

                $mode = System::strSpecialClear($raw->mode);

            } else {

                $mode = 'token';

            }



            $auth = Users::find(
                [
                    "email = :email: AND status_approve != 3",
                    "bind" => [
                        "email" => $login,
                    ],
                ]
            );

            if(count($auth) > 0) {


                $auth = Users::findFirstByEmail($login);


                if(isset($auth->id)) {

                    if($auth->status_approve != 3) {

                        if($auth->user_level == $rules) {


                            if (password_verify($pass, $auth->user_pass)) {


                                $auth_old = Auth::find(
                                    [
                                        "uid = :uid: AND user_agent = :user_agent:",
                                        "bind" => [
                                            "uid" => $auth->id,
                                            "user_agent" => $_SERVER['HTTP_USER_AGENT'],
                                        ],
                                    ]
                                );

                                if (isset($auth_old->id)) {

                                    $auth_old->delete();

                                }


                                Auth::createAuth($auth,$api,$mode,$this);

                            } else {
                                JSON::buildJsonContent(
                                    [
                                        0 => 'error login pass undefined',
                                        1 => Errors::getCode('error login pass undefined')
                                    ],
                                    'error'

                                );
                            }

                        } else {

                            JSON::buildJsonContent(
                                [
                                    0 => 'error user rules',
                                    1 => Errors::getCode('error user rules')
                                ],
                                'error'

                            );

                        }

                    } else {

                        JSON::buildJsonContent(
                            [
                                0 => 'error user rules',
                                1 => Errors::getCode('error user rules')
                            ],
                            'error'

                        );

                    }





                } else {

                    JSON::buildJsonContent(
                        [
                            0 => 'error login name undefined',
                            1 => Errors::getCode('error login name undefined')
                        ],
                        'error'

                    );
                }


            } else {

                JSON::buildJsonContent(
                    [
                        0 => 'error login name undefined',
                        1 => Errors::getCode('error login name undefined')
                    ],
                    'error'

                );

            }



        }


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


    public function getConfig() {

        $raw = JSON::get();

        if($this->accessPermission('getConfig')) {


            $config = [];

            $com_id = Companies::getDefault($this);



            $conf = Configurate::find(
                [
                    "company_id = :company_id: AND status = :status: AND permission LIKE :permission: AND component LIKE :component:",

                    "bind" => [

                        "company_id" => $com_id,

                        "status" => 1,

                        "permission" => "%".$this->userPermission."%",

                        "component" => "%".$raw->component."%",
                    ],

                    "order" => "position ASC",
                ]
            );

            if(count($conf) > 0) {

                $modules = [];

                $i = 0;

                $methods = '';

                $ui = null;

                foreach($conf as $set) {

                    if($set->method != 'ui') {

                        $modules[$i] = [];

                        $modules[$i]['method_name'] = $set->method;

                        $modules[$i]['position'] = $set->position;



                        if($methods != '') {

                            $methods .= ',';

                        }

                    }



                    $com = '';
                    if($set->method == 'categories') {

                        if($set->settings != '') {

                            $com = $set->settings;

                        }


                    }
                    if($set->method == 'departments_rating'
                        or $set->method == 'global_rating'
                        or $set->method == 'static_info'
                        or $set->method == 'user_profile'
                        or $set->method == 'top_players'
                        or $set->method == 'top_teams'
                        or $set->method == 'top_departments'
                        or $set->method == 'feed') {

                        $com = $set->settings;

                    }

                    if($set->method == 'calendar_by_month') {

                        $date = explode('-',System::toDay('date'));
                        $date = $date[2].'.'.$date[1].'.'.$date[0];
                        $com = '{date:'.$date.'}';

                    }



                    if($set->method == 'banners') {

                        if($set->settings != '') {

                            $com = $set->settings;

                        } else {

                            if(isset($raw->component)) {
                                $com = '{id:'.$set->id.'}';
                            }
                        }


                    }
                    if($set->method == 'media') {

                        $com = '{class:media}';


                    }
                    if($set->method == 'calendar') {

                        $date = System::toDay('date');
                        $date = explode('-',$date);
                        $com = '{date:31.'.$date[1].'.'.$date[0].'}';


                    }

                    if($set->method == 'coworker') {

                        if($set->settings != '') {

                            $com = $set->settings;

                        } else {
                            $com = '{class:coworker_menu}';
                        }


                    }
                    if($set->method == 'docs') {

                        $com = '{view:module}';

                    }

                    if($set->method == 'ui') {

                        $ui = $set->settings;

                    } else {

                        $methods .= $set->method.$com;

                        $i++;
                    }



                }



                $config['data_modules'] = $modules;

                $config['methods'] = $methods;

                if($ui) {

                    $config['ui'] = $ui;
                }


                JSON::buildJsonContent(

                    $config,

                    'ok'

                );

            } else {

                JSON::buildJsonContent(
                    'configurate not found',
                    'error'

                );

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




    /** Logout method
     * @param $type
     */

    public function logoutAction($type = null) {

        if(!$type) {

            if($this->accessToken) {

                $type = 'token';

            } else {

                $type = 'sid';

            }
        }

        if($type == 'token') {

            $auth = Auth::find(
                [
                    "access_token = :access_token: AND user_agent = :user_agent:",
                    "bind" => [
                        "access_token" => $this->accessToken,
                        "user_agent" => $this->userAgent,
                    ],
                ]
            );

            if (isset($auth->id)) {

                if ($auth->delete() === false) {

                    JSON::buildJsonContent(
                        'error_sql',
                        'error'

                    );
                } else {
                    JSON::buildJsonContent(
                        'success',
                        'ok'

                    );
                }

            } else {
                JSON::buildJsonContent(
                    'access denied',
                    'error'

                );
            }

        }

        if($type == 'sid') {

            $auth = Auth::find(
                [
                    "session_id = :session_id: AND user_agent = :user_agent:",
                    "bind" => [
                        "session_id" => $this->sessionId,
                        "user_agent" => $this->userAgent,
                    ],
                ]
            );

            if (isset($auth->id)) {

                if ($auth->delete() === false) {

                    JSON::buildJsonContent(
                        'error_sql',
                        'error'

                    );
                }

                $this->cookies->set(
                    "SID",
                    "",
                    time() + 15 * 86400
                );

            }


        }

        if($type == 'auto') {

            $this->cookies->set(
                "SID",
                "",
                time() + 15 * 86400
            );

        }


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

                            case 'updateTaskAction':
                            case 'deleteTaskAction':

                                $error = $this->checkIdByCompany($id,'Gem_tasks');

                                break;


                            case 'updateOrderAction':

                                $res = Orders::findFirstById($id);

                                if(isset($res->id)) {

                                    $error = false;

                                }

                                break;


                            case 'updateDepartmentAction':
                            case 'deleteDepartmentAction':

                                $error = $this->checkIdByCompany($id,'Departments');

                                break;

                            case 'reinvitePlayerAction':

                                $error = $this->checkIdByCompany($id,'Invites');

                                break;

                            case 'updateTeamAction':
                            case 'deleteTeamAction':

                                $error = $this->checkIdByCompany($id,'Teams');

                                break;

                            case 'updateParamAction':
                            case 'deleteParamAction':

                                $error = $this->checkIdByCompany($id,'Gem_params');

                                break;

                            case 'updateFormulaAction':
                            case 'deleteFormulaAction':

                                $error = $this->checkIdByCompany($id,'Gem_formules');

                                break;

                            case 'updateProductAction':
                            case 'deleteProductAction':

                                $error = $this->checkIdByCompany($id,'Products');

                                break;

                            case 'updatePointAction':
                            case 'deletePointAction':

                                $error = $this->checkIdByCompany($id,'Gem_points');

                                break;

                            case 'updateAchieveAction':
                            case 'deleteAchieveAction':

                                $error = $this->checkIdByCompany($id,'Gem_achieves');

                                break;


                            case 'updateConnectAction':
                            case 'deleteConnectAction':

                                $error = $this->checkIdByCompany($id);

                                break;

                            case 'updateUserAction':
                            case 'deleteUserAction':
                            case 'setUserNewPassAction':
                            case 'setUserSettingsAction':

                                if($this->uid != $id) {
                                    $error = false;
                                }

                                break;

                            case 'getUsersAction':
                            case 'getUsersInvitesAction':

                                $res = Companies::findFirstById($id);


                                if($this->userPermission == 'admin') {

                                    $error_message = ' (The company does not belong to the user)';

                                    if (isset($res->id)) {

                                        if ($res->id == $this->userProfile['company_id']) {

                                            $error = false;

                                        }
                                    }

                                }
                                if($this->userPermission == 'gamer') {

                                }



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


    public function checkCompany($id) {

        $sql = "owner_id = :owner_id: AND id = :id:";

        $bind = [
            "owner_id" => $this->uid,
            "id" => $id
        ];

        $res = Companies::find(
            [
                $sql,
                "bind" => $bind,

            ]
        );

        if (count($res) > 0) {
            return false;

        } else {
            return true;

        }
    }

    public function checkIdByCompany($id, $model = 'Gem_api_connect') {

        $company_id = Companies::getDefault($this);

        $res = [];

        $sql = "id = :id: AND company_id = :company_id:";

        $bind = [
            "company_id" => $company_id,
            "id" => $id
        ];

        switch ($model) {

            case 'Users':

                $res = Users::find(
                    [
                        $sql,
                        "bind" => $bind,

                    ]
                );

                break;


            case 'Invites':

                $res = Invites::find(
                    [
                        $sql,
                        "bind" => $bind,

                    ]
                );

                break;

            case 'Gem_tasks':

                $res = Gem_tasks::find(
                    [
                        $sql,
                        "bind" => $bind,

                    ]
                );

                break;

            case 'Gem_api_connect':

                $res = Gem_api_connect::find(
                    [
                        $sql,
                        "bind" => $bind,

                    ]
                );

                break;

            case 'Gem_points':

                $res = Gem_points::find(
                    [
                        $sql,
                        "bind" => $bind,

                    ]
                );

                break;

            case 'Gem_achieves':

                $res = Gem_achieves::find(
                    [
                        $sql,
                        "bind" => $bind,

                    ]
                );

                break;

            case 'Products':

                $res = Products::find(
                    [
                        $sql,
                        "bind" => $bind,

                    ]
                );

                break;

            case 'Gem_formules':

                $res = Gem_formules::find(
                    [
                        $sql,
                        "bind" => $bind,

                    ]
                );

                break;

            case 'Gem_params':

                $res = Gem_params::find(
                    [
                        $sql,
                        "bind" => $bind,

                    ]
                );

                break;

            case 'Departments':

                $res = Departments::find(
                    [
                        $sql,
                        "bind" => $bind,

                    ]
                );

                break;

            case 'Teams':



                $res = Teams::find(
                    [
                        $sql,
                        "bind" => $bind,

                    ]
                );

                break;

        }




        if (count($res) > 0) {

            return false;

        } else {

            return true;

        }
    }





    public function setCashe($model,$mode,$data = null) {

        if(!isset($this->cashe[$model])) {

            $this->cashe[$model] = [];

        }

        if($mode == 'add') {

            $this->cashe[$model] = $data;

        } else {

            switch ($model) {

                case 'Users':

                    if($mode == 'all') {

                        $sql = "company_id = :company_id:";

                        $bind = [

                            "company_id" => Companies::getDefault($this)

                        ];

                        $this->cashe[$model] = Users::find(
                            [
                                $sql,
                                "bind" => $bind,

                            ]
                        );


                    }

                    if($mode == 'ids') {

                        $this->cashe[$model] = Users::getUsersByIds($data);

                    }

                    break;


                case 'Gem_params':




                    if($mode == 'all') {

                        $sql = "company_id = :company_id:";

                        $bind = [

                            "company_id" => Companies::getDefault($this)

                        ];

                        $this->cashe[$model] = Gem_params::find(
                            [
                                $sql,
                                "bind" => $bind,

                            ]
                        );





                    }



                    break;


                case 'Gem_formules':

                    if($mode == 'all') {

                        $sql = "company_id = :company_id:";

                        $bind = [

                            "company_id" => Companies::getDefault($this)

                        ];

                        $this->cashe[$model] = Gem_formules::find(
                            [
                                $sql,
                                "bind" => $bind,

                            ]
                        );


                    }



                    break;


                case 'Gem_points':

                    if($mode == 'all') {

                        $sql = "company_id = :company_id:";

                        $bind = [

                            "company_id" => Companies::getDefault($this)

                        ];

                        $this->cashe[$model] = Gem_points::find(
                            [
                                $sql,
                                "bind" => $bind,

                            ]
                        );


                    }

                    break;



                case 'Gem_params_statistic':

                    if($mode == 'all') {

                        $sql = "company_id = :company_id:";

                        $bind = [

                            "company_id" => Companies::getDefault($this)

                        ];

                        $this->cashe[$model] = Gem_params_statistic::find(
                            [
                                $sql,
                                "bind" => $bind,

                            ]
                        );


                    }

                    break;




            }

        }

    }


    public function getCashe($model,$bind = null,$mode = null) {

        $multi = [];

        if(isset($this->cashe[$model])) {

            if(count($this->cashe[$model]) > 0) {

                if($bind) {

                    foreach ($this->cashe[$model] as $obj) {

                        if(!is_array($bind)) {

                            if($obj->id == $bind) {

                                if($mode == 'multi') {

                                    $multi[count($multi)] = $obj;

                                } else {

                                    return $obj;

                                }


                            }

                        } else {


                            $check = 0;

                            $need = 0;

                            foreach ($bind as $key => $value) {

                                if($obj->{$key} == $value) {

                                    $check++;

                                }

                                $need++;

                            }

                            if($check == $need) {

                                if($mode == 'multi') {

                                    $multi[count($multi)] = $obj;

                                } else {

                                    return $obj;

                                }

                            }

                        }

                    }

                    return $multi;


                } else {

                    return $this->cashe[$model];

                }


            } else {

                return null;

            }

        } else {

            return null;

        }

    }


}