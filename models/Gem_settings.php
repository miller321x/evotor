<?php

/**
 * Created by PhpStorm.
 * User: miller321x
 * Date: 17.04.17
 * Time: 10:55
 */
use Phalcon\Mvc\Model;

class Gem_settings extends Model
{

    public $id;

    public $owner_id;

    public $rating_live;

    public $coins_live;

    public $last_update;

    public $game_template;

    public $timezone;

    public $update_season;

    public $coins;

    public $points;

    public $levels;

    public $exchange_rates;

    public $date_end;

    public $days_end;

    public $status_clear;

    public $balance_free_interval;

    public $balance_free_update;

    public $balance_free_status;


    public static function validUpdateSettings($App, $data)
    {

        $exception = [];


        return $exception;

    }



    public static function get($company) {

        $company = Companies::findFirstById($company);

        return Gem_settings::findFirstByOwner_id($company->owner_id);

    }

    /**
     * INSERT INTO SETTINGS
     *
     */

    # add param

    public static function addNewSettings($user_id, $api, $timezone = null) {

        $timezone_select = 1;
        if($timezone) {
            $timezone_select = $timezone;
        }


        $phql = 'INSERT INTO gem_settings (owner_id, rating_live, '
            . '	coins_live, last_update, game_template, timezone, update_season, coins, points, levels, exchange_rates, date_end, days_end, status_clear, balance_free_interval, balance_free_update) '
            . ' VALUES (:owner_id:, :rating_live:, :coins_live:, :last_update:, :game_template:, :timezone:, :update_season:, :coins:, :points:, :levels:, :exchange_rates:, :date_end:, :days_end:, :status_clear:, :balance_free_interval:, :balance_free_update:'
            . ')';



        $status = $api->modelsManager->executeQuery(

            $phql,

            [

                "owner_id" => $user_id,

                "rating_live" => 0,

                "coins_live" => 0,

                "last_update" => System::toDay(),

                "game_template" => 1,

                "timezone" => $timezone_select,

                "update_season" => 0,

                "coins" => 0,

                "points" => 0,

                "levels" => "",

                "exchange_rates" => 1,

                "date_end" => System::toDay(),

                "days_end" => 30,

                "status_clear" => 0,

                "balance_free_interval" => 1,

                "balance_free_update" => System::toDay('timestamp'),



            ]
        );


        if ($status->success() !== true) {

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






    /**
     * Builing Model Data for view
     */

    public static function buildDataSettings($list_keys,$data,$Controller) {

        $data_by_keys = [];

        for($i = 0; $i < count($list_keys); $i++) {

            $key_val = trim($list_keys[$i]);

            if($key_val == 'id') {

                $data_by_keys['id'] = $data->id;

            }

            if($key_val == 'owner_id') {

                $data_by_keys['owner_id'] = $data->owner_id;

            }

            if($key_val == 'rating_live') {

                $data_by_keys['rating_live'] = $data->rating_live;

            }

            if($key_val == 'coins_live') {

                $data_by_keys['coins_live'] = $data->coins_live;

            }

            if($key_val == 'last_update') {

                $data_by_keys['last_update'] = $data->last_update;

            }

            if($key_val == 'game_template') {

                $data_by_keys['game_template'] = $data->game_template;

            }

            if($key_val == 'timezone') {

                $data_by_keys['timezone'] = $data->timezone;

            }

            if($key_val == 'update_season') {

                $data_by_keys['update_season'] = $data->update_season;

            }

            if($key_val == 'coins') {

                $data_by_keys['coins'] = $data->coins;

            }

            if($key_val == 'points') {

                $data_by_keys['points'] = $data->points;

            }

            if($key_val == 'levels') {

                if($data->levels != '') {

                    $data_by_keys['levels'] = JSON::decode($data->levels);

                } else {

                    $data_by_keys['levels'] = '';

                }


            }

            if($key_val == 'exchange_rates') {

                $data_by_keys['exchange_rates'] = $data->exchange_rates;

            }

            if($key_val == 'date_end') {

                $data_by_keys['date_end'] = $data->date_end;

            }

        }

        return $data_by_keys;

    }



}