<?php

/**
 * Created by PhpStorm.
 * User: miller321x
 * Date: 03.05.17
 * Time: 12:03
 */
use Phalcon\Mvc\Controller;

class CreateAchieveController extends Controller
{

    /**
     * @param $App
     * @param $api
     *
     */
    public function createAchieveAction($App,$api)
    {
        if($App->accessPermission('createAchieveAction')) {

            $raw = JSON::get();

            $error_exception = Gem_achieves::validNewAchieve($App, $raw);

            if (count($error_exception) > 0) {

                JSON::buildJsonContent(
                    $error_exception,
                    'error'

                );

            } else {

                Gem_achieves::addNewAchieve($raw, $api, $App);


            }

        }
    }


}