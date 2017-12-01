<?php

/**
 * Created by PhpStorm.
 * User: miller321x
 * Date: 15.05.17
 * Time: 15:34
 */
use Phalcon\Mvc\Controller;




class dataHandlerController extends Controller
{

    public $method = 'get';

    public $lastParam = [];

    public $n = 0;

    public $param_n = 0;

    public $users = [];

    public $Statistic = null;

    public $special = [];

    public $params = [];

    public $apiData = [];

    public $crm_user_id = null;

    public $convert_users = [];





    public function startCron($App,$api) {

        $sql = "status = :status:";

        $bind = [
            "status" => 1,
        ];


        $data = Companies::find(
            [
                $sql,
                "bind" => $bind,

            ]
        );


        if (count($data) > 0) {

            foreach ($data as $val) {

                $App->uid = $val->owner_id;

                $user = Users::findFirstById($App->uid);

                $settings = JSON::decode('['.$user->settings.']');

                foreach ($settings as $value) {

                    $App->lang = $value->localisation;
                }

                $App->userPermission = $user->user_level;

                $App->statusApprove = $user->status_approve;

                $this->getNewData($App,$api);

            }

        }

    }


    public function pushNewData($App,$api) {

        $raw = JSON::get();

        $this->method = 'push';

        echo 'test '.$raw->revenue;


        if($App->authByHash($raw->key)) {


            $App->setCashe('Gem_params','all');

            $App->setCashe('Gem_formules','all');

            $App->setCashe('Gem_points','all');

            $App->setCashe('Gem_params_statistic','all');


            $App->settings = Gem_settings::findFirstByOwner_id($App->uid);

            $this->Statistic = new StatisticController();

            $this->dataHandler($App,$api,$raw);

            //$this->addRating($api,$App);

            //$users = $this->getUsers($App);

            //$this->checkAchieves($api,$App,$users);

            //$this->checkTasks($api,$App,$users);

            //$this->setLevel($App,$api,$users);

            //$this->setDate($App);


            JSON::buildJsonContent(
                'done',
                'ok'

            );


        } else {
            JSON::buildJsonContent(
                'access denied',
                'error'

            );
        }

    }


    public function getNewData($App,$api) {


        if($App->uid) {


            $App->setCashe('Gem_params','all');

            $App->setCashe('Gem_formules','all');

            $App->setCashe('Gem_points','all');

            $App->setCashe('Gem_params_statistic','all');



            $com_id = Companies::getDefault($App);

            $sql = "company_id = :company_id: AND connect_type = :connect_type:";

            $bind = [
                "company_id" => $com_id,
                "connect_type" => 2,
            ];


            $connects = Gem_api_connect::find(
                [
                    $sql,
                    "bind" => $bind,

                ]
            );

            if(count($connects) > 0) {

                $App->settings = Gem_settings::findFirstByOwner_id($App->uid);

                $this->Statistic = new StatisticController();

                foreach ($connects as $val) {

                    $App->connectId = $val->id;

                    $data = System::curl($val->api_url);

                    $data = json_decode($data);

                    $this->dataHandler($App,$api,$data);

                }


                $App->setCashe('Gem_params_statistic','all');

                $this->addRating($api,$App);

                $users = $this->getUsers($App);

                $this->checkAchieves($api,$App,$users);

                $this->checkTasks($api,$App,$users);

                $this->setLevel($App,$api,$users);

                $this->setDate($App);


            } else {

                JSON::buildJsonContent(
                    'connects not found',
                    'error'

                );

            }


        } else {

            JSON::buildJsonContent(
                'access denied (user not found)',
                'error'

            );
        }


    }


    public function setDate($App) {


        $settings = $App->settings;

        if(isset($settings->id)) {






                    $settings->last_update = System::toDay();

                    $settings->save();




        }


    }



