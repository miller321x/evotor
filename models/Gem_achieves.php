<?php

/**
 * Created by PhpStorm.
 * User: miller321x
 * Date: 17.04.17
 * Time: 10:30
 */
use Phalcon\Mvc\Model;

class Gem_achieves extends Model
{

    public $id;

    public $title;

    public $description;

    public $image;

    public $rating;

    public $coins;

    public $rank;

    public $days_limit;

    public $company_id;

    public $dep_id;

    public $date_add;

    public $formula_id;

    public $special_func;

    public $status;


    # new achive validation

    public static function validNewAchieve($App, $data)
    {

        $exception = [];

        if(!isset($data->title)) {
            $exception[0] = 'title is required';
            $exception[1] = Errors::getCode('achieve title is required');
        }

        else if(!isset($data->image) || $data->image == '') {
            $exception[0] = 'image is required';
            $exception[1] = Errors::getCode('achieve image is required');
        }

        else if(!isset($data->coins) || $data->coins == '') {
            $exception[0] = 'coins is required';
            $exception[1] = Errors::getCode('achieve coins is required');
        }


        else if($data->rating < 0) {
            $exception[0] = 'negative int';
            $exception[1] = Errors::getCode('negative int');
        }



        else {

            if(isset($data->description)) {

                if (mb_strlen($data->description,'UTF-8') > 200) {

                    $exception[0] = 'achieve length 200';
                    $exception[1] = Errors::getCode('achieve max length 200');

                }

            }


            if(isset($data->rank)) {
                if (mb_strlen($data->rank,'UTF-8') > 50) {

                    $exception[0] = 'max length 50';
                    $exception[1] = Errors::getCode('runk name max length 50');

                }
            }

            $exception = self::validName($App,$exception, $data->title);
        }

        return $exception;

    }

    public static function validUpdateAchieve($App, $data)
    {

        $exception = [];

        if(!isset($data->title)) {
            $exception[0] = 'title is required';
            $exception[1] = Errors::getCode('achieve title is required');
        }


        else if(!isset($data->coins) || $data->coins == '') {
            $exception[0] = 'coins is required';
            $exception[1] = Errors::getCode('achieve coins is required');
        }

        else if($data->coins < 0) {
            $exception[0] = 'negative int';
            $exception[1] = Errors::getCode('negative int');
        }

        else if($data->rating < 0) {
            $exception[0] = 'negative int';
            $exception[1] = Errors::getCode('negative int');
        }

        else if(!isset($data->dep_id) || $data->dep_id == '') {
            $exception[0] = 'dep_id is required';
            $exception[1] = Errors::getCode('achieve dep_id is required');
        }

        else {

            if(isset($data->description)) {

                if (mb_strlen($data->description,'UTF-8') > 200) {

                    $exception[0] = 'achieve length 200';
                    $exception[1] = Errors::getCode('achieve max length 200');

                }

            }

            if(isset($data->rank)) {
                if (mb_strlen($data->rank,'UTF-8') > 50) {

                    $exception[0] = 'max length 50';
                    $exception[1] = Errors::getCode('runk name max length 50');

                }
            }

            $exception = self::validName($App,$exception, $data->title, false);
        }


        return $exception;

    }

    /** Properties validation */

    # Valid name

    public static function validName($App, $error_exception,$name, $unic = true)
    {

        $name = System::strSpecialClear($name);


        if(trim($name) == ''){

            $error_exception[0] = 'achieve name is empty';
            $error_exception[1] = Errors::getCode('achieve name is empty');

        } else {

            if (mb_strlen($name,'UTF-8') > 50) {

                $error_exception[0] = 'max length 50';
                $error_exception[1] = Errors::getCode('achieve name max length 50');

            }




        }


        return $error_exception;

    }

    # Valid name

    public static function validRank($App, $error_exception,$name, $unic = true)
    {

        $name = System::strSpecialClear($name);


        if (mb_strlen($name,'UTF-8') > 100) {

            $error_exception[0] = 'max length 100';
            $error_exception[1] = Errors::getCode('achieve rank max length 100');

        }


        return $error_exception;

    }



    public static function getAchievesByDep($dep_id, $App) {

        $company_id = Companies::getDefault($App);

        $sql = "company_id = :company_id: AND dep_id = :dep_id: AND status = :status:";

        $bind = [
            "company_id" => $company_id,
            "dep_id" => $dep_id,
            "status" => 0,
        ];

        $achieves = self::find(
            [
                $sql,
                "bind" => $bind,

            ]
        );

        return $achieves;

    }

    public static function getAchieves($App) {

        $company_id = Companies::getDefault($App);

        $sql = "company_id = :company_id: AND dep_id = :dep_id: AND status = :status:";

        $bind = [
            "company_id" => $company_id,
            "dep_id" => 0,
            "status" => 0,
        ];

        $achieves = self::find(
            [
                $sql,
                "bind" => $bind,

            ]
        );

        return $achieves;

    }



    public static function clearRelationsDep($api,$App,$id) {

        $phql = "UPDATE Gem_achieves SET dep_id = 0 WHERE dep_id = '".$id."'";

        $api->modelsManager->executeQuery($phql);

    }



