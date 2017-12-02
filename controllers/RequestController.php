<?php

/**
 * Created by PhpStorm.
 * User: miller321x
 * Date: 15.06.17
 * Time: 16:25
 */
use Phalcon\Mvc\Controller;

class RequestController extends Controller
{


    public $micro_interval = 60;

    public $total_count = 100;

    public $blocked_time = 360;





    public function register($App,$api) {

        /*

        $ip = self::getIp();

        if($App->uid) {

            $request = Requests::findFirstByUid($App->uid);

        } else {


            $request = Requests::findFirstByIp($ip);
        }

        if(!isset($request->id)) {

            Requests::newRequest($App,$api,$ip);

        } else {

            $date = new DateTime();

            $date = $date->getTimestamp();


            if($request->status == 1) {



                $offset =  $this->blocked_time - ($date - $request->micro_interval);

                if($offset > 0) {

                    JSON::buildJsonContent(
                        'access blocked on '.$offset.' seconds',
                        'error'

                    );

                } else {

                    $request->micro_interval = $date;

                    $request->micro_interval_count = 0;

                    $request->status = 0;

                    $request->save();

                }



            } else {

                $offset = $date - $request->micro_interval;

                if($offset < $this->micro_interval) {

                    if(($request->micro_interval_count + 1) >= $this->total_count) {

                        $request->status = 1;

                        $request->save();

                        JSON::buildJsonContent(
                            'access blocked on '.$this->blocked_time.' seconds',
                            'error'

                        );

                    } else {

                        $request->micro_interval_count = $request->micro_interval_count + 1;

                        $request->save();

                    }

                } else {

                    $request->micro_interval = $date;

                    $request->micro_interval_count = 0;

                    $request->save();

                }

            }


        }
        */

    }




    public static function insertLimit($App,$api) {




    }




    /**
     * Метод получения текущего ip-адреса из переменных сервера.
     */
    private static function getIp() {

        // ip-адрес по умолчанию
        $ip_address = '127.0.0.1';

        // Массив возможных ip-адресов
        $addrs = array();

        // Сбор данных возможных ip-адресов
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // Проверяется массив ip-клиента установленных прозрачными прокси-серверами
            foreach (array_reverse(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])) as $value) {
                $value = trim($value);
                // Собирается ip-клиента
                if (preg_match('#^\d{1,3}.\d{1,3}.\d{1,3}.\d{1,3}$#', $value)) {
                    $addrs[] = $value;
                }
            }
        }
        // Собирается ip-клиента
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $addrs[] = $_SERVER['HTTP_CLIENT_IP'];
        }
        // Собирается ip-клиента
        if (isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) {
            $addrs[] = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
        }
        // Собирается ip-клиента
        if (isset($_SERVER['HTTP_PROXY_USER'])) {
            $addrs[] = $_SERVER['HTTP_PROXY_USER'];
        }
        // Собирается ip-клиента
        if (isset($_SERVER['REMOTE_ADDR'])) {
            $addrs[] = $_SERVER['REMOTE_ADDR'];
        }

        // Фильтрация возможных ip-адресов, для выявление нужного
        foreach ($addrs as $value) {
            // Выбирается ip-клиента
            if (preg_match('#^(\d{1,3}).(\d{1,3}).(\d{1,3}).(\d{1,3})$#', $value, $matches)) {
                $value = $matches[1] . '.' . $matches[2] . '.' . $matches[3] . '.' . $matches[4];
                if ('...' != $value) {
                    $ip_address = $value;
                    break;
                }
            }
        }

        // Возврат полученного ip-адреса
        return $ip_address;
    }

}