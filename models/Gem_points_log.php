<?php

/**
 * Created by PhpStorm.
 * User: miller321x
 * Date: 19.05.17
 * Time: 12:28
 */
use Phalcon\Mvc\Model;

class Gem_points_log extends Model
{

    public $id;

    public $uid;

    public $status;

    public $points;

    public $point_rules_id;

    public $date_add;






    /**
     * SELECT POINTS
     *
     */

    public static function getPointsLog($api, $App, $params) {



        $date = new DateTime();
        $now = $date->getTimestamp();

        $time = $now - $params->time;

        # SQL Query

        $phql = "SELECT * " .
            "FROM Gem_points_log WHERE uid = :uid: AND point_rules_id = :point_rules_id: AND status = 0  AND date_add <= :date_add:";




        $rows = $api->modelsManager->executeQuery(

            $phql,

            [

                "uid" => $params->uid,
                "point_rules_id" => $params->id,
                "date_add" => $time,

            ]
        );

        return $rows;


    }


    /**
     * INSERT INTO POINTS
     *
     */

    # add point

    public static function addNewPointLog($data, $api) {



        $date = new DateTime();
        $now = $date->getTimestamp();

        # SQL Query

        $phql = 'INSERT INTO gem_points_log (uid, status, '
            . '	points, point_rules_id, date_add) '
            . ' VALUES (:uid:, :status:, :points:,'
            . ':point_rules_id:, :date_add:)';



        $api->modelsManager->executeQuery(

            $phql,

            [

                "uid" => $data->uid,

                "status" => 0,

                "points" => System::intLength($data->points,11),

                "point_rules_id" => System::intLength($data->point_id,11),

                "date_add" => $now,


            ]
        );




    }



}