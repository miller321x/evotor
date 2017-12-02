<?php

/**
 * Created by PhpStorm.
 * User: miller321x
 * Date: 25.07.17
 * Time: 13:35
 */
use Phalcon\Mvc\Model;

class Configurate extends Model
{

    public $id;

    public $component;

    public $method;

    public $settings;

    public $status;

    public $permission;

    public $position;


    public static function getModule($module) {

        $sql = "method = :method:";

        $bind = [

            "method" => $module
        ];

        $config = self::find(
            [
                $sql,
                "bind" => $bind,

            ]
        );

        $settings = '';
        foreach($config as $data) {
            $settings = $data->settings;
        }

        $settings = JSON::decode($settings);

        return $settings;

    }


    /**
     * INSERT INTO CONFIGURATE
     *
     */



    public static function addNewOption($value,$api)
    {





        # SQL Query

        $phql = 'INSERT INTO Configurate (component, method, settings, status, permission, position, company_id)' .
            ' VALUES (:component:, :method:, :settings:, :status:, :permission:, :position:, :company_id:)';

        $status = $api->modelsManager->executeQuery(

            $phql,

            [

                "component" => $value->component,

                "method" => $value->method,

                "settings" => $value->settings,

                "status" => $value->status,

                "permission" => $value->permission,

                "position" => $value->position,

                "company_id" => $value->company_id,



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



}