    public static function exportBasicAchieves($api,$com_id) {

        $achieves = Gem_achieves_export::find();


        if(count($achieves) > 0) {

            foreach($achieves as $achieve) {

                $phql = 'INSERT INTO gem_achieves (title, description, '
                    . '	image, 	rating, coins, rank, days_limit, company_id, dep_id, date_add, formula_id, special_func) '
                    . ' VALUES (:title:, :description:, :image:, :rating:, :coins:, :rank:, :days_limit:, :company_id:,'
                    . ':dep_id:, :date_add:, :formula_id:, :special_func:)';



                $status = $api->modelsManager->executeQuery(

                    $phql,

                    [

                        "title" => $achieve->title,

                        "description" => $achieve->description,

                        "image" => $achieve->image,

                        "rating" => $achieve->rating,

                        "coins" => $achieve->coins,

                        "rank" => $achieve->rank,

                        "days_limit" => $achieve->days_limit,

                        "company_id" => $com_id,

                        "dep_id" => 0,

                        "date_add" => System::toDay(),

                        "formula_id" => $achieve->formula_id,

                        "special_func" => $achieve->special_func,



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


        }



    }



    /**
     * INSERT INTO ACHIVE
     *
     */

    # add param

    public static function addNewAchieve($data, $api, $App) {

        if(!isset($data->company_id)) {

            $data->company_id = Companies::getDefault($App);

        }

        if(!isset($data->dep_id)) {
            $data->dep_id = 0;
        }

        if(!isset($data->image)) {
            $data->image = '';

        } else {

            if($data->image != '') {
                if(strpos($data->image,';') !== false) {
                    $data->image = System::uploadImage($data->image, $App);
                } else {
                    $path = explode('uploads',$data->image);
                    $data->image = 'uploads'.$path[1];
                }
            }

        }

        if(!isset($data->rank)) {
            $data->rank = 0;
        } else {
            if($data->rank == '') {
                $data->rank = 0;
            } else {
                $data->rank = Rungs::createNew(System::strLength(System::strSpecialClear($data->rank),50),$api,$App);
            }

        }



        if(!isset($data->rating)) {
            $data->rating = 0;
        }
        if(!isset($data->days_limit)) {
            $data->days_limit = System::toDay();
        } else {
            if($data->days_limit == '') {
                $data->days_limit = System::toDay();
            }
        }

        $formula_id = 0;

        if(isset($data->formula_id)) {
            if($data->formula_id > 0) {
                $formula_id = $data->formula_id;
            }
        }

        $special_func = 0;
        if(isset($data->special_func)) {
            if($data->special_func > 0) {
                $special_func = $data->special_func;
            }
        }

        # SQL Query

        $phql = 'INSERT INTO gem_achieves (title, description, '
            . '	image, 	rating, coins, rank, days_limit, company_id, dep_id, date_add, formula_id, special_func) '
            . ' VALUES (:title:, :description:, :image:, :rating:, :coins:, :rank:, :days_limit:, :company_id:,'
            . ':dep_id:, :date_add:, :formula_id:, :special_func:)';



        $status = $api->modelsManager->executeQuery(

            $phql,

            [

                "title" => $data->title,

                "description" => $data->description,

                "image" => $data->image,

                "rating" => System::intLength($data->rating,11),

                "coins" => System::intLength($data->coins,11),

                "rank" => $data->rank,

                "days_limit" => $data->days_limit,

                "company_id" => $data->company_id,

                "dep_id" => System::intLength($data->dep_id,11),

                "date_add" => System::toDay(),

                "formula_id" => System::intLength($formula_id,11),

                "special_func" => System::intLength($special_func,11),



            ]
        );


        if($App->response_view) {

            if ($status->success() === true) {

                $id = $status->getModel()->id;


                $frontend = [];

                $frontend['id'] = $id;

                $frontend['code'] = Success::getCode('achieve create');



                JSON::buildJsonContent(
                    $frontend,
                    'created'

                );


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



    /**
     * Builing Model Data for view
     */

    public static function buildDataAchieves($list_keys,$data,$Controller) {

        $data_by_keys = [];

        for($i = 0; $i < count($list_keys); $i++) {

            $key_val = trim($list_keys[$i]);

            if($key_val == 'id') {

                $data_by_keys['id'] = $data->id;

            }

            if($key_val == 'status') {


                $sql = "uid = :uid: AND achieve_id = :achieve_id:";

                $bind = [
                    "uid" => $Controller->uid,
                    "achieve_id" => $data->id,
                ];

                $status = Gem_user_achieves::find(
                    [
                        $sql,
                        "bind" => $bind,
                        "offset" => 0,
                        "limit" => 1,
                    ]
                );


                if(count($status) > 0) {

                    $data_by_keys['status'] = 1;

                } else {

                    $data_by_keys['status'] = 0;

                }


            }

            if($key_val == 'title') {

                $data_by_keys['title'] = $data->title;

            }

            if($key_val == 'description') {

                $data_by_keys['description'] = $data->description;

            }

            if($key_val == 'formula_id') {

                $data_by_keys['formula_id'] = $data->formula_id;

            }

            if($key_val == 'image') {

                $data_by_keys['image'] = System::getImageUrl($data->image);

            }

            if($key_val == 'coins') {

                $data_by_keys['coins'] = $data->coins;

            }

            if($key_val == 'company_id') {

                $data_by_keys['company_id'] = $data->company_id;

            }

            if($key_val == 'dep_id') {

                $data_by_keys['dep_id'] = $data->dep_id;

            }


            if($key_val == 'rating') {

                $data_by_keys['rating'] = $data->rating;

            }

            if($key_val == 'rank') {

                $data_by_keys['rank'] = '';

                if($data->rank > 0) {

                    $rung = Rungs::findFirstById($data->rank);

                    if(isset($rung->title)) {

                        $data_by_keys['rank'] = $rung->title;

                    }


                }


            }

            if($key_val == 'days_limit') {

                $data_by_keys['days_limit'] = $data->days_limit;

            }



        }

        return $data_by_keys;

    }




}