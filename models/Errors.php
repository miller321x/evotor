<?php

/**
 * Created by PhpStorm.
 * User: miller321x
 * Date: 23.05.17
 * Time: 16:33
 */
use Phalcon\Mvc\Model;

class Errors extends Model
{

    public static function getCode($name) {

        $dataErrors = [

            "company_name is required" => 41,

            "company isset yet" => 42,

            "ERROR_COMPANY_NAME_MAX" => 43,

            "ERROR_COMPANY_NAME_INVALID" => 44,

            "ERROR_COMPANY_ISSET" => 45,

            "dep_name is required" => 51,

            "company undefined" => 52,

            "ERROR_DEP_NAME_MAX" => 53,

            "ERROR_DEP_NAME_INVALID" => 54,

            "dep_name is empty" => 55,

            "dep name isset yet" => 56,





        ];


        if(isset($dataErrors[$name])) {

            return $dataErrors[$name];

        } else {

            return 0;

        }


    }

}