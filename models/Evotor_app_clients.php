<?php

/**
 * Created by PhpStorm.
 * User: miller321x
 * Date: 23.11.17
 * Time: 17:10
 */

use Phalcon\Mvc\Model;

class Evotor_app_clients extends Model
{

    public $id;

    public $client_id;

    public $type_state;

    public $date_add;



    /**
     * INSERT INTO APP CLIENTS
     *
     */

    # add departments

    public static function addNewInstallApp($value,$api,$App)
    {


        # SQL Query

        $phql = 'INSERT INTO Evotor_app_clients (client_id, type_state, date_add)' .
            ' VALUES (:client_id:, :type_state:, :date_add:)';

        $status = $api->modelsManager->executeQuery(

            $phql,

            [

                "client_id" => $value->data->userId,

                "type_state" => $value->type,

                "date_add" => $value->timestamp,



            ]
        );

        if ($status->success() === true) {

            $id = $status->getModel()->id;


            $frontend = [];

            $frontend['id'] = $id;

            $frontend['code'] = Success::getCode('installed evator create');


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