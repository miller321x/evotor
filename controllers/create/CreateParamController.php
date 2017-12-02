<?php

/**
 * Created by PhpStorm.
 * User: miller321x
 * Date: 03.05.17
 * Time: 12:03
 */
use Phalcon\Mvc\Controller;

class CreateParamController extends Controller
{

    public function createParamAction($App,$api)
    {
        if($App->accessPermission('createParamAction')) {

            $raw = JSON::get();

            $error_exception = Gem_params::validNewParam($App, $raw);

            if (count($error_exception) > 0) {

                JSON::buildJsonContent(
                    $error_exception,
                    'error'

                );

            } else {

                Gem_params::addNewParam($raw, $api, $App);


            }

        }
    }


}