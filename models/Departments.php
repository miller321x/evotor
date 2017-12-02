<?php

/**
 * Created by PhpStorm.
 * User: miller321x
 * Date: 14.04.17
 * Time: 17:30
 */
use Phalcon\Mvc\Model;

class Departments extends Model
{

    public $id;

    public $dep_name;

    public $dep_image;

    public $date_add;

    public $status;

    public $company_id;


    # new department validation

    public static function validNewDepartment($App, $value)
    {

        $exception = [];

        if(!isset($value->dep_name)) {
            $exception[0] = 'dep_name is required';
            $exception[1] = Errors::getCode('dep_name is required');
        }
        else {

            if(isset($value->dep_name)) {

                $exception = self::validName($App, $exception, $value->dep_name, true);


            }
        }



        return $exception;

    }

    public static function validUpdateDepartment($App, $value)
    {

        $exception = [];


        if(!isset($value->dep_name)) {
            $exception[0] = 'dep_name is required';
            $exception[1] = Errors::getCode('dep_name is required');
        } else {
            $exception = self::validName($App, $exception, $value->dep_name, false);
        }



        return $exception;

    }



    /** Properties validation */

    # Valid name

    public static function validName($App, $error_exception, $name = '' , $uniq = false)
    {


        if(trim($name) == ''){

            $error_exception[0] = 'dep_name is empty';
            $error_exception[1] = Errors::getCode('dep_name is empty');

        } else {

            $name = System::strSpecialClear($name);


            if (mb_strlen($name,'UTF-8') > 100) {

                $error_exception[0] = Ui::lang($App,'ERROR_DEP_NAME_MAX', 100);
                $error_exception[1] = Errors::getCode('ERROR_DEP_NAME_MAX');

            }

            else if (!preg_match(REGULAR_VARCHAR_NAME, $name))
            {
                $error_exception[0] = Ui::lang($App,'ERROR_DEP_NAME_INVALID');
                $error_exception[1] = Errors::getCode('ERROR_DEP_NAME_INVALID');
            }

            else {

                if ($uniq) {


                    $res = self::findFirstByDep_name($name);

                    if (isset($res->id)) {

                        $error_exception[0] = 'dep name isset yet';
                        $error_exception[1] = Errors::getCode('dep name isset yet');

                    }



                }

            }



        }





        return $error_exception;

    }


    public static function getDepartments($App,$api) {


        $phql = "SELECT * FROM Departments WHERE company_id = :company_id:";

        $bind = [

            "company_id" => Companies::getDefault($App),

        ];


        $dep = [];

        $row = $api->modelsManager->executeQuery($phql,$bind);

        $i = 0;

        foreach ($row as $val) {

            $dep[$i] = [];

            $dep[$i]['dep_id'] = $val->id;

            $dep[$i]['dep_name'] = $val->dep_name;

            $dep[$i]['teams'] = Teams::getTeamsByDep($api,$val->id);


            $i++;

        }

        return $dep;


    }


    /**
     * INSERT INTO DEPARTMENTS
     *
     */

    # add departments

    public static function addNewDepartment($value,$api,$App)
    {


        if(!isset($value->dep_image)){

            $value->dep_image = '';

        } else {

            if($value->dep_image != '') {
                $value->dep_image = System::uploadImage($value->dep_image,$App);
            }

        }

        if(!isset($value->company_id)) {

            $value->company_id = Companies::getDefault($App);

        }


        # SQL Query

        $phql = 'INSERT INTO departments (dep_name, dep_image, date_add, status, company_id)' .
            ' VALUES (:dep_name:, :dep_image:, :date_add:, :status:, :company_id:)';

        $status = $api->modelsManager->executeQuery(

            $phql,

            [



                "dep_name" => $value->dep_name,

                "dep_image" => $value->dep_image,

                "date_add" => System::toDay(),

                "status" => 1,

                "company_id" => $value->company_id



            ]
        );


        if ($status->success() !== true) {

            $errors = [];

            foreach ($status->getMessages() as $message) {
                $errors[] = $message->getMessage();
            }

            JSON::buildJsonContent(
                $errors,
                'error'

            );

        }

        if($App->response_view) {

            if ($status->success() === true) {

                $dep_id = $status->getModel()->id;


                /**
                 * response
                 *
                 */
                $frontend = [];

                $frontend['id'] = $dep_id;

                $frontend['code'] = Success::getCode('department create');


                JSON::buildJsonContent(
                    $frontend,
                    'created'

                );


            }


        } else {

            return $status->getModel()->id;

        }





    }


    /**
     * Builing Model Data for view
     */

    public static function buildDataDepartments($list_keys,$data,$Controller) {

        $data_by_keys = [];

        for($i = 0; $i < count($list_keys); $i++) {

            $key_val = trim($list_keys[$i]);

            if($key_val == 'id') {

                $data_by_keys['id'] = $data->id;

            }

            if($key_val == 'dep_name') {

                $data_by_keys['dep_name'] = $data->dep_name;

            }

            if($key_val == 'teams_quantity') {

                $rowCount = Teams::count(["dep_id = :dep_id:", "bind" => ["dep_id" => $data->id]]);

                $data_by_keys['teams_quantity'] = $rowCount;

            }

            if($key_val == 'players_quantity') {

                $rowCount = Users::count(["dep_id = :dep_id: AND company_id = :company_id: AND status_approve != 3", "bind" => ["dep_id" => $data->id, "company_id" => $data->company_id]]);

                $data_by_keys['players_quantity'] = $rowCount;

            }


            if($key_val == 'dep_image') {

                $data_by_keys['dep_image'] = System::getImageUrl($data->dep_image);

            }


            if($key_val == 'status') {

                $data_by_keys['status'] = $data->status;

            }

            if($key_val == 'date_add') {

                $data_by_keys['date_add'] = $data->date_add;

            }

            if($key_val == 'company_id') {

                $data_by_keys['company_id'] = $data->company_id;

            }


        }

        return $data_by_keys;

    }



}