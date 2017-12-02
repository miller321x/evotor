<?php

/**
 * Created by PhpStorm.
 * User: miller321x
 * Date: 17.04.17
 * Time: 15:20
 */

use Phalcon\Mvc\Controller;

class CreateCompanyController extends Controller
{


    public function createCompanyAction($App,$api)
    {
        if($App->accessPermission('createCompanyAction')) {

            $raw = JSON::get();

                $error_exception = Companies::validNewCompany($App, $raw);


                if (count($error_exception) > 0) {

                    JSON::buildJsonContent(
                        $error_exception,
                        'error'

                    );

                } else {

                    Companies::addNewCompany($raw, $api, $App);


                }



        }
    }

}