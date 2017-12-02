<?php

/**
 * Created by PhpStorm.
 * User: miller321x
 * Date: 23.05.17
 * Time: 16:33
 */
use Phalcon\Mvc\Model;

class Success extends Model
{

    public static function getCode($name, $type = false) {

        $dataSuccess = [

            "company create" => 1001,

            "department create" => 1002,

            "achieve create" => 1003,

            "connect create" => 1004,

            "formula create" => 1005,

            "param create" => 1006,

            "point create" => 1007,

            "task create" => 1008,

            "invite create" => 1009,

            "product create" => 1010,

            "team create" => 1011,

            "user create" => 1012,

            "order create" => 1013,

            "advert create" => 1014,

            "message create" => 1015,

            "email create" => 1016,

            "coins sent" => 1017,

            "forgot pass create" => 1018,

            "check formula" => 1019,

            "vacancy create" => 1020,

            "doc create" => 1022,

            "media files create" => 1021,

            "reinvite create" => 1023,

            "role create" => 1024,


            "achieve update" => 2001,

            "company update" => 2002,

            "connect update" => 2003,

            "department update" => 2004,

            "team update" => 2005,

            "formula update" => 2006,

            "param update" => 2007,

            "point update" => 2008,

            "product update" => 2009,

            "settings update" => 2010,

            "task update" => 2011,

            "player update" => 2012,

            "user update" => 2013,

            "order update" => 2014,

            "writing update" => 2015,

            "player photo update" => 2016,

            "ui update" => 2017,

            "read transactions" => 2017,

            "rung update" => 2018,

            "clear done" => 2019,

            "media update" => 2020,

            "media cover update" => 2021,

            "vacancy update" => 2022,

            "contacts update" => 2022,

            "advert update" => 2023,

            "article update" => 2024,

            "doc update" => 2025,

            "doc position update" => 2026,

            "player balance update" => 2027,

            "text_status update" => 2028,

            "order group update" => 2029,

            "role update" => 2030,

            "order approve update" => 2031,

            "order remove update" => 2032,

            "balance add" => 2033,

            "balance remove" => 2034,

            "task remove user update" => 2035,

            "task add user update" => 2036,

            "task done user update" => 2037,

            "event update" => 2038,

            "order group delete update" => 2039,


            "achieve delete" => 3001,

            "connect delete" => 3002,

            "department delete" => 3003,

            "team delete" => 3004,

            "formula delete" => 3005,

            "param delete" => 3006,

            "point delete" => 3007,

            "product delete" => 3008,

            "task delete" => 3010,

            "player delete" => 3011,

            "user delete" => 3012,

            "album delete" => 3013,

            "vacancy delete" => 3014,

            "advert delete" => 3015,

            "article delete" => 3016,

            "doc delete" => 3017,

            "file delete" => 3018,

            "role delete" => 3019,

            "invite delete" => 3020,

            "event delete" => 3021,

            "attach delete" => 3022,


            "form send" => 4000,

            "uploaded" => 5000,

            "mail doc create" => 5001,

            "set pass" => 6000,

            "installed evator create" => 7000,

            "registred evator client create" => 7001,






        ];


        if(isset($dataSuccess[$name])) {

            if($type) {

                $res = [

                    "message" => $type,

                    "code" => $dataSuccess[$name]

                ];

                return $res;

            } else {

                return $dataSuccess[$name];

            }


        } else {

            return 0;

        }


    }

}