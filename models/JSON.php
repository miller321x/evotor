<?php

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 31.03.17
 * Time: 8:40
 */
use Phalcon\Mvc\Model;
use Phalcon\Http\Response;


class JSON extends Model
{


    private static function modelRouter($Controller,$list_keys,$data) {

        switch ($Controller->model) {

            case 'Users':

                return Users::buildDataUsers($list_keys,$data,$Controller);

                break;

            case 'Companies':

                return Companies::buildDataCompanies($list_keys,$data,$Controller);

                break;




        }

    }

    public static function decode($json) {

        if(json_decode($json)) {

            return json_decode($json, false, 512, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        } else {

            if($json == '') {

                return [];

            } else {

                self::buildJsonContent(
                    'incorrect JSON array'.$json,
                    'error'

                );
            }

        }

    }

    public static function encode($json) {

        return json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    }

    public static function get($api = null) {



        $data = json_decode(json_encode($_REQUEST,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        if(isset($data->data)) {

            return $data->data;

        } else {

            if(count($data) > 0) {

                return $data;

            } else {

                if($api) {

                    $data = JSON::object($api->request->getJsonRawBody(true));

                    return $data;

                }

            }

        }

        return null;


    }

    public static function object($arr) {

        $data = json_decode(json_encode($arr));

        return $data;

    }


    public static function buildJsonContentConstructor($res = null,$Controller = null, $type = 'list') {

        $result_data = [];

            $list_keys = explode(':', $res);

            $i = 0;

            if($type == 'list') {

                foreach ($Controller->data as $val) {

                    $data_by_keys = self::modelRouter($Controller,$list_keys,$val);

                    $result_data[$i] = $data_by_keys;

                    $i++;
                }

                return $result_data;

            } else {

                return self::modelRouter($Controller,$list_keys,$Controller->data);

            }



    }


    public static function buildJsonContent($res = null,$type = 'ok',$Controller = null) {

        $response = new Response();

        $result = [];

        switch ($type) {

            case 'list':

                $result_data = [];

                $list_keys = explode(':', $res);

                $i = 0;

                $order = 'DESC';

                if(isset($Controller->order)) {

                    $order = $Controller->order;
                }

                if($order == 'DESC') {

                    foreach ($Controller->data as $val) {

                        $data_by_keys = self::modelRouter($Controller,$list_keys,$val);

                        $result_data[$i] = $data_by_keys;

                        $i++;
                    }

                } else {

                    $n = count($Controller->data) - 1;

                    foreach ($Controller->data as $val) {

                        $data_by_keys = self::modelRouter($Controller,$list_keys,$val);

                        $result_data[$n] = $data_by_keys;

                        $n = $n - 1;
                    }
                }


                $response->setStatusCode(200, "success");

                $result = [

                    "response" => $result_data

                ];



                break;

            case 'item':

                $list_keys = explode(':', $res);

                $data_by_keys = self::modelRouter($Controller,$list_keys,$Controller->data);

                $response->setStatusCode(200, "success");

                $result = [

                    "response" => $data_by_keys

                ];


                break;

            case 'created':

                $response->setStatusCode(201, "Created");

                $result = [

                    "response" => $res

                ];


                break;

            case 'ok':

                $response->setStatusCode(200, "success");

                $result = [

                    "response" => $res

                ];


                break;


            case 'error':

                if(count($res) > 1) {

                    $result = [

                        "error"   => ["message" => $res[0], "code" => $res[1]],

                    ];

                } else {

                    $result = [

                        "error"   => ["message" => $res],

                    ];
                }


                break;

            case 'access_error':



                    $result = [

                        "access_error"   => ["message" => $res[0], "code" => $res[1]],

                    ];




                break;

        }

        $response->setContentType('application/json', 'UTF-8');

        $response->setContent(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        $response->send();

        exit();




    }

}