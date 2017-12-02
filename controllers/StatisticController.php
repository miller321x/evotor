<?php

/**
 * Created by PhpStorm.
 * User: miller321x
 * Date: 18.05.17
 * Time: 10:28
 */
use Phalcon\Mvc\Controller;

class StatisticController extends Controller
{

    public $paramStatistic = null;

    public $new = false;

    public $offset = 0;

    public $limit = 10;

    public $last_update = 0;

    public $param_type = null;

    public $update_type = null;

    public $format = null;

    public $time_live = null;

    public $live_status = true;

    public $model = null;

    public $data = null;

    public $sum = 0;

    public $App = null;



    public static function getStatOnePlayerByParams($App,$uid,$params) {


        $params = explode(':',$params);

        $data = [];


        for($i = 0; $i < count($params); $i++) {

            $param_name = trim($params[$i]);


            $sql = "company_id = :company_id: AND param_name = :param_name: AND uid = :uid:";


            $bind = [

                "company_id" => $App->userProfile['company_id'],

                "param_name" => $param_name,

                "uid" => $uid,

            ];



            $paramData = Gem_params_statistic::find(
                [
                    $sql,

                    "bind" => $bind,

                    "offset" => 0,

                    "limit" => 1,

                ]
            );


            if(count($paramData) > 0) {

                foreach($paramData as $param) {

                    $data[$param_name] = $param->param_value;

                }

            }

        }



        return $data;

    }


    public static function getStatPlayersByParams($App,$params) {


        $params = explode(':',$params);

        $data = [];

        $users = [];

        $users_evotor = [];

        for($i = 0; $i < count($params); $i++) {

            $param_name = trim($params[$i]);

            $data[$param_name] = [];


            $sql = "company_id = :company_id: AND param_name = :param_name:";


            $bind = [

                "company_id" => $App->userProfile['company_id'],

                "param_name" => $param_name,

            ];

            # get param data

            $paramData = Gem_params::find(
                [
                    $sql,

                    "bind" => $bind,

                    "offset" => 0,

                    "limit" => 1,

                ]
            );

            if(count($paramData) > 0) {

                foreach($paramData as $param) {


                    $list = Gem_params_statistic::find(
                        [
                            $sql,

                            "bind" => $bind,

                            "order" => "param_value DESC",

                            "offset" => 0,

                            "limit" => 30,

                        ]
                    );


                    $i2 = 0;

                    $max = 0;

                    $c = 0;

                    if(count($list) > 0) {

                        foreach($list as $val) {

                            if($i2 == 0) {

                                $max = $val->param_value;

                                $c = 100 / $max;
                            }

                            $data[$param_name][$i2] = [];

                            $data[$param_name][$i2]['value'] = $val->param_value;

                            $data[$param_name][$i2]['percent'] = ($c * $val->param_value) / 100;

                            $data[$param_name][$i2]['format'] = $param->format;





                            if(!isset($users[$val->uid])) {

                                $users[$val->uid] = Users::findFirstById($val->uid);

                                $users_evotor[$val->uid] = Evotor_users::findFirstByUid($val->uid);

                            }

                            $user = $users[$val->uid];

                            $user_evotor = $users_evotor[$val->uid];

                            $data[$param_name][$i2]['user_id'] = $val->uid;

                            $data[$param_name][$i2]['user_evotor_id'] = $user_evotor->evotor_uid;

                            $data[$param_name][$i2]['user_name'] = $user->name.' '.$user->full_name;

                            $data[$param_name][$i2]['user_photo'] = System::getImageUrl($user->photo);


                            $i2++;


                        }

                    }


                }

            }

        }



        return $data;





    }


    public static function getStatDepByParams($App,$params) {


        $params = explode(':',$params);

        $data = [];

        $users = [];

        for($i = 0; $i < count($params); $i++) {

            $param_name = trim($params[$i]);

            $data[$param_name] = [];


            $dep = [];

            $dep_index = [];

            $sql = "company_id = :company_id: AND param_name = :param_name:";


            $bind = [

                "company_id" => $App->userProfile['company_id'],

                "param_name" => $param_name,

            ];

            # get param data

            $paramData = Gem_params::find(
                [
                    $sql,

                    "bind" => $bind,

                    "offset" => 0,

                    "limit" => 1,

                ]
            );

            if(count($paramData) > 0) {

                foreach($paramData as $param) {


                    $list = Gem_params_statistic::find(
                        [
                            $sql,

                            "bind" => $bind,

                            "order" => "param_value DESC",

                            "offset" => 0,

                            "limit" => 30,

                        ]
                    );


                    $i2 = 0;



                    if(count($list) > 0) {

                        foreach($list as $val) {


                            if(!isset($users[$val->uid])) {

                                $users[$val->uid] = Users::findFirstById($val->uid);

                            }

                            $user = $users[$val->uid];

                            if($user->dep_id != 0) {

                                if(!isset($dep[$user->dep_id])) {

                                    $depData = Departments::findFirstById($user->dep_id);

                                    $dep[$user->dep_id] = [];

                                    $dep[$user->dep_id]['value'] = $val->param_value;

                                    $dep[$user->dep_id]['format'] = $param->format;

                                    $dep[$user->dep_id]['name'] = $depData->dep_name;

                                    $dep[$user->dep_id]['image'] = System::getImageUrl($depData->dep_image);


                                    $dep_index[$user->dep_id] = $val->param_value;




                                } else {

                                    $dep[$user->dep_id]['value'] = $dep[$user->dep_id]['value'] + $val->param_value;

                                    $dep_index[$user->dep_id] = $dep_index[$user->dep_id] + $val->param_value;

                                }

                            }


                            $i2++;


                        }

                    }


                }

                # get percent


                arsort ($dep_index);


                $i2 = 0;

                $max = 0;

                $c = 0;


                foreach ($dep_index as $key => $value) {

                    if($i2 == 0) {

                        $max = $value;

                        $c = 100 / $max;
                    }

                    $data[$param_name][$i2] = [];

                    $data[$param_name][$i2]['value'] = $value;

                    $data[$param_name][$i2]['percent'] = ($c * $value) / 100;

                    $data[$param_name][$i2]['format'] = $dep[$key]['format'];

                    $data[$param_name][$i2]['name'] = $dep[$key]['name'];

                    $data[$param_name][$i2]['image'] = $dep[$key]['image'];



                    $i2++;

                }


            }

        }



        return $data;

    }




    public function setStatisticAction($App,$api,$data) {



        $this->paramStatistic = $this->getParamStatistic($data,$App,$api);

        $this->getConfigParam($data,$App);

        if(!$this->new) {

            if(isset($this->paramStatistic['id'])) {

                $time = self::checkTime($this->paramStatistic['date_update'],$this->time_live);

                if($time) {

                    $param = Gem_params_statistic::findFirstById($this->paramStatistic['id']);


                    if(isset($param->id)) {

                        if($this->update_type == 1) {



                            $n = ($data->param_value - $param->param_start_value);

                            if($n > 0) {

                                $param->param_value = $param->param_value + $n;

                                $param->param_start_value = $param->param_value;

                            }

                        } else if($this->update_type == 3) {

                            $param->param_value = $param->param_value + $data->param_value;


                        } else if($this->update_type == 4) {



                            $param->param_value = ($param->param_value + 1);

                        } else if($this->update_type == 5) {

                            $param->param_value = 1;

                        }

                        else {

                            $param->param_value = $data->param_value;

                        }

                        $param->save();

                    }


                } else {

                    $this->live_status = false;

                }

            }

        }

    }








}