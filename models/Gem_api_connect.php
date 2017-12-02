<?php

/**
 * Created by PhpStorm.
 * User: miller321x
 * Date: 17.04.17
 * Time: 10:36
 */
use Phalcon\Mvc\Model;

class Gem_api_connect extends Model
{

    public $id;

    public $api_url;

    public $user;

    public $pass;

    public $company_id;

    public $connect_type;

    public $custom_name;

    public $crm_id;

    public $crm_user_id;


    # new connect validation

    public static function validNewConnect($App, $data)
    {

        $exception = [];

        if(!isset($data->connect_type)) {
                $exception[0] = 'connect_type is required';
                $exception[1] = Errors::getCode('connect_type is required');

        }

        else if(!isset($data->custom_name)) {
                $exception[0] = 'custom_name is required';
                $exception[1] = Errors::getCode('custom_name is required');
        }


        else if(isset($data->custom_name)) {

            $exception = self::validName($App,$exception, $data->custom_name);

        } else {

            if($data->connect_type == 2) {
                if(!isset($data->api_url)) {
                    $exception[0] = 'api_url is required';
                    $exception[1] = Errors::getCode('api_url is required');
                }
            }
        }

        return $exception;

    }

    public static function validUpdateConnect($App, $data)
    {

        $exception = [];

        if(!isset($data->connect_type)) {
            $exception[0] = 'connect_type is required';
            $exception[1] = Errors::getCode('connect_type is required');
        }

        else if(!isset($data->custom_name)) {
            $exception[0] = 'custom_name is required';
            $exception[1] = Errors::getCode('custom_name is required');
        }


        else if(isset($data->custom_name)) {

            $exception = self::validName($App,$exception, $data->custom_name, false);

        }
        else {
            if($data->connect_type == 2) {
                if(!isset($data->api_url)) {
                    $exception[0] = 'api_url is required';
                    $exception[1] = Errors::getCode('api_url is required');
                }
            }
        }

        return $exception;

    }



    /** Properties validation */

    # Valid name

    public static function validName($App, $error_exception, $name, $unic = true)
    {


        $name = System::strSpecialClear($name);

        if(trim($name) == ''){

            $error_exception[0] = 'api_connect_name is empty';
            $error_exception[1] = Errors::getCode('api_connect_name is empty');

        } else {

            if (mb_strlen($name,'UTF-8') > 100) {

                $error_exception[0] = 'max length 100';
                $error_exception[1] = Errors::getCode('connect max length 100');

            }

            else if (!preg_match(REGULAR_VARCHAR_NAME, $name))
            {
                $error_exception[0] = 'invalid name';
                $error_exception[1] = Errors::getCode('connect invalid name');

            } else {

                if($unic) {
                    $res = self::findFirstByCustom_name($name);

                    if (isset($res->id)) {

                        $error_exception[0] = 'name yet isset';
                        $error_exception[1] = Errors::getCode('connect name yet isset');

                    }
                }

            }

        }



        return $error_exception;

    }


    /**
     * UPDATE CONNECTS
     *
     */

    # update user settings

    public static function updateConnect($value, $connect)
    {


        $connect->connect_type = $value->connect_type;
        $connect->custom_name = $value->custom_name;
        if(isset($value->crm_id)) {
            $connect->crm_id = $value->crm_id;
        }
        if(isset($value->api_url)) {
            $connect->api_url = $value->api_url;
        }

        $connect->save();

        JSON::buildJsonContent(
            'success',
            'ok'

        );

    }

    /**
     * INSERT INTO CONNECTS
     *
     */

    # add company