    public function dataHandler($App, $api, $raw) {



        $this->apiData = [];

        $this->apiData['users'] = self::identifyPlayers($App, $raw);

        $this->apiData['params'] = $this->getConnectParams($App);

        $this->apiData['convertData'] = $this->recursDataConverter($raw, $this->apiData['params']);


        $this->apiData['formulas'] = self::getConnectFormulas($App);

        $this->apiData['setData'] = self::calculateData($this->apiData, $this->Statistic);



        $this->setLog($raw, $App, $api);

        $this->setData($this->apiData, $App, $api, $this->Statistic);




    }



    public function getUsers($App) {

        return $App->getCashe('Users');

    }




    public function identifyPlayers($App, $data) {

        $res = Gem_api_connect::findFirstById($App->connectId);



        if (isset($res->id)) {

            $params = [];

            $params[0] = $res->crm_user_id;
            $this->crm_user_id = $res->crm_user_id;


            $convert = [];
            $users = $this->recursDataConverter($data,$params,$convert,'users');





            $users_id = [];

            $c = 0;

            for($i = 0; $i < count($users); $i++) {




                $sql = "connect_id = :connect_id: AND crm_user_id = :crm_user_id:";

                $bind = [
                    "connect_id" => $App->connectId,
                    "crm_user_id" => $users[$i][$res->crm_user_id],
                ];



                $user = Gem_api_user_id::find(
                    [
                        $sql,
                        "bind" => $bind,

                    ]
                );


                if(count($user) > 0) {


                    foreach ($user as $val) {




                            $users_id[$c] = $val->uid;
                            $users_crm_id[$c] = $val->crm_user_id;
                            $c++;





                    }



                }


            }


            if(count($users_id) > 0) {


                $App->setCashe('Users','ids',$users_id);

            }



            $this->apiData['uid'] = $users_crm_id;

            return $users_id;


        } else {

            JSON::buildJsonContent(
                'connect undefined',
                'error'

            );
        }


    }


    public function setLog($data,$App,$api) {

        $res = [];

        var_dump($this->apiData);

        $res['request'] = self::responseViewData($this->apiData);

        $res['response'] = self::responseViewData($data);

        $res = json_decode(json_encode($res));




        Api_log::addNewApiLog($res,$api,$App);

    }


    public static function calculateData($apiData, $Statistic) {


            for($i = 0; $i < count($apiData['users']); $i++) {

                $calculate_data = [];

                $data_params = [];

                $c = 0;

                foreach ($apiData['convertData'][$i] as $key => $value) {

                    $calculate_data[$key] = $value;

                    $data_params[$c]['param_name'] = $key;

                    $data_params[$c]['param_value'] = $value;

                    $c++;

                }

                // автоматическое создание параметра с единицей для уникальных параметров

                for($i2 = 0; $i2 < count($apiData['params']); $i2++) {

                    if(!isset($calculate_data[$apiData['params'][$i2]])) {

                        $calculate_data[$apiData['params'][$i2]] = 1;

                        $data_params[$c]['param_name'] = $apiData['params'][$i2];

                        $data_params[$c]['param_value'] = Gem_params_statistic::getParam($apiData['params'][$i2],$apiData['users'][$i]) + 1;

                        $c++;

                    }

                }



                if($apiData['formulas'] > 0) {

                    for($i2 = 0; $i2 < count($apiData['formulas']); $i2++) {

                        $key_name = '';

                        foreach ($apiData['formulas'][$i2] as $key => $value) {

                            if($key == 'param_name') {

                                $key_name = $value;

                            }
                            if($key == 'formula') {

                                $res = null;


                                $res = $Statistic->doFormula($value,$data_params);


                                if(gettype($res) !== 'boolean') {

                                    if(is_nan($res)) {
                                        $res = 0;
                                    }

                                    $calculate_data[$key_name] = $res;

                                    $data_params[$c]['param_name'] = $key_name;

                                    $data_params[$c]['param_value'] = $res;

                                    $c++;

                                } else {

                                    if($res) {

                                        $res = 'true';

                                    } else {

                                        $res = 'false';

                                    }

                                    $calculate_data[$key_name] = $res;
                                }

                            }


                        }

                    }

                }


                $apiData['convertData'][$i] = $calculate_data;


            }



        return $apiData['convertData'];

    }


