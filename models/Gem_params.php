<?php

/**
 * Created by PhpStorm.
 * User: miller321x
 * Date: 17.04.17
 * Time: 10:41
 */
use Phalcon\Mvc\Model;

class Gem_params extends Model
{

    public $id;

    public $company_id;

    public $param_type;

    public $api_connect_id;

    public $param_name;

    public $param_custom_name;

    public $update_type;

    public $format;

    public $time_live;

    public $last_update;

    public $formula_id;

    public $show_statistic;



    # new connect validation

    public static function validNewParam($App, $data)
    {

        $exception = [];

        if(!isset($data->api_connect_id)) {
            $exception[0] = 'api_connect_id is required';
            $exception[1] = Errors::getCode('api_connect_id is required');
        }

        else if(!isset($data->param_name)) {
            $exception[0] = 'param_name is required';
            $exception[1] = Errors::getCode('param_name is required');
        } else {
            $exception = self::validName($App,$exception, $data->param_name);
        }


        return $exception;

    }

    public static function validUpdateParam($App, $data)
    {

        $exception = [];

        if(!isset($data->api_connect_id)) {
            $exception[0] = 'api_connect_id is required';
            $exception[1] = Errors::getCode('api_connect_id is required');
        }

        else if(!isset($data->param_name)) {
            $exception[0] = 'param_name is required';
            $exception[1] = Errors::getCode('param_name is required');
        } else {
            $exception = self::validName($App,$exception, $data->param_name, false);
        }


        return $exception;

    }

    /** Properties validation */

    # Valid name

    public static function validName($App, $error_exception,$name, $unic = true)
    {

        $name = System::strSpecialClear($name);


        if(trim($name) == ''){

            $error_exception[0] = 'param_name is empty';
            $error_exception[1] = Errors::getCode('param_name is empty');

        } else {

            if (mb_strlen($name,'UTF-8') > 30) {

                $error_exception[0] = 'max length 30';
                $error_exception[1] = Errors::getCode('param max length 30');

            }

            else if (!preg_match(REGULAR_VARCHAR_NAME, $name))
            {
                $error_exception[0] = 'invalid name';
                $error_exception[1] = Errors::getCode('param invalid name');
            }

            else {

                if($unic) {
                    $res = self::findFirstByParam_name($name);


                    if (isset($res->id)) {

                        $error_exception[0] = 'param name used';
                        $error_exception[1] = Errors::getCode('param name used');

                    }
                }

            }

        }





        return $error_exception;

    }



    /**
     * INSERT INTO PARAMS
     *
     */

    # add param

    public static function addNewParam($data, $api, $App) {

        if(!isset($data->company_id)) {

            $data->company_id = Companies::getDefault($App);

        }

        if(!isset($data->param_type)) {
            $data->param_type = 1;
        }

        if(!isset($data->param_custom_name)) {
            $data->param_custom_name = $data->param_name;
        }


        if(!isset($data->update_type)) {
            $data->update_type = 1;
        }

        if(!isset($data->param_unit)) {
            $data->format = '';
        } else {
            $data->format = $data->param_unit;
        }

        if(!isset($data->time_live)) {
            $data->time_live = 1;
        }

        if(!isset($data->formula_id)) {
            $data->formula_id = 0;
        }

        if($data->api_connect_id) {
            $data->connect_id = $data->api_connect_id;
        }

        $show_statistic = 0;
        if(isset($data->show_statistic)) {
            $show_statistic = $data->show_statistic;
        }

        # SQL Query

        $phql = 'INSERT INTO gem_params (company_id, param_type, '
            . '	api_connect_id, param_name, param_custom_name, update_type, format, time_live, 	last_update, formula_id, show_statistic) '
            . ' VALUES (:company_id:, :param_type:, :api_connect_id:, :param_name:, :param_custom_name:,'
            . ':update_type:, :format:, :time_live:, :last_update:, :formula_id:, :show_statistic:)';



        $status = $api->modelsManager->executeQuery(

            $phql,

            [

                "company_id" => $data->company_id,

                "param_type" => $data->param_type,

                "api_connect_id" => System::intLength($data->connect_id,11),

                "param_name" => System::strLength($data->param_name,50),

                "param_custom_name" => System::strLength($data->param_custom_name,50),

                "update_type" => $data->update_type,

                "format" => System::strLength($data->format,50),

                "time_live" => System::intLength($data->time_live,11),

                "last_update" => System::toDay(),

                "formula_id" => System::intLength($data->formula_id,11),

                "show_statistic" => System::intLength($show_statistic,1),


            ]
        );


        if($App->response_view) {

            if ($status->success() === true) {

                $param_id = $status->getModel()->id;


                $frontend = [];

                $frontend['id'] = $param_id;

                $frontend['code'] = Success::getCode('param create');


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

    public static function buildDataParams($list_keys,$data,$Controller) {

        $data_by_keys = [];

        for($i = 0; $i < count($list_keys); $i++) {

            $key_val = trim($list_keys[$i]);

            if($key_val == 'id') {

                $data_by_keys['id'] = $data->id;

            }

            if($key_val == 'company_id') {

                $data_by_keys['company_id'] = $data->company_id;

            }

            if($key_val == 'param_type') {

                $data_by_keys['param_type'] = $data->param_type;

            }

            if($key_val == 'api_connect_id') {

                $data_by_keys['api_connect_id'] = $data->api_connect_id;

            }


            if($key_val == 'param_name') {

                $data_by_keys['param_name'] = $data->param_name;

            }


            if($key_val == 'param_custom_name') {

                $data_by_keys['param_custom_name'] = $data->param_custom_name;

            }


            if($key_val == 'update_type') {

                $data_by_keys['update_type'] = $data->update_type;

            }

            if($key_val == 'param_unit') {

                $data_by_keys['param_unit'] = $data->format;

            }

            if($key_val == 'time_live') {

                $data_by_keys['time_live'] = $data->time_live;

            }

            if($key_val == 'last_update') {

                $data_by_keys['last_update'] = $data->last_update;

            }

            if($key_val == 'formula_id') {

                if($data->param_type == 2) {

                    $data_by_keys['formula_id'] = $data->formula_id;

                }


            }

        }

        return $data_by_keys;

    }


}