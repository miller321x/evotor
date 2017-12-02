<?php

/**
 * Created by PhpStorm.
 * User: miller321x
 * Date: 05.05.17
 * Time: 10:35
 */
use Phalcon\Mvc\Controller;

class UpdateProductController extends Controller
{

    public function updateProductAction($App)
    {

        $raw = JSON::get();

        if (isset($raw->id)) {

            if ($App->accessPermission('updateProductAction', $raw->id)) {

                $product = Products::findFirstById($raw->id);

                if (isset($product->id)) {

                    $error_exception = Products::validUpdateProduct($App, $raw);

                    if (count($error_exception) > 0) {
                        JSON::buildJsonContent(
                            $error_exception,
                            'error'

                        );

                    } else {


                        if(isset($raw->image)){

                            if(isset($raw->image->cropped)) {

                                $image = System::uploadImage($raw->image->cropped,$App);
                                $product->image = $image;
                            }

                            if(isset($raw->image->preview)) {

                                $image = System::uploadImage($raw->image->preview,$App);
                                $product->image = $image;
                            }


                            if(isset($raw->image->original)) {
                                $image_original = System::uploadImage($raw->image->original,$App);
                                $product->image_original = $image_original;
                            }


                        }


                        $product->title = System::strLength(System::strSpecialClear($raw->title),255);
                        $product->description = System::strSpecialClear($raw->description);
                        $product->price = System::intLength(System::strSpecialClear($raw->price),11);
                        $product->group_status = intval(System::strSpecialClear($raw->group_status));

                        $product->count_limit = System::intLength(System::strSpecialClear($raw->count_limit),11);

                        $product->save();

                        JSON::buildJsonContent(
                            Success::getCode('product update','update'),
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