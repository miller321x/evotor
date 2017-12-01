<?php

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 30.03.17
 * Time: 15:44
 */
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Query;



class Users extends Model
{
    public $id;

    public $status_approve;

    public $user_level;

    public $system_id;

    public $dir_section;

    public $hash;

    public $oauth_id;

    public $api_id;

    public $oauth_name;

    public $name;

    public $email;

    public $user_phone;

    public $user_pass;

    public $full_name;

    public $third_name;

    public $photo;

    public $gender;

    public $date_reg;

    public $date_visit;

    public $rating;

    public $game_level;

    public $balance;

    public $balance_free;

    public $auto_balance;

    public $user_role;

    public $level_role;

    public $company_id;

    public $dep_id;

    public $team_id;

    public $settings;

    public $start_work;

    public $rung;

    public $status_display_rating;

    public $birthday;

    public $phone;

    public $messanger;

    public $work_position;

    public $text_status;

    public $tasks;

    public $tasks_static;

    public $reminder_day;

    public $reminder_month;



    /**
     * Init default user settings
     *
     */

    public static function defaultUserSettings($App) {

        $settings = [];
        $settings['localisation'] = $App->lang;
        $settings['email_notifications'] = 1;
        $settings['black_list_users'] = '';
        $settings['page_lock'] = 0;
        $settings['mode_anonim'] = 0;

        return json_encode($settings, JSON_UNESCAPED_UNICODE);

    }


    /**
     * VALIDATION USERS
     *
     */

    # user settings validation

    public static function validSettings($App, $exception, $value)
    {


            if(!isset($value->localisation)) {
                $exception[0] = 'localisation is required';
                $exception[1] = Errors::getCode('localisation is required');
            }

            else if(!isset($value->email_notifications)) {
                $exception[0] = 'email_notifications is required';
                $exception[1] = Errors::getCode('email_notifications is required');
            }

            else if(!isset($value->black_list_users)) {
                $exception[0] = 'black_list_users is required';
                $exception[1] = Errors::getCode('black_list_users is required');
            }

            else if(!isset($value->page_lock)) {
                $exception[0] = 'page_lock is required';
                $exception[1] = Errors::getCode('page_lock is required');
            }
            else {

                if(!isset($value->mode_anonim)) {
                    $exception[0] = 'mode_anonim is required';
                    $exception[1] = Errors::getCode('mode_anonim is required');
                }
            }




        return $exception;
    }


    # User pass validation

    public static function validNewPass($App, $exception, $value, $users) {



           if(!isset($value->new_pass)) {
                $exception[0] = 'new_pass is required';
                $exception[1] = Errors::getCode('new_pass is required');
            }

            else if(isset($value->new_pass)) {



                if (mb_strlen($value->new_pass, 'UTF-8') < 8) {

                    $exception[0] = Ui::lang($App,'ERROR_USER_PASS_MIN', 8);
                    $exception[1] = Errors::getCode('ERROR_USER_PASS_MIN');

                }
                else {

                    if (mb_strlen($value->new_pass, 'UTF-8') > 30) {

                        $exception[0] = Ui::lang($App,'ERROR_USER_PASS_MAX', 30);
                        $exception[1] = Errors::getCode('ERROR_USER_PASS_MAX');

                    }

                }

                if (!preg_match('/^[a-zA-Z0-9]+$/u', $value->new_pass))
                {

                    $exception[0] = Ui::lang($App,'ERROR_USER_PASS_INVALID');
                    $exception[1] = Errors::getCode('ERROR_USER_PASS_INVALID');

                }


            }




        return $exception;

    }


    # new user validation

    public static function validNewUser($App, $value)
    {

        $exception = [];



            if(!isset($value->name)) {
                $exception[0] = 'name is required';
                $exception[1] = Errors::getCode('user name is required');
            }

            else if(!isset($value->email)) {
                $exception[0] = 'email is required';
                $exception[1] = Errors::getCode('user email is required');
            }

            else if(!isset($value->pass)) {
                $exception[0] = 'pass is required';
                $exception[1] = Errors::getCode('user pass is required');
            }
            else {

                if(isset($value->name) and isset($value->email) and isset($value->pass)) {

                    $exception = self::validName($App,$exception, $value->name);

                    $exception = self::validEmail($App,$exception, $value->email, 1);

                    //$exception = self::validPass($App, $exception, $value->pass);
                }

            }






        return $exception;

    }

    # update player validation

