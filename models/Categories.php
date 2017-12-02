<?php

/**
 * Created by PhpStorm.
 * User: miller321x
 * Date: 26.07.17
 * Time: 14:57
 */
use Phalcon\Mvc\Model;

class Categories extends Model
{

    public $id;

    public $company_id;

    public $title_ru_ru;

    public $description_ru_ru;

    public $status;

    public $position;

    public $parent_id;

    public $type_category;

    public $url;

    public $permission;

    public $component;

    public $class;

    public $icon;

    public $css_class;


    public static function select($api,$sql,$bind) {

        $res = $api->modelsManager->executeQuery(

            $sql,

            $bind
        );

        return $res;

    }

    /**
     * INSERT INTO CATEGORIES
     *
     */



    public static function addNewCategory($value,$api)
    {





        # SQL Query

        $phql = 'INSERT INTO Categories (company_id, title_ru_ru, description_ru_ru, status, position, parent_id, type_category, url, permission, component, class, icon, css_class, methods, view)' .
            ' VALUES (:company_id:, :title_ru_ru:, :description_ru_ru:, :status:, :position:, :parent_id:, :type_category:, :url:, :permission:, :component:, :class:, :icon:, :css_class:, :methods:, :view:)';

        $status = $api->modelsManager->executeQuery(

            $phql,

            [

                "company_id" => $value->company_id,

                "title_ru_ru" => $value->title,

                "description_ru_ru" => $value->description,

                "status" => $value->status,

                "position" => $value->position,

                "parent_id" => $value->parent_id,

                "type_category" => $value->type_category,

                "url" => $value->url,

                "permission" => $value->permission,

                "component" => $value->component,

                "class" => $value->class,

                "icon" => $value->icon,

                "css_class" => $value->css_class,

                "methods" => $value->methods,

                "view" => $value->view,



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