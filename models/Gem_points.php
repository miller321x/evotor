<?php

/**
 * Created by PhpStorm.
 * User: miller321x
 * Date: 05.05.17
 * Time: 18:00
 */
use Phalcon\Mvc\Model;

class Gem_points extends Model
{

    public $id;

    public $point_name;

    public $formula_id;

    public $period_mode;

    public $interval_have;

    public $company_id;

    public $special_func;


    # new point validation

    public static function validNewPoint($App, $data)
    {

        $exception = [];

        if(!isset($data->point_name) || $data->point_name == '') {
            $exception[0] = 'point_name is required';
            $exception[1] = Errors::getCode('point_name is required');
        }

        else if(!isset($data->formula_id) || $data->formula_id == '') {
            $exception[0] = 'formula_id is required';
            $exception[1] = Errors::getCode('point formula_id is required');
        }
        else if(!isset($data->period_mode) || $data->period_mode == '') {
            $exception[0] = 'period_mode is required';
            $exception[1] = Errors::getCode('period_mode is required');
        }
        else if(!isset($data->interval_have) || $data->interval_have == '') {
            $exception[0] = 'interval_have is required';
            $exception[1] = Errors::getCode('interval_have is required');
        }
        else {
            $exception = self::validName($App,$exception, $data->point_name);
        }


        return $exception;

    }

    public static function validUpdatePoints($App, $data)
    {

        $exception = [];


        if(!isset($data->point_name) || $data->point_name == '') {
            $exception[0] = 'point_name is required';
            $exception[1] = Errors::getCode('point_name is required');
        }

        else if(!isset($data->formula_id) || $data->formula_id == '') {
            $exception[0] = 'formula_id is required';
            $exception[1] = Errors::getCode('point formula_id is required');
        }
        else if(!isset($data->period_mode) || $data->period_mode == '') {
            $exception[0] = 'period_mode is required';
            $exception[1] = Errors::getCode('period_mode is required');
        }
        else if(!isset($data->interval_have) || $data->interval_have == '') {
            $exception[0] = 'interval_have is required';
            $exception[1] = Errors::getCode('interval_have is required');
        }

        else {
            $exception = self::validName($App,$exception, $data->point_name, false);
        }



        return $exception;

    }

    /** Properties validation */

    # Valid name

    public static function validName($App, $error_exception,$name, $unic = true)
    {

        $name = System::strSpecialClear($name);

        if(trim($name) == ''){

            $error_exception[0] = 'point_name is empty';
            $error_exception[1] = Errors::getCode('point_name is empty');

        } else {

            if (mb_strlen($name,'UTF-8') > 30) {

                $error_exception[0] = 'max length 30';
                $error_exception[1] = Errors::getCode('point max length 30');

            }

            else if (!preg_match(REGULAR_VARCHAR_NAME, $name))
            {
                $error_exception[0] = 'invalid name';
                $error_exception[1] = Errors::getCode('point invalid name');
            }

            else {

                if($unic) {
                    $res = self::findFirstByPoint_name($name);


                    if (isset($res->id)) {

                        $error_exception[0] = 'point name used';
                        $error_exception[1] = Errors::getCode('point name used');

                    }
                }

            }

        }


        return $error_exception;

    }


    /**
     * SELECT POINTS
     *
     */

    public static function getPointsFormulas($api, $App) {

        $company_id = Companies::getDefault($App);

        # SQL Query

        $phql = "SELECT p.id, p.formula_id, p.point_name, p.period_mode, p.interval_have, f.formula " .
            "FROM Gem_points p, Gem_formules f WHERE p.company_id = :company_id: AND f.id = p.formula_id";



        $rows = $api->modelsManager->executeQuery(

            $phql,

            [

                "company_id" => $company_id,

            ]
        );

        return $rows;


    }


    /**
     * INSERT INTO POINTS
     *
     */

    # add point

    public static function addNewPoint($data, $api, $App) {



        if(!isset($data->company_id)) {

            $data->company_id = Companies::getDefault($App);

        }

        $special_func = 0;

        if(isset($data->special_func)) {

            $special_func = System::intLength($data->special_func,11);

        }



        # SQL Query

        $phql = 'INSERT INTO gem_points (point_name, formula_id, '
            . '	period_mode, interval_have, company_id, special_func) '
            . ' VALUES (:point_name:, :formula_id:, :period_mode:,'
            . ':interval_have:, :company_id:, :special_func:)';



        $status = $api->modelsManager->executeQuery(

            $phql,

            [

                "point_name" => System::strLength($data->point_name,100),

                "formula_id" => System::intLength($data->formula_id,11),

                "period_mode" => $data->period_mode,

                "interval_have" => $data->interval_have,

                "company_id" => $data->company_id,

                "special_func" => $special_func,


            ]
        );


        if($App->response_view) {

            if ($status->success() === true) {

                $id = $status->getModel()->id;


                $frontend = [];

                $frontend['id'] = $id;

                $frontend['code'] = Success::getCode('point create');


                JSON::buildJsonContent(
                    $frontend,
                    'created'

                );


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

    /**
     * Builing Model Data for view
     */

    public static function buildDataPoints($list_keys,$data,$Controller) {

        $data_by_keys = [];

        for($i = 0; $i < count($list_keys); $i++) {

            $key_val = trim($list_keys[$i]);

            if($key_val == 'id') {

                $data_by_keys['id'] = $data->id;

            }

            if($key_val == 'point_name') {

                $data_by_keys['point_name'] = $data->point_name;

            }

            if($key_val == 'formula_id') {

                $data_by_keys['formula_id'] = $data->formula_id;

            }

            if($key_val == 'period_mode') {

                $data_by_keys['period_mode'] = $data->period_mode;

            }

            if($key_val == 'interval_have') {

                $data_by_keys['interval_have'] = $data->interval_have;

            }

            if($key_val == 'company_id') {

                $data_by_keys['company_id'] = $data->company_id;

            }

        }

        return $data_by_keys;

    }





}