    public static function validUpdatePlayerParam($App, $value) {

        $exception = [];

        /*
        if(!isset($value->rating)) {
            $exception[0] = 'rating is required';
            $exception[1] = Errors::getCode('rating is required');
        }
        else if($value->rating < 0) {
            $exception[0] = 'negative int';
            $exception[1] = Errors::getCode('negative int');
        }
        */

        if($value->balance < 0) {
            $exception[0] = 'negative int';
            $exception[1] = Errors::getCode('negative int');
        }

        if(!isset($value->first_name) or $value->first_name == '') {
            $exception[0] = 'first_name is required';
            $exception[1] = Errors::getCode('first_name is required');
        }
        if(!isset($value->last_name) or $value->last_name == '') {
            $exception[0] = 'last_name is required';
            $exception[1] = Errors::getCode('last_name is required');
        }

        if(isset($value->first_name)) {
            $exception = self::validName($App,$exception, $value->first_name);
        }
        if(isset($value->last_name)) {
            $exception = self::validName($App,$exception, $value->last_name);
        }





        return $exception;

    }

    # new invite player validation

    public static function validNewInvite($App, $value) {

        $exception = [];

        if(!isset($value->name) or $value->name == '') {
            $exception[0] = 'name is required';
            $exception[1] = Errors::getCode('user name is required');
        }

        else if(!isset($value->email) or $value->email == '') {
            $exception[0] = 'email is required';
            $exception[1] = Errors::getCode('user email is required');
        }

        else if(!isset($value->year) or $value->year == '') {
            $exception[0] = 'year is required';
            $exception[1] = Errors::getCode('user year is required');
        }
        else {

            $exception = self::validEmail($App,$exception, $value->email, 2);

            if(count($exception) == 0) {

                $exception = self::validName($App,$exception, $value->name);

            }

        }

        return $exception;
    }




    # update user validation

    public static function validUpdateUser($App, $value)
    {

        $exception = [];


                $exception = self::validName($App, $exception, $value->name);


                $exception = self::validEmail($App, $exception, $value->email);


                $exception = self::validGender($App, $exception, $value->gender);


                $exception = self::validNameFull($App, $exception, $value->full_name);


                $exception = self::validNameFull($App, $exception, $value->third_name);


                $exception = self::validPhone($App, $exception, $value->user_phone);



        return $exception;

    }


    /** Properties validation */

    # Valid name

    public static function validName($App, $error_exception,$name,$uniq = false)
    {


        if(!$name){

            $error_exception[0] = 'name is required';
            $error_exception[1] = Errors::getCode('user name is required');

        } else {

            $name = System::strSpecialClear($name);

            if (mb_strlen($name,'UTF-8') < 2) {

                $error_exception[0] = Ui::lang($App,'ERROR_USER_NAME_MIN', 2);
                $error_exception[1] = Errors::getCode('ERROR_USER_NAME_MIN');

            }
            else if (mb_strlen(trim($name),'UTF-8') > 100) {

                $error_exception[0] = Ui::lang($App,'ERROR_USER_NAME_MAX', 100);
                $error_exception[1] = Errors::getCode('ERROR_USER_NAME_MAX');

            }

            else if (!preg_match(REGULAR_VARCHAR_NAME_USER, $name))
            {
                $error_exception[0] = Ui::lang($App,'ERROR_USER_NAME_INVALID_MULTI');
                $error_exception[1] = Errors::getCode('ERROR_USER_NAME_INVALID');
            }

            else {

                if ($uniq) {

                    $res = self::findFirstByName($name);


                    if (isset($res->id)) {

                        $error_exception[0] = Ui::lang($App, 'ERROR_USER_ISSET');
                        $error_exception[1] = Errors::getCode('ERROR_USER_ISSET');

                    }



                }
            }

        }




        return $error_exception;

    }






    # Valid email