    public function setData($apiData,$App,$api, $Statistic) {


        $api->db->begin();

        for($i = 0; $i < count($apiData['users']); $i++) {

            if(!in_array($apiData['users'][$i], $this->users)) {

                $this->users[$apiData['users'][$i]] = 1;

            }

            foreach ($apiData['setData'][$i] as $key => $value) {

                $data = [];

                $data['uid'] = $apiData['users'][$i];

                $data['param_name'] = $key;

                $data['param_value'] = $value;

                $data = json_decode(json_encode($data));

                Gem_params_log::addNewParamData($data, $api, $App);

                $Statistic->setStatisticAction($App,$api,$data);


            }

        }


        $api->db->commit();

    }


    public function addRating($api,$App) {


        $data = Gem_points::getPointsFormulas($api,$App);

        if(count($data) > 0) {

            $api->db->begin();

            foreach ($this->users as $key => $value) {

                $data_params = $this->Statistic->getParamsByUser($App,$key);


                foreach ($data as $row) {

                    if($row->special_func > 0) {

                        $points =  SpecialFunctions::doFunction($App,$this->special[$row->special_func],$data_params);

                    } else {


                        $points = $this->Statistic->doFormula($row->formula,$data_params);

                    }




                    if($points) {

                        $this->Statistic->setPointsLog($row, $points, $key, $api);

                        $this->Statistic->accruePoints($row, $key, $api, $App);

                    }





                }


            }

            $api->db->commit();

        }


    }

    public function checkAchieves($api,$App,$users) {

        if(count($users) > 0) {

            foreach ($users as $val) {

                $achieves = Gem_achieves::getAchievesByDep($val->dep_id,$App);

                foreach ($achieves as $row) {

                    $achieve = Gem_user_achieves::checkAchieve($row->id,$val->id);

                    if(!$achieve) {

                        if($this->Statistic->dateStart($row, $App, 'userProfile')) {

                            if($row->formula_id > 0) {

                                $achieve = $App->getCashe('Gem_formules',$row->formula_id);

                                $data_params = $this->Statistic->getParamsByUser($App,$val->id);

                                if($this->Statistic->doFormula($achieve->formula,$data_params)) {

                                    $this->Statistic->achieveDone($val, $row, $App, $api);

                                }

                            }
                            else if($row->special_func > 0) {

                                if(SpecialFunctions::doFunction($App,$this->special[$row->special_func],$this->params)) {

                                    $this->Statistic->achieveDone($val, $row, $App, $api);

                                }

                            }

                            else {

                                $this->Statistic->achieveDone($val, $row, $App, $api);
                            }
                        }

                    }

                }


            }


        }

    }



    public function checkTasks($api,$App,$users) {

        if(count($users) > 0) {

            foreach ($users as $val) {


                $data_params = $this->Statistic->getParamsByUser($App,$val->id);

                $tasks = Gem_tasks::getTasksByDep($val->dep_id,$App);

                foreach ($tasks as $row) {



                    $task = Gem_user_tasks::checkTask($row->id,$val->id);

                    if(!$task) {



                        //if($this->Statistic->dateStart($row, $App, 'self')) {

                            if($row->formula_id > 0) {

                                $task = $App->getCashe('Gem_formules',$row->formula_id);



                                if(isset($task->formula)) {

                                    if($task->formula != '') {

                                        if($this->Statistic->doFormula($task->formula,$data_params)) {

                                            $this->Statistic->taskDone($val, $row, $App, $api);

                                        }
                                    }

                                }



                            }

                            if($row->special_func > 0) {

                                if(SpecialFunctions::doFunction($App,$this->special[$row->special_func],$this->params)) {

                                    $this->Statistic->taskDone($val, $row, $App, $api);

                                }

                            }

                        //}

                    }

                }


            }



        }

    }


