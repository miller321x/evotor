<?php

/**
 * Created by PhpStorm.
 * User: miller321x
 * Date: 03.05.17
 * Time: 16:13
 */
use Phalcon\Mvc\Controller;

class UpdateParamController extends Controller
{

    public function updateParamAction($App)
    {

        $raw = JSON::get();

        if (isset($raw->id)) {

            if ($App->accessPermission('updateParamAction', $raw->id)) {

                $params = Gem_params::findFirstById($raw->id);

                if (isset($params->id)) {

                    $error_exception = Gem_params::validUpdateParam($App, $raw);

                    if (count($error_exception) > 0) {
                        JSON::buildJsonContent(
                            $error_exception,
                            'error'

                        );

                    } else {


                        if(isset($raw->param_type)) {
                            if($raw->param_type != '') {
                                $params->param_type = System::strSpecialClear($raw->param_type);
                            }
                        }

                        if(isset($raw->param_unit)) {
                            if($raw->param_unit != '') {
                                $params->format = System::strSpecialClear($raw->param_unit);
                            }
                        }


                        if(isset($raw->param_custom_name)) {
                            if($raw->param_custom_name != '') {
                                $params->param_custom_name = System::strLength(System::strSpecialClear($raw->param_custom_name),50);
                            }
                        }

                        if(isset($raw->formula_id)) {
                            if($raw->formula_id != '') {
                                $params->formula_id = System::intLength($raw->formula_id,11);
                            }
                        }

                        $params->param_name = System::strLength(System::strSpecialClear($raw->param_name),50);


                        if(isset($raw->update_type)) {
                            if($raw->update_type != '') {
                                $params->update_type = intval(System::strSpecialClear($raw->update_type));
                            }
                        }

                        if(isset($raw->time_live)) {
                            if($raw->time_live != '') {
                                $params->time_live = System::intLength(System::strSpecialClear($raw->time_live),11);
                            }
                        }

                        if(isset($raw->last_update)) {
                            if($raw->last_update != '') {
                                $params->last_update = System::strSpecialClear($raw->last_update);
                            }
                        }




                        $params->save();

                        JSON::buildJsonContent(
                            Success::getCode('param update','update'),
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