    public static function validEmail($App,$error_exception,$email,$uniq = 0)
    {



        if(!isset($email)){

            $error_exception[0] = 'email is required';
            $error_exception[1] = Errors::getCode('user email is required');

        } else {

            $email = System::strSpecialClear($email);

            if (mb_strlen($email,'UTF-8') > 50) {

                $error_exception[0] = Ui::lang($App,'ERROR_USER_EMAIL_MAX', 50);
                $error_exception[1] = Errors::getCode('ERROR_USER_EMAIL_MAX');

            }

            else if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            {

                $error_exception[0] = Ui::lang($App,'ERROR_USER_EMAIL_INVALID');
                $error_exception[1] = Errors::getCode('ERROR_USER_EMAIL_INVALID');

            }

            else {

                if ($uniq) {

                    if($uniq == 1) {

                        $res = self::findFirstByEmail($email);

                        if (isset($res->id)) {

                            $error_exception[0] = Ui::lang($App,'ERROR_EMAIL_ISSET');
                            $error_exception[1] = Errors::getCode('ERROR_EMAIL_ISSET');

                        }

                    } else {



                            $res = self::findFirstByEmail($email);

                            if (isset($res->id)) {

                                $error_exception[0] = Ui::lang($App,'ERROR_EMAIL_ISSET');
                                $error_exception[1] = Errors::getCode('ERROR_EMAIL_ISSET');

                            }





                    }





                }
            }


        }

        return $error_exception;

    }



    public static function validTextStatus($App,$error_exception,$status)
    {


        if(isset($status)){


            if (mb_strlen($status, 'UTF-8') > 200) {

                $error_exception[0] = Ui::lang($App,'ERROR_USER_STATUS_MAX', 200);
                $error_exception[1] = Errors::getCode('ERROR_USER_STATUS_MAX');

            }


        }

        return $error_exception;
    }

    # Valid full name

    public static function validNameFull($App,$error_exception,$name)
    {


        if(!isset($name)){

            $error_exception[0] = 'full_name is required';
            $error_exception[1] = Errors::getCode('full_name is required');

        } else {

            $name = System::strSpecialClear($name);



            if (mb_strlen($name,'UTF-8') < 2) {

                $error_exception[0] = 'second name is short (2)';
                $error_exception[1] = Errors::getCode('ERROR_USER_NAME_SECOND_MIN');

            }

            if (mb_strlen($name, 'UTF-8') > 100) {

                $error_exception[0] = Ui::lang($App,'ERROR_USER_NAME_MAX', 100);
                $error_exception[1] = Errors::getCode('ERROR_USER_NAME_MAX');

            } else {
                if (!preg_match('/^[a-zA-Zа-яёА-ЯЁ ]+$/u', $name))
                {

                    $error_exception[0] = Ui::lang($App,'ERROR_USER_FULL_NAME_INVALID');
                    $error_exception[1] = Errors::getCode('ERROR_USER_FULL_NAME_INVALID');

                }
            }


        }
        return $error_exception;
    }

    # Valid Gender

    public static function validGender($App,$error_exception,$gender)
    {


        if(!isset($gender)){

            $error_exception[0] = 'gender is required';
            $error_exception[1] = Errors::getCode('gender is required');

        }


        return $error_exception;

    }


    # Valid messenger

    public static function validMessenger($App,$error_exception,$messenger) {

        if($messenger != '') {


            if (mb_strlen($messenger, 'UTF-8') > 30) {

                $error_exception[0] = Ui::lang($App, 'ERROR_USER_MESSENGER_MAX', 30);
                $error_exception[1] = Errors::getCode('ERROR_USER_MESSENGER_MAX');

            } else {

                if (!preg_match('/^[a-zA-Z0-9._-]+$/u', $messenger)) {

                    $error_exception[0] = Ui::lang($App, 'ERROR_USER_MESSENGER_INVALID');
                    $error_exception[1] = Errors::getCode('ERROR_USER_MESSENGER_INVALID');

                }

            }

        }



        return $error_exception;
    }


    # Valid phone

    public static function validPhone($App,$error_exception,$phone)
    {

        if(!isset($phone) or $phone == ''){

            $error_exception[0] = 'phone is required';
            $error_exception[1] = Errors::getCode('phone is required');

        } else {

            $phone = System::strSpecialClear($phone);

            if (mb_strlen($phone, 'UTF-8') > 30) {

                $error_exception[0] = Ui::lang($App,'ERROR_USER_PHONE_MAX', 30);
                $error_exception[1] = Errors::getCode('ERROR_USER_PHONE_MAX');

            } else {

                if (!preg_match('/^[0-9-+()]+$/u', $phone))
                {

                    $error_exception[0] = Ui::lang($App,'ERROR_USER_PHONE_INVALID');
                    $error_exception[1] = Errors::getCode('ERROR_USER_PHONE_INVALID');

                }

            }

        }


        return $error_exception;

    }


    # Valid pass

