<?php

/**
 * Created by PhpStorm.
 * User: miller321x
 * Date: 05.05.17
 * Time: 10:46
 */
use Phalcon\Mvc\Controller;

class UpdateDepTeamController extends Controller
{

    public function updateDepartmentAction($App)
    {

        $raw = JSON::get();

        if (isset($raw->id)) {

            if ($App->accessPermission('updateDepartmentAction', $raw->id)) {

                $dep = Departments::findFirstById($raw->id);

                if (isset($dep->id)) {

                    $error_exception = Departments::validUpdateDepartment($App, $raw);

                    if (count($error_exception) > 0) {
                        JSON::buildJsonContent(
                            $error_exception,
                            'error'

                        );

                    } else {


                        if(isset($raw->dep_image)){

                            if($raw->dep_image != '') {
                                $raw->dep_image = System::uploadImage($raw->dep_image,$App);
                                $dep->dep_image = System::strSpecialClear($raw->dep_image);
                            }

                        }



                        $dep->dep_name = System::strLength(System::strSpecialClear($raw->dep_name),100);



                        $dep->save();

                        JSON::buildJsonContent(
                            Success::getCode('department update','update'),
                            'ok'

                        );


                    }

                } else {
                    JSON::buildJsonContent(
                        'not-found'

                    );
                }

            }

        } else {
            JSON::buildJsonContent(
                'id is required',
                'error'

            );
        }


    }


    public function updateTeamAction($App,$api)
    {

        $raw = JSON::get();

        if (isset($raw->id)) {




            if ($App->accessPermission('updateTeamAction', $raw->id)) {

                $team = Teams::findFirstById($raw->id);

                if (isset($team->id)) {

                    $error_exception = Teams::validUpdateTeam($App, $raw);

                    if (count($error_exception) > 0) {
                        JSON::buildJsonContent(
                            $error_exception,
                            'error'

                        );

                    } else {


                        if(isset($raw->team_image)){

                            if($raw->team_image != '') {
                                $raw->team_image = System::uploadImage($raw->team_image,$App);
                                $team->team_image = System::strSpecialClear($raw->team_image);
                            }

                        }



                        $team->team_name = System::strLength(System::strSpecialClear($raw->team_name),100);

                        if(isset($raw->dep_id)){

                            if($raw->dep_id != '') {

                                $team->dep_id = $raw->dep_id;

                                $bind = [
                                    "team_id" => $team->id,
                                    "dep_id" => $raw->dep_id
                                ];

                                Users::updateRelationsDep($api,$bind);

                            }

                        }



                        $team->save();

                        JSON::buildJsonContent(
                            Success::getCode('team update','update'),
                            'ok'

                        );


                    }

                } else {
                    JSON::buildJsonContent(
                        'not-found'

                    );
                }

            }

        } else {
            JSON::buildJsonContent(
                'id is required',
                'error'

            );
        }


    }

}