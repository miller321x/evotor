<?php

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 02.04.17
 * Time: 4:08
 */

use Phalcon\Mvc\Model;
use Phalcon\Http\Request;


class Auth extends Model
{
    public $id;

    public $uid;

    public $session_id;

    public $user_agent;

    public $access_token;

    public $user_ip;

    public $date_auth;


    /**
     * INSERT INTO AUTH
     *
     */

    # add user

    public static function createAuth($user,$api,$mode, $App = null)
    {
        # Init System Vars


        $request = new Request();

        $session_id =  System::genSession();

        $access_token = System::genHash();

        $user_ip =  $_SERVER["REMOTE_ADDR"];

        $user_id = $user->id;

        $date_auth = System::toDay();


        # SQL Query

        $phql = 'INSERT INTO auth (uid, session_id, user_agent, access_token, '
            . 'user_ip, date_auth) VALUES (:uid:, :session_id:, '
            . ':user_agent:, :access_token:, :user_ip:, :date_auth:)';



        $status = $api->modelsManager->executeQuery(

            $phql,

            [

                "uid" => $user_id,

                "session_id" => $session_id,

                "user_agent" => $_SERVER['HTTP_USER_AGENT'],

                "access_token" => $access_token,

                "user_ip" => $user_ip,

                "date_auth" => $date_auth,

            ]
        );


        if ($status->success() === true) {



            $frontend = [];

            if($mode == 'sid') {

                $frontend['user_id'] = $user_id;

                $frontend['session_id'] = $session_id;

                $frontend['user_ip'] = $user_ip;

                $frontend['date_auth'] = $date_auth;



                $api->cookies->set(
                    "SID",
                    $session_id,
                    time() + 15 * 86400
                );




            } else {

                if($App != null and $App->evotor_user != null) {

                    $frontend['userId'] = $App->evotor_user;

                    $frontend['hasBilling'] = false;

                    $frontend['token'] = $access_token;


                    $res = JSON::encode($frontend);

                    echo $res;

                    exit();


                } else {

                    $frontend['user_id'] = $user_id;

                    $frontend['access_token'] = $access_token;

                    $frontend['user_ip'] = $user_ip;

                    $frontend['date_auth'] = $date_auth;

                }

            }



            if($mode != 'auto') {

                JSON::buildJsonContent(
                    $frontend,
                    'ok'

                );

            } else {

                header('Location: /redirect?token=' . $access_token );
            }



        } else {

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