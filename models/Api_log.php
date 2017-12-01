<?php

/**
 * Created by PhpStorm.
 * User: miller321x
 * Date: 16.05.17
 * Time: 16:37
 */
use Phalcon\Mvc\Model;

class Api_log extends Model
{

    public $id;

    public $convert_data;

    public $api_data;

    public $date_add;





    /**
     * INSERT INTO LOG
     *
     */

    # add team

    public static function addNewApiLog($value,$api,$App)
    {

        // Init


        # SQL Query

        $phql = 'INSERT INTO api_log (convert_data, api_data, date_add)' .
            ' VALUES (:convert_data:, :api_data:, :date_add:)';

        $status = $api->modelsManager->executeQuery(

            $phql,

            [



                "convert_data" => $value->request,

                "api_data" => $value->response,

                "date_add" => System::toDay(),





            ]
        );


    }
}