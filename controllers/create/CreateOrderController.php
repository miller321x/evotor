<?php

/**
 * Created by PhpStorm.
 * User: miller321x
 * Date: 25.05.17
 * Time: 13:30
 */
use Phalcon\Mvc\Controller;

class CreateOrderController extends Controller
{

    public function createOrderAction($App,$api,$mode = null)
    {
        if($App->accessPermission('createOrderAction')) {

            $raw = JSON::get($api);

            $error_exception = Orders::validNewOrder($App, $raw);

            if (count($error_exception) > 0) {

                JSON::buildJsonContent(
                    $error_exception,
                    'error'

                );

            } else {

                $product = Products::findFirstById($raw->product_id);

                if($product->count_limit > 0) {

                    $product->count_limit = $product->count_limit - 1;

                    $product->save();


                    if($product->group_status == 1) {

                        if(isset($raw->players) and $raw->players != '') {

                            if(is_array($raw->players)) {

                                $players = $raw->players;



                            } else {


                                $players = [];

                                $players[0] = $raw->players;


                            }

                            if(count($players) <= 10) {

                                $n = $product->price / (count($players) + 1);

                                $n = round($n);

                                if($App->userProfile['balance'] >= $n) {


                                    $user = Users::findFirstById($App->uid);

                                    $user->balance = $user->balance - $n;

                                    $user->save();


                                    $players_new = [];



                                    for($i = 0; $i < count($players); $i++) {

                                        $players_new[$i] = [];

                                        $players_new[$i]['id'] = $players[$i];

                                        if($players[$i] != $App->uid) {

                                            $players_new[$i]['status'] = 1;

                                        } else {

                                            $players_new[$i]['status'] = 2;

                                        }



                                    }

                                    $raw->players = JSON::encode($players_new);

                                    Orders::addNewOrder($raw, $api, $App);

                                } else {

                                    $exception = [];
                                    $exception[0] = 'no balance';
                                    $exception[1] = Errors::getCode('no balance');

                                    JSON::buildJsonContent(
                                        $exception,
                                        'error'

                                    );

                                }

                            } else {

                                $exception = [];
                                $exception[0] = 'max players in order';
                                $exception[1] = Errors::getCode('max players in order');

                                JSON::buildJsonContent(
                                    $exception,
                                    'error'

                                );

                            }




                        } else {



                            $exception = [];
                            $exception[0] = 'players is required';
                            $exception[1] = Errors::getCode('players is required');

                            JSON::buildJsonContent(
                                $exception,
                                'error'

                            );

                        }





                    } else {

                        if($mode == 'evotor') {

                            $user = Evotor_users::findFirstByEvotor_uid($raw->id);

                            $user = Users::findFirstById($user->uid);


                            if($user->balance >= $product->price) {


                                $user->balance = $user->balance - $product->price;

                                $user->save();



                                Orders::addNewOrder($raw, $api, $App);

                            } else {

                                $exception = [];
                                $exception[0] = 'no balance';
                                $exception[1] = Errors::getCode('no balance');

                                JSON::buildJsonContent(
                                    $exception,
                                    'error'

                                );

                            }

                        } else {

                            if($App->userProfile['balance'] >= $product->price) {


                                $user = Users::findFirstById($App->uid);

                                $user->balance = $user->balance - $product->price;

                                $user->save();



                                Orders::addNewOrder($raw, $api, $App);

                            } else {

                                $exception = [];
                                $exception[0] = 'no balance';
                                $exception[1] = Errors::getCode('no balance');

                                JSON::buildJsonContent(
                                    $exception,
                                    'error'

                                );
                            }

                        }




                    }


                } else {

                    $exception = [];
                    $exception[0] = 'count products limit done';
                    $exception[1] = Errors::getCode('count products limit done');

                    JSON::buildJsonContent(
                        $exception,
                        'error'

                    );

                }


            }

        }

    }

}