<?php

/**
 * Created by PhpStorm.
 * User: miller321x
 * Date: 17.04.17
 * Time: 10:53
 */
use Phalcon\Mvc\Model;

class Gem_params_statistic extends Model
{

    public $id;

    public $company_id;

    public $uid;

    public $date_add;

    public $param_name;

    public $param_value;

    public $param_start_value;



    public static function getParam($param,$uid) {


        $sql = "uid = :uid: AND param_name = :param_name:";

        $bind = [
            "uid" => $uid,
            "param_name" => $param,
        ];


        $data = self::find(
            [
                $sql,
                "bind" => $bind,

            ]
        );



        foreach ($data as $val) {

            return $val->param_value;

        }



    }


    public static function clearStatistic($api,$company) {


            $phql = "DELETE FROM Gem_params_statistic WHERE company_id = '".$company."'";

            $api->modelsManager->executeQuery($phql);




    }


    /**
     * INSERT INTO PARAMS LOG
     *
     */

    # add param

    public static function addNewParamData($data, $api, $App) {



        $data->company_id = Companies::getDefault($App);


        if(!isset($data->param_value) and $data->param_value == '') {

            $data->param_value = 1;

        }




        # SQL Query

        $phql = 'INSERT INTO gem_params_statistic (company_id, uid, '
            . '	date_add, param_name, param_value, param_start_value) '
            . ' VALUES (:company_id:, :uid:, :date_add:, :param_name:, :param_value:, :param_start_value:'
            . ')';



        $status = $api->modelsManager->executeQuery(

            $phql,

            [

                "company_id" => $data->company_id,

                "uid" => System::intLength($data->uid,11),

                "date_add" => System::toDay(),

                "param_name" => System::strLength($data->param_name,30),

                "param_value" => System::strLength($data->param_value,30),

                "param_start_value" => System::strLength($data->param_value,30),


            ]
        );




        if ($status->success() === true) {

            return $status->getModel()->id;

        } else { return null; }





    }


}