    public static function validPass($App, $error_exception,$pass)
    {

        if(!isset($pass)){

            $error_exception[] = 'pass is required';
            $error_exception[0] = Errors::getCode('user pass is required');

        } else {

            $pass = System::strSpecialClear($pass);

            if (mb_strlen($pass, 'UTF-8') < 8) {

                $error_exception[0] = Ui::lang($App,'ERROR_USER_PASS_MIN', 8);
                $error_exception[1] = Errors::getCode('ERROR_USER_PASS_MIN');

            }

            else if (mb_strlen($pass, 'UTF-8') > 30) {

                $error_exception[0] = Ui::lang($App,'ERROR_USER_PASS_MAX', 30);
                $error_exception[1] = Errors::getCode('ERROR_USER_PASS_MAX');

            } else {

                if (!preg_match('/^[a-z0-9_-]+$/u', $pass))
                {

                    $error_exception[0] = Ui::lang($App,'ERROR_USER_PASS_INVALID');
                    $error_exception[1] = Errors::getCode('ERROR_USER_PASS_INVALID');

                }

            }



        }


        return $error_exception;

    }




    /**
     * UPDATE USERS
     *
     */

    # update user settings

    public static function updateUserSettings($value, $users)
    {


        $users->settings = JSON::encode($value);

        $users->save();

        JSON::buildJsonContent(
            'success',
            'ok'

        );

    }


    public static function updateUserPass($value, $users) {

        $users->user_pass = System::securePass($value->new_pass);

        $users->save();

        JSON::buildJsonContent(
            'success',
            'ok'

        );
    }


    /**
     * INSERT INTO ACHIVE
     *
     */

    # add param

    public static function addNewAchieve($data, $api, $App) {



        # SQL Query

        $phql = 'INSERT INTO gem_user_achieves (user_id, achieve_id, '
            . '	date_add) '
            . ' VALUES (:user_id:, :achieve_id:, :date_add:)';



        $status = $api->modelsManager->executeQuery(

            $phql,

            [

                "user_id" => $data->user_id,

                "achieve_id" => $data->achieve_id,

                "date_add" => System::toDay(),



            ]
        );


        if($App->response_view) {

            if ($status->success() === true) {

                return $status->getModel()->id;



            } else {

                $errors = [];

                foreach ($status->getMessages() as $message) {
                    $errors[] = $message->getMessage();
                }

                JSON::buildJsonContent(
                    $errors,
                    'error'

                );
            }
        }

    }


    public static function getUsersByIds($ids)
    {

        $placeholder = '';

        for($i = 0; $i < count($ids); $i++){

            if($placeholder != '') {

                $placeholder .= ',';

            }

            $placeholder .= '?'.$i;

        }


        $sql = "id IN (".$placeholder.")";


        $users = self::find(
            [
                $sql,
                "bind" => $ids,


            ]
        );

        return $users;
    }




    public static function clearRelationsDep($api,$App,$id) {

        $phql = "UPDATE Users SET dep_id = 0 WHERE dep_id = '".$id."' AND team_id = 0";

        $api->modelsManager->executeQuery($phql);

    }


    public static function updateRelationsDep($api,$bind) {

        $phql = "UPDATE Users SET dep_id = ".$bind['dep_id']." WHERE team_id = '".$bind['team_id']."'";

        $api->modelsManager->executeQuery($phql);

    }


    public static function clearCoins($api,$comapny) {

        $phql = "UPDATE Users SET balance = 0 WHERE company_id = '".$comapny."'";

        $api->modelsManager->executeQuery($phql);

    }

    public static function clearPoints($api,$comapny) {

        $phql = "UPDATE Users SET rating = 0, game_level = 0 WHERE company_id = '".$comapny."'";

        $api->modelsManager->executeQuery($phql);

    }



    public static function rawQuery($api,$phql,$bind) {


        $res = $api->modelsManager->executeQuery(

            $phql,

            $bind
        );

        return $res;


    }



