<?php

/**
 * Created by PhpStorm.
 * User: miller321x
 * Date: 04.05.17
 * Time: 15:12
 */
use Phalcon\Mvc\Controller;

class UpdateFormulaController extends Controller
{

    public function updateFormulaAction($App)
    {

        $raw = JSON::get();

        if (isset($raw->id)) {

            if ($App->accessPermission('updateFormulaAction', $raw->id)) {

                $formula = Gem_formules::findFirstById($raw->id);

                if (isset($formula->id)) {

                    $error_exception = Gem_formules::validUpdateFormula($App, $raw);

                    if (count($error_exception) > 0) {
                        JSON::buildJsonContent(
                            $error_exception,
                            'error'

                        );

                    } else {


                        $formula->formula_name = System::strLength(System::strSpecialClear($raw->formula_name),100);
                        $formula->formula = System::strSpecialClear($raw->formula);


                        $formula->save();

                        JSON::buildJsonContent(
                            Success::getCode('formula update','update'),
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