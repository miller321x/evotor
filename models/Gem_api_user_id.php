<?php

/**
 * Created by PhpStorm.
 * User: miller321x
 * Date: 16.05.17
 * Time: 11:28
 */
use Phalcon\Mvc\Model;

class Gem_api_user_id extends Model
{

    public $id;

    public $uid;

    public $connect_id;

    public $crm_user_id;



    /**
     * INSERT INTO API USER ID
     *
     */

    # add user id

    public static function addNewUserId($value,$api,$App = null)
    {



        # SQL Query

        $phql = 'INSERT INTO gem_api_user_id (uid, connect_id, crm_user_id'
            . ') VALUES (:uid:, :connect_id:, :crm_user_id:)';



        $status = $api->modelsManager->executeQuery(

            $phql,

            [

                "uid" => $value->uid,

                "connect_id" => System::intLength($value->connect_id,11),

                "crm_user_id" => System::strLength($value->crm_user_id,50),

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


        } else {

            return $status->getModel()->id;

        }


    }


    /**
     * Builing Model Data for view
     */

    public static function buildDataApiUserId($list_keys,$data,$Controller) {

        $data_by_keys = [];

        for($i = 0; $i < count($list_keys); $i++) {

            $key_val = trim($list_keys[$i]);

            if($key_val == 'id') {

                $data_by_keys['id'] = $data->id;

            }

            if($key_val == 'uid') {

                $data_by_keys['uid'] = $data->uid;

            }

            if($key_val == 'connect_id') {

                $data_by_keys['connect_id'] = $data->connect_id;

            }


            if($key_val == 'crm_user_id') {

                $data_by_keys['crm_user_id'] = $data->crm_user_id;

            }




        }

        return $data_by_keys;

    }

}