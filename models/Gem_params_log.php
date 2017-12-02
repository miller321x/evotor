<?php

/**
 * Created by PhpStorm.
 * User: miller321x
 * Date: 17.04.17
 * Time: 10:44
 */
use Phalcon\Mvc\Model;

class Gem_params_log extends Model
{

    public $id;

    public $company_id;

    public $uid;

    public $date_add;

    public $param_name;

    public $param_value;

    public $status;




    public static function clearLog($api,$com_id) {

        $phql = "UPDATE Gem_params_log SET status = 1 WHERE company_id = '".$com_id."'";

        $api->modelsManager->executeQuery($phql);
    }


    public static function rawQuery($api,$phql,$bind) {


        $res = $api->modelsManager->executeQuery(

            $phql,

            $bind
        );

        return $res;


    }


    /**
     * INSERT INTO PARAMS LOG
     *
     */

    # add param

    public static function addNewParamData($data, $api, $App) {

        if(!isset($data->company_id)) {

            //$data->company_id = Companies::getDefault($App);

        }


        $data->company_id = 3;


        # SQL Query

        $phql = 'INSERT INTO gem_params_log (company_id, uid, '
            . '	date_add, param_name, param_value, status) '
            . ' VALUES (:company_id:, :uid:, :date_add:, :param_name:, :param_value:, :status:'
            . ')';



        $api->modelsManager->executeQuery(

            $phql,

            [

                "company_id" => $data->company_id,

                "uid" => $data->uid,

                "date_add" => System::toDay(),

                "param_name" => $data->param_name,

                "param_value" => $data->param_value,

                "status" => 0,


            ]
        );




    }


}