<?php

/**
 * Created by PhpStorm.
 * User: miller321x
 * Date: 05.05.17
 * Time: 10:35
 */
use Phalcon\Mvc\Controller;

class UpdateCompanyController extends Controller
{

    public function updateCompanyAction($App,$api)
    {

        $raw = JSON::get();

        if (!isset($raw->id) or $raw->id == '') {

            if($App->accessPermission('createCompanyAction')) {



                $error_exception = Companies::validNewCompany($App, $raw);


                if (count($error_exception) > 0) {

                    JSON::buildJsonContent(
                        $error_exception,
                        'error'

                    );

                } else {

                    Companies::addNewCompany($raw, $api, $App);


                }



            }

        } else {

            if ($App->accessPermission('updateCompanyAction')) {

                $company = Companies::findFirstById($raw->id);

                if (isset($company->id)) {

                    $error_exception = Companies::validUpdateCompany($App, $raw);

                    if (count($error_exception) > 0) {
                        JSON::buildJsonContent(
                            $error_exception,
                            'error'

                        );

                    } else {


                        $image = '';
                        if(isset($raw->company_image)){

                            if($raw->company_image != '') {
                                $raw->company_image = System::uploadImage($raw->company_image,$App);
                                $company->company_image = $raw->company_image;
                                $image = $raw->company_image;
                            }

                        }

                        if(isset($raw->timezone)) {

                            $sett = Gem_settings::findFirstByOwner_id($App->uid);

                            if(isset($sett->id)) {

                                $sett->timezone = $raw->timezone;

                                $sett->save();

                            }

                        }




                        $company->company_name = System::strLength(System::strSpecialClear($raw->company_name),255);

                        $company->email = $raw->email;

                        $company->save();



                        if($image != '') {

                            $res = [];

                            $res['url'] = System::getImageUrl($image);

                            $res['code'] = Success::getCode('company update');

                            JSON::buildJsonContent(
                                $res,
                                'ok'

                            );

                        } else {

                            JSON::buildJsonContent(
                                Success::getCode('company update','update'),
                                'ok'

                            );

                        }



                    }

                } else {
                    JSON::buildJsonContent(
                        'not-found'

                    );
                }

            }

        }


    }

}