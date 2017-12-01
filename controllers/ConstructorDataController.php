<?php

/**
 * Created by PhpStorm.
 * User: miller321x
 * Date: 18.04.17
 * Time: 16:06
 */
use Phalcon\Mvc\Controller;

class ConstructorDataController extends Controller
{

    public $app = null;
    public $api = null;
    public $userLang = USER_DEFAULT_LANG;
    public $userId = null;
    public $userPerm = null;
    public $userStatus = null;
    public $userProfile = null;
    public $params = [];



    public function getData($App,$api)
    {

        // Set auth user info

        $App->constructor = true;
        $this->app = $App;
        $this->api = $api;
        $this->userLang = $App->lang;
        $this->userId = $App->uid;
        $this->userPerm = $App->userPermission;
        $this->userStatus = $App->statusApprove;
        $this->userProfile = $App->userProfile;

        $params = System::getRequest();

        $ui = isset($params->ui) ? explode(',', $params->ui) : null;
        $methods = isset($params->methods) ? $this->prepare(explode(',',$params->methods)) : null;
        $info = isset($params->info) ? explode(',',$params->info) : null;



        $constructor = [];

        if($App->uid) {

            if($App->accessPermission('getData')) {

                if($info) {
                    $constructor['info'] = $this->getInfo($info);
                }
                if($methods) {
                    $constructor['methods'] = $this->getMethods($methods);
                }

                if(count($constructor) == 0) {
                    $constructor['basic_info'] = $this->getBasicInfo();
                }

            } else {

                JSON::buildJsonContent(
                    'access denied',
                    'error'

                );
            }

        } else {

            if($ui) {

                $constructor['ui'] = $this->uiClasses($ui);

            } else {

                $App->accessPermission('getData');

            }

        }


        JSON::buildJsonContent(
            $constructor,
            'ok'

        );




    }

    public function prepare($methods) {

        $new_methods = [];

        for($i = 0; $i < count($methods); $i++) {

            if(strpos($methods[$i],'{') !== false) {

                $list = explode('{',$methods[$i]);

                $new_methods[$i] = $list[0];

                $list = str_ireplace('}','',$list[1]);

                $list = explode('+',$list);

                for($i2 = 0; $i2 < count($list); $i2++) {

                    $param = explode(':',$list[$i2]);

                    if(!isset($this->params[$new_methods[$i]])) {

                        $this->params[$new_methods[$i]] = [];

                    }

                    $this->params[$new_methods[$i]][$param[0]] = $param[1];

                }



            } else {
                $new_methods[$i] = $methods[$i];
            }
        }

        return $new_methods;
    }





    private function getInfo(array $info = []) {

        $result = [];

        for($i = 0; $i < count($info); $i++) {

            switch (trim($info[$i])) {

                case 'basic':

                    $result['basic'] = $this->getBasicInfo();

                    break;
            }

        }

        return $result;

    }

    private function getMethods(array $methods = []) {

        $result = [];


        for($i = 0; $i < count($methods); $i++) {

            switch (trim($methods[$i])) {

                case 'companies':

                    $Controller = new ViewCompanyController();

                    $result['companies'] = $Controller->getCompanies($this->app);

                    break;



                case 'players':

                    $Controller = new ViewUserController();


                    $this->params['players']['company_id'] = Companies::getDefault($this->app);

                    //$this->params['fields'] = 'id : name : full_name : photo : rating : game_level : department : team ';

                    $this->params['players']['fields'] = 'id : name : full_name : photo : balance : balance_free : user_role_name';

                    $this->params['players']['limit'] = 1000;

                    $this->params['players']['sort'] = 'coins';

                    $params = JSON::object($this->params['players']);

                    $result['players'] = $Controller->getUsers($this->api,$this->app, $params);




                    break;






                default:

                    $result[trim($methods[$i])] = 'method not found';
            }

        }

        return $result;

    }






    private function getBasicInfo() {

        $company = Companies::findFirstById($this->userProfile['company_id']);

        $result = [];
        $result['auth_user_id'] = $this->userId;
        $result['user_permissions'] = $this->userPerm;
        $result['user_lang'] = $this->userLang;
        $result['user_status_approve'] = $this->userStatus;
        $result['user_full_name'] = $this->userProfile['full_name'];
        $result['user_photo'] = $this->userProfile['photo'];
        $result['company_id'] = $this->userProfile['company_id'];
        $result['company_image'] = System::getImageUrl($company->company_image);

        return $result;

    }

}