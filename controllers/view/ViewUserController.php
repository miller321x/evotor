<?php

/**
 * Created by PhpStorm.
 * User: miller321x
 * Date: 18.04.17
 * Time: 13:38
 */


use Phalcon\Mvc\Controller;


class ViewUserController extends Controller
{
    public $model = 'Users';
    public $data = null;
    public $limit = 15;
    public $fields = 'id : email : status_approve : user_phone : name : full_name : third_name: photo : gender : rating';
    public $App = null;
    public $api = null;




    public function getPlayerInfo($App,$api,$params = null) {


        $raw = JSON::get();

        $locked = [];

        $shared = [];


        if($App->accessPermission('getPlayerInfo')) {

            $gameSettings = self::gameSettings($App);


            if(isset($params->locked)) {

                $p = explode('.',$params->locked);

                for($i = 0; $i < count($p); $i++) {

                    $locked[$p[$i]] = true;

                }


            }

            if(isset($params->shared)) {

                $s = explode('.',$params->shared);

                for($i = 0; $i < count($s); $i++) {

                    $shared[$s[$i]] = true;

                }


            }


            $company = Companies::findFirstById(Companies::getDefault($App));

            $Stat = new StatisticController();

            if(isset($raw->id)) {

                $user = Users::findFirstById($raw->id);

                if(isset($user->id)) {




                    $playerData = [


                        "user_id" => $raw->id,

                        "company_id" => $company->id,

                        "photo" => System::getImageUrl($user->photo),

                        "name" => $user->name . ' ' .$user->full_name,

                        "department" => self::getPlayerDepartment($App,$user->dep_id),

                        "achieves" => Gem_user_achieves::get($raw->id),

                        "company_image" => System::getImageUrl($company->company_image),

                        "coins_history" => $this->getCoinsUser($App,$raw->id),

                        "work_position" => $user->work_position

                    ];


                    //if(isset($shared['text_status'])) {

                        $playerData["text_status"] = $user->text_status;

                    //}


                    if(isset($shared['stat_info'])) {

                        $playerData["stat_info"] = $params->stat_info;

                    }

                    if(!isset($locked['rung'])) {

                        $playerData["rung"] = self::getPlayerRung($App,$user);

                    }



                    JSON::buildJsonContent(
                        $playerData,
                        'ok'

                    );


                } else {

                    JSON::buildJsonContent(
                        'user not found',
                        'error'

                    );
                }


            } else {



                $App->constructor = true;

                $playerData = [

                    "user_id" => $App->uid,

                    "company_id" => $company->id,

                    "photo" => System::getImageUrl($App->userProfile['photo']),

                    "notifications" => $Stat->getNotifications($App,null,$params),

                    "company_image" => System::getImageUrl($company->company_image),

                    "coins_history" => $this->getCoinsUser($App,$App->uid),

                    "work_position" => $App->userProfile['work_position']


                ];


                if(isset($shared['text_status'])) {

                    $playerData["text_status"] = $App->userProfile['text_status'];

                }


                if(!isset($locked['rung'])) {

                    $playerData["rung"] = self::getPlayerRung($App);

                }

                if(isset($shared['stat_info'])) {

                    $playerData["stat_info"] = $params->stat_info;

                }


                if(!isset($locked['all_rungs'])) {

                    $playerData["all_rungs"] = Gem_user_rungs::getRungList($App,$api);

                }

            }




            return $playerData;


        }





    }


    public static function getStartWork($date) {

        $date = explode(' ',$date);

        $arr = [

            "date" => $date[0],
            "time" => $date[1],

        ];

        return $arr;

    }








    public function getUsers($api,$App,$params = null)
    {


        if(!$params) {
            $params = System::getRequest();
        }

        if(isset($params->fields)) {
            $this->fields = $params->fields;
        }


        $search = isset($params->search) ? $params->search : null;
        $ids = isset($params->ids) ? str_ireplace('_', ',', $params->ids) : null;
        $company_id = isset($params->company_id) ? $params->company_id : null;
        $team = isset($params->team) ? System::intLength($params->team,11) : null;
        $dep = isset($params->dep) ? System::intLength($params->dep,11) : null;

        if(isset($params->order)) { if($params->order == 1) {$order = 'DESC';} else {$order = 'ASC';} } else { $order = 'DESC';}

        $sort = 'rating';

        if($params->sort) {
            if($params->sort == 'rating') {
                $sort = 'rating';
            }
            if($params->sort == 'coins') {
                $sort = 'balance';
            }
        }

        $limit = isset($params->limit) ? $params->limit : $this->limit;



        $page = isset($params->page) ? $params->page : 1;
        $offset = ($page - 1) * $limit;


        if($search) {


            $params = [];

            $params['limit'] = $limit;

            $params['offset'] = ($page - 1) * $params['limit'];

            $params['order'] = $order;

            return Users::getSearch($App,$api,$search,$params);


        } else {

            if($company_id) {

                if($App->accessPermission('getUsersAction',$company_id)) {


                    $sql = "status_approve != :status_approve: AND user_level = :user_level: AND company_id = :company_id:";

                    $bind = [
                        "status_approve" => 3,
                        "user_level" => "gamer",
                        "company_id" => $company_id,
                    ];


                    if ($search) {


                        $sql .= " AND name LIKE :value:";

                        $bind['value'] = "%".$search."%";


                    }

                    if ($team) {


                        $sql .= " AND team_id = :team_id:";

                        $bind['team_id'] = $team;


                    }

                    if ($dep) {


                        $sql .= " AND dep_id = :dep_id:";

                        $bind['dep_id'] = $dep;


                    }

                    if ($ids) {


                        $sql .= "AND id IN (:ids:)";

                        $bind['ids'] = $ids;


                    }


                    $this->data = Users::find(
                        [
                            $sql,
                            "bind" => $bind,
                            "order" => $sort . " " . $order,
                            "offset" => $offset,
                            "limit" => $limit,
                        ]
                    );


                    $count = Users::count(
                        [
                            $sql,
                            "bind" => $bind,
                            "order" => $sort . " " . $order,

                        ]
                    );


                    $count = $count - ($offset + $limit);

                    if($count < 0) {
                        $count = 0;
                    }



                    # response

                    if (count($this->data) > 0) {


                        if($App->constructor) {

                            $res = [];

                            $res['length'] = $count;

                            $res['data'] = JSON::buildJsonContentConstructor(
                                $this->fields,
                                $this
                            );

                            return $res;

                        } else {
                            JSON::buildJsonContent(
                                $this->fields,
                                'list',
                                $this
                            );
                        }


                    } else {

                        if($App->constructor) {

                            return '';

                        } else {

                            JSON::buildJsonContent(
                                ''

                            );

                        }

                    }

                }

            } else {

                if($App->constructor) {

                    return 'company_id is required';

                } else {
                    JSON::buildJsonContent(
                        'company_id is required'

                    );
                }

            }

        }








    }











}