    public static function getSearch($App,$api,$search,$params) {


        $search = System::strSpecialClear($search);


        $no_client = '';

        if($App->userPermission == 'gamer') {

            $no_client = " AND id != '".$App->uid."'";

        }


        if(!isset($params['order'])) {

            $params['order'] = 'DESC';

        }

        if($search != '') {


            $search_full = trim($search);

            $search_full = explode(' ', $search_full);

            $search = trim($search);

            if(count($search_full) > 1) {


                $phql = "SELECT id, photo, name, balance, full_name, rating, game_level, dep_id, team_id, user_role FROM Users WHERE user_level = 'gamer' AND status_approve != 3".$no_client." AND company_id = :company_id: AND (name LIKE :name: AND full_name LIKE :full_name:) ORDER BY balance ".$params['order']." LIMIT ".$params['offset'].",".$params['limit']."";

                $sql_count = "user_level = 'gamer' AND status_approve != 3".$no_client." AND company_id = :company_id: AND (name LIKE :name: AND full_name LIKE :full_name:)";

                $bind = [

                    "name" => '%'.$search_full[0].'%',

                    "full_name" => '%'.$search_full[1].'%',

                    "company_id" => Companies::getDefault($App),

                ];

                $res = $api->modelsManager->executeQuery(

                    $phql,

                    $bind
                );

            } else {


                $phql = "SELECT id, photo, balance, name, full_name, rating, game_level, dep_id, team_id, user_role FROM Users WHERE user_level = 'gamer' AND status_approve != 3".$no_client." AND company_id = :company_id: AND (name LIKE :name: OR full_name LIKE :full_name:) ORDER BY balance ".$params['order']."  LIMIT ".$params['offset'].",".$params['limit']."";

                $sql_count = "user_level = 'gamer' AND status_approve != 3".$no_client." AND company_id = :company_id: AND (name LIKE :name: OR full_name LIKE :full_name:)";

                $bind = [

                    "name" => '%'.$search.'%',

                    "full_name" => '%'.$search.'%',

                    "company_id" => Companies::getDefault($App),

                ];


                $res = $api->modelsManager->executeQuery(

                    $phql,

                    $bind
                );

            }



        } else {

            $phql = "SELECT id, photo, balance, name, full_name, rating, game_level, dep_id, team_id, user_role FROM Users WHERE user_level = 'gamer' AND status_approve != 3".$no_client." AND company_id = :company_id: ORDER BY balance ".$params['order']." LIMIT ".$params['offset'].",".$params['limit']."";

            $sql_count = "user_level = 'gamer' AND status_approve != 3".$no_client." AND company_id = :company_id:";

            $bind = [

                "company_id" => Companies::getDefault($App),

            ];

            $res = $api->modelsManager->executeQuery(

                $phql,

                $bind
            );
        }




        $count = Users::count(
            [
                $sql_count,
                "bind" => $bind,


            ]
        );



        $count = $count - ($params['offset'] + $params['limit']);

        if($count < 0) {
            $count = 0;
        }


            if(count($res) > 0) {

                $result = [];

                $i = 0;
                foreach ($res as $val) {


                    $result[$i]['id'] = $val->id;

                    $result[$i]['name'] =  $val->name.' '.$val->full_name;

                    $result[$i]['full_name'] = $val->full_name;

                    $result[$i]['balance'] = $val->balance;






                    //$result[$i]['rating'] = $val->rating;

                    //$result[$i]['game_level'] = $val->game_level;

                    $result[$i]['photo'] = System::getImageUrl($val->photo);

                    /*
                    if($val->dep_id > 0) {

                        $dep = Departments::findFirstById($val->dep_id);

                        $result[$i]['dep_name'] = $dep->dep_name;
                    }


                    if($val->team_id > 0) {
                        $team = Teams::findFirstById($val->team_id);

                        $result[$i]['team_name'] = $team->team_name;
                    }
                    */

                    $i++;
                }


                $res = [];

                $res['length'] = $count;

                $res['data'] = $result;



                if($App->constructor) {

                    return $res;

                } else {

                    JSON::buildJsonContent(
                        $res,
                        'ok'

                    );

                }



            } else {

                JSON::buildJsonContent(
                    'not-found',
                    'ok'

                );

            }






    }


    /**
     * INSERT INTO USERS
     *
     */


    # add user

