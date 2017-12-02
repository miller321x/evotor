<?php

/**
 * Created by PhpStorm.
 * User: miller321x
 * Date: 04.05.17
 * Time: 11:56
 */
use Phalcon\Mvc\Controller;

class UpdateConnectController extends Controller
{




    public function UpdateConnectUserId($App)
    {

        $raw = JSON::get();

        if (isset($raw->id)) {

            if ($App->accessPermission('updateConnectAction', $raw->id)) {

                $connect = Gem_api_connect::findFirstById($raw->id);

                if (isset($connect->id)) {



                        $connect->crm_user_id = System::strLength(System::strSpecialClear($raw->crm_user_id),50);

                        $connect->save();

                        JSON::buildJsonContent(
                            Success::getCode('connect update','update'),
                            'ok'

                        );




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

    public function updateConnectAction($App)
    {

        $raw = JSON::get();

        if (isset($raw->id)) {

            if ($App->accessPermission('updateConnectAction', $raw->id)) {

                $connect = Gem_api_connect::findFirstById($raw->id);

                if (isset($connect->id)) {

                    $error_exception = Gem_api_connect::validUpdateConnect($App, $raw);

                    if (count($error_exception) > 0) {
                        JSON::buildJsonContent(
                            $error_exception,
                            'error'

                        );

                    } else {


                        if(isset($raw->api_url)) {
                            if($raw->api_url != '') {
                                $connect->api_url = System::strSpecialClear($raw->api_url);
                            }
                        }
                        if(isset($raw->crm_id)) {
                            if($raw->crm_id != '') {
                                $connect->crm_id = intval(System::strSpecialClear($raw->crm_id));
                            }
                        }
                        $connect->connect_type = intval(System::strSpecialClear($raw->connect_type));
                        $connect->custom_name = System::strLength(System::strSpecialClear($raw->custom_name),100);
                        $connect->crm_user_id = System::strLength(System::strSpecialClear($raw->crm_user_id),50);


                        $connect->save();

                        JSON::buildJsonContent(
                            Success::getCode('connect update','update'),
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