<?php

/**
 * Created by PhpStorm.
 * User: miller321x
 * Date: 05.05.17
 * Time: 10:35
 */
use Phalcon\Mvc\Controller;

class UpdateAchieveController extends Controller
{

    public function updateAchieveAction($App,$api)
    {

        $raw = JSON::get();

        if (isset($raw->id)) {

            if ($App->accessPermission('updateAchieveAction', $raw->id)) {

                $achieve = Gem_achieves::findFirstById($raw->id);



                if (isset($achieve->id)) {

                    $error_exception = Gem_achieves::validUpdateAchieve($App, $raw);

                    if (count($error_exception) > 0) {
                        JSON::buildJsonContent(
                            $error_exception,
                            'error'

                        );

                    } else {


                        if(isset($raw->image)){

                            if($raw->image != '') {



                                    if(strpos($raw->image,';') !== false) {
                                        $raw->image = System::uploadImage($raw->image,$App);

                                    }
                                    $path = explode('uploads',$raw->image);
                                    $achieve->image = 'uploads'.$path[1];



                            }

                        }

                        if(isset($raw->dep_id)){
                            $achieve->dep_id = System::intLength($raw->dep_id,11);
                        }

                        $achieve->title = System::strLength(System::strSpecialClear($raw->title),100);
                        $achieve->description = System::strSpecialClear($raw->description);
                        $achieve->coins = System::intLength(System::strSpecialClear($raw->coins),11);

                        $achieve->rating = System::intLength(System::strSpecialClear($raw->rating),11);

                        if(!isset($raw->rank)) {
                            $rank = 0;
                        } else {
                            if($raw->rank == '') {
                                $rank = 0;
                            } else {

                                $rank = Rungs::createNew(System::strLength(System::strSpecialClear($raw->rank),50),$api,$App);
                            }

                        }

                        $achieve->rank = $rank;

                        $achieve->days_limit = $raw->days_limit;


                        $achieve->save();



                        JSON::buildJsonContent(

                            Success::getCode('achieve update','update'),
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