    public static function addNewUser($value,$api,$App, $level = 'admin')
    {


            # Init System Vars

            $genId =  System::genID();

            $dirSection = System::dirSection();

            $genHash = System::genHash();

            $email =  System::phoneEmail($value->email,'email');

            $phone = System::phoneEmail($value->email,'phone');

            $com_id = 0;

            if(isset($value->company_id)) {
                $com_id = $value->company_id;
            }


            if(isset($value->pass)) {

                $pass = System::securePass($value->pass);

            } else {

                $pass = System::securePass($genId);

            }

            $full_name = '';

            $third_name = '';

            if(isset($value->full_name)) {

                $full_name = System::strSpecialClear($value->full_name);

            }
            if(isset($value->third_name)) {

                $third_name = System::strSpecialClear($value->third_name);

            }


            $start_work = System::toDay('date');

            if(isset($value->start_work)) {

                $start_work = $value->start_work;

            }

            $dep_id = 0;

            if(isset($value->dep_id)) {

                $dep_id = $value->dep_id;

            }


            # SQL Query

            $phql = 'INSERT INTO users (status_approve, user_level, system_id, dir_section, '
            . 'hash, oauth_id, api_id, oauth_name, name, email, user_phone, user_pass, full_name, third_name, photo, gender, date_reg, '
            . 'date_visit, rating, game_level, balance, balance_free, auto_balance, user_role, level_role, company_id, dep_id, team_id, start_work, rung, settings, status_display_rating, birthday, phone, messanger, work_position, text_status, tasks, tasks_static, reminder_day, reminder_month) VALUES (:status_approve:, :user_level:, '
                . ':system_id:, :dir_section:, :hash:, :oauth_id:, :oauth_name:, :api_id:, '
            . ':name:, :email:, :user_phone:, :user_pass:, :full_name:, :third_name:, '
            . ':photo:, :gender:, :date_reg:, :date_visit:, :rating:, :game_level:, :balance:, :balance_free:, :auto_balance:, :user_role:, :level_role:, :company_id:, :dep_id:, :team_id:, :start_work:, :rung:, :settings:, :status_display_rating:, :birthday:, :phone:, :messanger:, :work_position:, :text_status:, :tasks:, :tasks_static:, :reminder_day:, :reminder_month:)';



            $status = $api->modelsManager->executeQuery(

                $phql,

                [

                    "status_approve" => 0,

                    "user_level" => $level,

                    "system_id" => $genId,

                    "dir_section" => $dirSection,

                    "hash" => $genHash,

                    "oauth_id" => '',

                    "api_id" => '',

                    "oauth_name" => '',

                    "name" => System::strSpecialClear(System::strLength($value->name,30)),

                    "email" => System::strSpecialClear($email),

                    "user_phone" => System::strSpecialClear(System::strLength($phone,50)),

                    "user_pass" => System::strLength($pass,255),

                    "full_name" => System::strSpecialClear(System::strLength($full_name,100)),

                    "third_name" => System::strSpecialClear(System::strLength($third_name,50)),

                    "photo" => '',

                    "gender" => 0,

                    "date_reg" => System::toDay(),

                    "date_visit" => System::toDay(),

                    "rating" => 0,

                    "game_level" => 1,

                    "balance" => 0,

                    "balance_free" => 0,

                    "auto_balance" => 0,

                    "user_role" => 0,

                    "level_role" => 0,

                    "company_id" => $com_id,

                    "dep_id" => $dep_id,

                    "team_id" => 0,

                    "start_work" => $start_work,

                    "rung" => 0,

                    "settings" => self::defaultUserSettings($App),

                    "status_display_rating" => 1,

                    "birthday" => "0000-00-00",

                    "phone" => "",

                    "messanger" => "",

                    "work_position" => "",

                    "text_status" => "",

                    "tasks" => "",

                    "tasks_static" => 0,

                    "reminder_day" => "",

                    "reminder_month" => ""

                ]
            );


            if ($status->success() === true) {

                $user_id = $status->getModel()->id;


                /**
                 * create dirs
                 *
                 */
                System::mkdirs(UPLOADS_PATH.'/'.$dirSection.'/'.$genId.'/',0755);

                System::mkdirs(UPLOADS_PATH.'/'.$dirSection.'/'.$genId.'/user_content/',0755);

                System::mkdirs(UPLOADS_PATH.'/'.$dirSection.'/'.$genId.'/user_content/miniature/',0755);



                if($level == 'admin') {

                    Gem_settings::addNewSettings($user_id, $api);

                }




                /**
                 * response
                 *
                 */
                $frontend = [];

                $frontend['id'] = $user_id;

                $frontend['status_approve'] = 0;

                $frontend['name'] = $value->name;

                $frontend['email'] = $email;

                $frontend['phone'] = $phone;

                $frontend['date_reg'] = System::toDay();

                $frontend['code'] = Success::getCode('user create');



                if($App->constructor === false) {

                    if($App->activate) {







                        $company = Companies::findFirstById($com_id);

                        $template = Mail_template::findFirstByCompany_id($com_id);




                        $body = [


                            "company_name" => $template->company_from,

                            "company_email" => $company->email,

                            "email" => $email,

                            "message_title" => $template->title_create_account,

                            "message_link" => $company->member_panel,

                            "message_pass" => $genId,

                            "name_user" => $value->name." ".$full_name,

                            "template" => "pass_player"

                        ];




                        $Controller = new SendMailController();

                        $Controller->mail($App,$body,$template);


                        $auth = Users::findFirstById($user_id);

                        Auth::createAuth($auth,$api,'auto');








                    } else {



                        JSON::buildJsonContent(
                            $frontend,
                            'created'

                        );

                    }

                } else {

                    return $user_id;

                }







            } else {

                $errors = [];

                foreach ($status->getMessages() as $message) {

                    $errors[] = $message->getMessage();

                }

                JSON::buildJsonContent(

                    $errors,

                    'error'

                );
            }



    }