    public static function addNewConnect($value,$api,$App)
    {


        $url_api = '';
        if(isset($value->api_url)) {
            $url_api = $value->api_url;
        }

        if(!isset($value->company_id)) {

            $value->company_id = Companies::getDefault($App);

        }


        if(!isset($value->crm_id)) {
            $value->crm_id = 0;
        } else {
            if($value->crm_id == '') {
                $value->crm_id = 0;
            }
        }


        $crm_user_id = '';
        if(!isset($value->crm_user_id)) {
            $crm_user_id = '';
        }



        # SQL Query

        $phql = 'INSERT INTO gem_api_connect (api_url, user, pass, company_id, '
            . 'connect_type, custom_name, crm_id, crm_user_id) VALUES (:api_url:, :user:, '
            . ':pass:, :company_id:, :connect_type:, :custom_name:, :crm_id:, :crm_user_id:)';



        $status = $api->modelsManager->executeQuery(

            $phql,

            [

                "api_url" => $url_api,

                "user" => '',

                "pass" => '',

                "company_id" => $value->company_id,

                "connect_type" => $value->connect_type,

                "custom_name" => $value->custom_name,

                "crm_id" => $value->crm_id,

                "crm_user_id" => $crm_user_id,

            ]
        );


        if ($status->success() === true) {


            $frontend = [];

            $frontend['id'] = $status->getModel()->id;

            $frontend['code'] = Success::getCode('connect create');

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




    /**
     * INSERT INTO CONNECTS
     *
     */

    # add company

    public static function addNewConnectAuto($value,$api)
    {


        $phql = 'INSERT INTO Gem_api_connect (api_url, user, pass, company_id, '
            . 'connect_type, custom_name, crm_id, crm_user_id) VALUES (:api_url:, :user:, '
            . ':pass:, :company_id:, :connect_type:, :custom_name:, :crm_id:, :crm_user_id:)';



        $status = $api->modelsManager->executeQuery(

            $phql,

            [

                "api_url" => "",

                "user" => "",

                "pass" => "",

                "company_id" => $value->com_id,

                "connect_type" => $value->type,

                "custom_name" => $value->name,

                "crm_id" => 0,

                "crm_user_id" => $value->user_id,

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





    }



    /**
     * Builing Model Data for view
     */

    public static function buildDataConnects($list_keys,$data,$Controller) {

        $data_by_keys = [];

        for($i = 0; $i < count($list_keys); $i++) {

            $key_val = trim($list_keys[$i]);

            if($key_val == 'id') {

                $data_by_keys['id'] = $data->id;

            }

            if($key_val == 'data_type') {

                if($data->connect_type == 1) {

                    $res = Users::findFirstById($Controller->App->uid);

                    if (isset($res->id)) {
                        $data_by_keys['api_key'] = $res->hash . '.' . $data->id;
                    }

                } else {

                    $data_by_keys['api_url'] = $data->api_url;

                }
            }

            if($key_val == 'api_url') {

                $data_by_keys['api_url'] = $data->api_url;

            }

            if($key_val == 'api_key') {

                $res = Users::findFirstById($Controller->App->uid);

                if (isset($res->id)) {
                    $data_by_keys['api_key'] = $res->hash . '.' . $data->id;
                }


            }


            if($key_val == 'user') {

                $data_by_keys['user'] = $data->user;

            }


            if($key_val == 'pass') {

                $data_by_keys['pass'] = $data->pass;

            }


            if($key_val == 'company_id') {

                $data_by_keys['company_id'] = $data->company_id;

            }

            if($key_val == 'crm_user_id') {

                $data_by_keys['crm_user_id'] = $data->crm_user_id;

            }

            if($key_val == 'connect_type') {

                $data_by_keys['connect_type'] = $data->connect_type;

            }


            if($key_val == 'custom_name') {

                $data_by_keys['custom_name'] = $data->custom_name;

            }

            if($key_val == 'crm_id') {

                if($data->crm_id > 0) {
                    $data_by_keys['crm_id'] = $data->crm_id;
                }

            }

            if($key_val == 'name') {

                $res = Crms::findFirstById($data->crm_id);

                if (isset($res->id)) {
                    $data_by_keys['crm_name'] = $res->name;
                }


            }


        }

        return $data_by_keys;

    }


}