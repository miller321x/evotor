<?php

/**
 * Created by PhpStorm.
 * User: miller321x
 * Date: 02.05.17
 * Time: 11:45
 */
use Phalcon\Mvc\Controller;

class CreateConnectController extends Controller
{

    public function createConnectAction($App,$api)
    {
        if($App->accessPermission('createConnectAction')) {

            $raw = JSON::get();

            $error_exception = Gem_api_connect::validNewConnect($App, $raw);

            if (count($error_exception) > 0) {

                JSON::buildJsonContent(
                    $error_exception,
                    'error'

                );

            } else {

                    if(isset($raw->id)) {

                        $connect = Gem_api_connect::findFirstById($raw->id);
                        Gem_api_connect::updateConnect($raw,$connect);

                    } else {


                        $connect_id = Gem_api_connect::addNewConnect($raw, $api, $App);

                        if(isset($raw->profession_name)) {

                            if(isset($raw->crm_id)) {
                                $options = [
                                    "profession_name" => $raw->profession_name,
                                    "crm_id" => $raw->crm_id,
                                    "connect_id" => $connect_id,
                                ];

                                Gem_params_lib::importParams($options, $api, $App);
                            }

                        }



                    }




                JSON::buildJsonContent(
                    'done',
                    'created'

                );




            }



        }
    }
}