    /** Properties methods */



    public function getId()
    {
        return $this->id;
    }



    # Name user methods


    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }



    # Email user methods

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getEmail()
    {
        return $this->email;
    }


    # Gender user methods


    # Ful name user methods


    # phone user methods


    # password user methods








    /**
     * Builing Model Data for view
     */

    public static function buildDataUsers($list_keys,$data,$Controller) {

        $data_by_keys = [];

        for($i = 0; $i < count($list_keys); $i++) {

            $key_val = trim($list_keys[$i]);

            if($key_val == 'id') {

                $data_by_keys['id'] = $data->id;

            }

            if($key_val == 'status_approve') {

                $data_by_keys['status_approve'] = $data->status_approve;

            }

            if($key_val == 'user_level') {

                $data_by_keys['user_level'] = $data->user_level;

            }

            if($key_val == 'system_id') {

                $data_by_keys['system_id'] = $data->system_id;

            }






            if($key_val == 'api_id') {

                $data_by_keys['api_id'] = '';

                if($data->api_id != '') {

                    $connects = JSON::decode($data->api_id);

                    $connect_data = [];
                    $i2 = 0;

                    foreach ($connects as $val) {

                        $connect = Gem_api_connect::findFirstById($val->crm_id);


                        $connect_data[$i2]['crm_id'] = $connect->id;
                        $connect_data[$i2]['uid'] = $val->uid;
                        $connect_data[$i2]['crm_custom_name'] = $connect->custom_name;

                        $i2++;

                    }


                    $data_by_keys['api_id'] = $connect_data;


                }


            }

            if($key_val == 'all_teams') {

                $data_by_keys['all_teams'] = Teams::getAllTeams($Controller->App, $Controller->api, $data->team_id);

            }

            if($key_val == 'dep_list') {

                $data_by_keys['dep_list'] = Departments::getDepartments($Controller->App, $Controller->api);

            }

            if($key_val == 'team_list') {

                $data_by_keys['team_list'] = Teams::getTeams($Controller->App, $Controller->api);

            }

            if($key_val == 'dir_section') {

                $data_by_keys['dir_section'] = $data->dir_section;

            }

            if($key_val == 'session_id') {

                $data_by_keys['session_id'] = $data->session_id;

            }

            if($key_val == 'hash') {

                $data_by_keys['hash'] = $data->hash;

            }

            if($key_val == 'oauth_id') {

                $data_by_keys['oauth_id'] = $data->oauth_id;

            }

            if($key_val == 'oauth_name') {

                $data_by_keys['oauth_name'] = $data->oauth_name;

            }

            if($key_val == 'user_agent') {

                $data_by_keys['user_agent'] = $data->user_agent;

            }

            if($key_val == 'name') {

                $data_by_keys['name'] = $data->name.' '.$data->full_name;

            }

            if($key_val == 'first_name') {

                $data_by_keys['first_name'] = $data->name;

            }

            if($key_val == 'last_name') {

                $data_by_keys['last_name'] = $data->full_name;

            }

            if($key_val == 'third_name') {

                $data_by_keys['third_name'] = $data->third_name;

            }

            if($key_val == 'full_name') {

                $data_by_keys['full_name'] = $data->full_name;

            }

            if($key_val == 'email') {

                $data_by_keys['email'] = $data->email;

            }

            if($key_val == 'phone') {

                $data_by_keys['phone'] = $data->phone;

            }

            if($key_val == 'messanger') {

                $data_by_keys['messenger'] = $data->messanger;

            }

            if($key_val == 'birthday') {

                $data_by_keys['birthday'] = $data->birthday;

            }

            if($key_val == 'work_position') {

                $data_by_keys['work_position'] = $data->work_position;

            }


            if($key_val == 'photo') {

                $data_by_keys['photo'] = System::getImageUrl($data->photo);



            }

            if($key_val == 'achieves') {

                $data_by_keys['achieves'] = Gem_user_achieves::get($data->id);
            }

            if($key_val == 'gender') {

                $data_by_keys['gender'] = $data->gender;

            }

            if($key_val == 'date_reg') {

                $data_by_keys['date_reg'] = $data->date_reg;

            }

            if($key_val == 'date_reg') {

                $data_by_keys['date_reg'] = $data->date_reg;

            }

            if($key_val == 'date_visit') {

                $data_by_keys['date_visit'] = $data->date_visit;

            }

            if($key_val == 'rating') {

                $data_by_keys['rating'] = $data->rating;

            }

            if($key_val == 'game_level') {

                if($data->game_level < 1) {

                    $data->game_level = 1;

                }

                $data_by_keys['game_level'] = $data->game_level;

            }



            if($key_val == 'balance') {

                $data_by_keys['balance'] = $data->balance;

            }

            if($key_val == 'balance_free') {

                $data_by_keys['balance_free'] = $data->balance_free;

            }



            if($key_val == 'settings') {

                $data_by_keys['settings'] = $data->settings;

            }

            if($key_val == 'company_id') {

                $data_by_keys['company_id'] = $data->company_id;

            }

            if($key_val == 'department') {

                $dep = Departments::findFirstById($data->dep_id);

                $data_by_keys['dep_name'] = '';

                if(isset($dep->id)) {

                    $data_by_keys['dep_name'] = $dep->dep_name;

                }


                $data_by_keys['dep_id'] = $data->dep_id;

            }

            if($key_val == 'dep_id') {

                $data_by_keys['dep_id'] = $data->dep_id;
            }


            if($key_val == 'tasks') {


                $user_tasks = [];
                $user_tasks['selected'] = [];
                $user_tasks['all'] = [];


                $tasks = JSON::decode($data->tasks);

                if(count($tasks) > 0) {

                    $sql = '';

                    $ni = 0;

                    $bind = [];

                    foreach($tasks as $task) {

                        if($sql != '') {

                            $sql .= " OR ";

                        }

                        $sql .= "id = :id_".$ni.":";

                        $bind['id_'.$ni] = $task['id'];

                        $ni++;

                    }

                    $tasks_data = Gem_tasks::find(
                        [
                            $sql,
                            "bind" => $bind,
                            "order" => "date_add DESC",

                        ]
                    );



                    if(count($tasks_data) > 0) {

                        $nc = 0;
                        foreach($tasks_data as $task) {

                            $user_tasks['selected'][$nc] = [];
                            $user_tasks['selected'][$nc]['id'] = $task->id;
                            $user_tasks['selected'][$nc]['title'] = $task->title;
                            $nc++;
                        }

                    }


                }



                $sql = "company_id = :company_id:";
                $bind = [];
                $bind['company_id'] = $data->company_id;

                $tasks_data = Gem_tasks::find(
                    [
                        $sql,
                        "bind" => $bind,
                        "order" => "date_add DESC",

                    ]
                );

                $nc = 0;

                if(count($tasks_data) > 0) {

                    foreach($tasks_data as $task) {

                        $user_tasks['all'][$nc] = [];
                        $user_tasks['all'][$nc]['id'] = $task->id;
                        $user_tasks['all'][$nc]['title'] = $task->title;
                        $nc++;
                    }

                }




                $data_by_keys['tasks'] = $user_tasks;

            }

            if($key_val == 'team') {

                $team = Teams::findFirstById($data->team_id);

                $data_by_keys['team_name'] = '';

                if(isset($team->id)) {

                    $data_by_keys['team_name'] = $team->team_name;

                }


                $data_by_keys['team_id'] = $data->team_id;

            }

            if($key_val == 'reminder_day') {

                $data_by_keys['reminder_day'] = JSON::decode($data->reminder_day);

            }

            if($key_val == 'reminder_month') {

                $data_by_keys['reminder_month'] = JSON::decode($data->reminder_month);

            }

            if($key_val == 'team_id') {

                $data_by_keys['team_id'] = $data->team_id;

            }

            if($key_val == 'start_work') {

                $data_by_keys['start_work'] = $data->start_work;

            }

        }

        return $data_by_keys;

    }


}