<?php

/**
 * Created by PhpStorm.
 * User: miller321x
 * Date: 18.04.17
 * Time: 14:22
 */
use Phalcon\Mvc\Controller;

class CreateDepTeamController extends Controller
{


    public function createDepartmentAction($App,$api)
    {
        if($App->accessPermission('createDepartmentAction')) {


                $raw = JSON::get();

                $error_exception = Departments::validNewDepartment($App, $raw);


                if (count($error_exception) > 0) {

                    JSON::buildJsonContent(
                        $error_exception,
                        'error'

                    );

                } else {

                    Departments::addNewDepartment($raw, $api, $App);


                }

        }
    }

    public function createTeamAction($App,$api)
    {
        if($App->accessPermission('createTeamAction')) {

            $raw = JSON::get();

                $error_exception = Teams::validNewTeam($App, $raw);


                if (count($error_exception) > 0) {

                    JSON::buildJsonContent(
                        $error_exception,
                        'error'

                    );

                } else {

                    Teams::addNewTeam($raw, $api, $App);


                }



        }
    }


}