    public function setLevel($App,$api,$users) {

        if(count($users) > 0) {

            foreach ($users as $val) {

                $this->Statistic->getLevelPlayer($val->id,$App,$api);

            }
        }
    }




    public static function responseViewData($apiData) {

        return json_encode($apiData);

    }

    public function getConnectParams($App) {


       // $sql = "api_connect_id = :api_connect_id: AND param_type = :param_type:";

        $bind = [
            "api_connect_id" => $App->connectId,
            "param_type" => 1,
        ];


        /*
        $data = Gem_params::find(
            [
                $sql,
                "bind" => $bind,

            ]
        );
*/

        $data = $App->getCashe('Gem_params',$bind,'multi');

        $params = [];

        /*
        $i = 0;

        foreach ($data as $val) {

            $params[$i] = $val->param_name;

            $i++;
        }
        */

        $params[0] = $this->crm_user_id;

        for($i = 1; $i <= count($data); $i++) {

            $params[$i] = $data[($i - 1)]->param_name;

        }



        return $params;

    }

    public static function getConnectFormulas($App) {


        $sql = "api_connect_id = :api_connect_id: AND param_type = :param_type:";

        $bind = [
            "api_connect_id" => $App->connectId,
            "param_type" => 2,
        ];


        $data = Gem_params::find(
            [
                $sql,
                "bind" => $bind,
                "order" => "id ASC",

            ]
        );

        $formulas = [];

        $i = 0;

        foreach ($data as $val) {

            $formula = $App->getCashe('Gem_formules',$val->formula_id);

            $formulas[$i] = [];

            $formulas[$i]['param_name'] = $val->param_name;

            $formulas[$i]['formula'] = $formula->formula;

            $i++;
        }

        return $formulas;

    }

    public function recursDataConverter($data, $params, $convert = [], $type = null) {


        # if 0 recurse level

        if(count($convert) == 0) {

            if($type != 'users') {
                $this->n = -1;
            } else {
                $this->n = 0;
            }

            $this->param_n = 0;

            $this->convert_users = [];


        }

        # get key value


        foreach ($data as $key => $value) {


            # if value is object or array -> go to down level

            if (is_object($value)) {

                $convert = self::recursDataConverter($value,$params,$convert,$type);

            }

            else if (is_array($value)) {


                for($i2 = 0; $i2 < count($value); $i2++) {

                    $convert = self::recursDataConverter($value[$i2],$params,$convert,$type);

                }

            } else  {

                # if value is string or numeric
                # get all params created in game for this connect
                # and check


                    for($i = 0; $i < count($params); $i++) {

                        if($key == $params[$i]) {


                            # if param key it's crm user ID
                            # create new line params


                            if($key == $this->crm_user_id) {



                                # if we want to get only params user

                                if($type != 'users') {


                                    # we need take only users who playing now

                                    for($u = 0; $u < count($this->apiData['users']); $u++) {

                                        if($value == $this->apiData['uid'][$u]) {


                                            if(!isset($this->convert_users[$value])) {

                                                # check if all users done

                                                if($this->param_n < count($this->apiData['users'])) {

                                                    $this->n++;

                                                    $convert[$this->n] = [];

                                                    $convert[$this->n][$params[$i]] = $value;

                                                    $this->convert_users[$value] = 1;


                                                }

                                                $this->param_n++;

                                            }






                                        }

                                    }

                                } else {

                                    # if we want to get only id user

                                    $convert[$this->n] = [];

                                    $convert[$this->n][$params[$i]] = $value;

                                    $this->n++;

                                }




                            } else {

                                # if we want to get only params user
                                # set param to line next after ID param

                                if($type != 'users') {


                                    if(!isset($convert[$this->n][$params[$i]])) {

                                        if(isset($convert[$this->n][$this->crm_user_id])) {

                                            $convert[$this->n][$params[$i]] = $value;
                                        }

                                    }



                                } else {

                                    # if we want to get only id user

                                    $convert[$this->n][$params[$i]] = $value;
                                }

                            }

                        }

                    }

                }

        }


        return $convert;

    }








}