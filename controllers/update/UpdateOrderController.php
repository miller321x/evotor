<?php

/**
 * Created by PhpStorm.
 * User: miller321x
 * Date: 25.05.17
 * Time: 15:04
 */
use Phalcon\Mvc\Controller;

class UpdateOrderController extends Controller
{


    public function updateGroupOrderAction($App,$api)
    {

        $raw = JSON::get();

        if (isset($raw->id)) {

            if ($App->accessPermission('updateGroupOrderAction')) {

                $order = Orders::findFirstById($raw->id);


                if(isset($order->id)) {


                    $product = Products::findFirstById($order->product_id);

                    $user = Users::findFirstById($App->uid);

                    $users = JSON::decode($order->users);




                    if(isset($raw->status)) {


                        $n = count($users);

                        $c = intval($product->price);

                        $pr = $c / ($n + 1);

                        $pr = round($pr);



                        if($user->balance >= $pr or $raw->status == 3) {


                            if($raw->status == 2) {

                                $user->balance = $user->balance - $pr;

                                $user->save();

                            }


                            $access = [];
                            $i = 0;

                            $active = 1;

                            foreach($users as $user) {

                                $access[$i] = [];

                                $access[$i]['id'] = $user->id;

                                if($App->uid == $user->id) {

                                    $access[$i]['status'] = intval($raw->status);

                                    if($raw->status == 3 || $raw->status == 1) {
                                        $active = 0;
                                    }
                                    if($raw->status == 3) {
                                        $active = 3;
                                    }

                                } else {

                                    $access[$i]['status'] = intval($user->status);

                                    if($user->status == 3 || $user->status == 1) {
                                        $active = 0;
                                    }
                                    if($user->status == 3) {
                                        $active = 3;
                                    }
                                }

                                $i++;

                            }

                            $access = JSON::encode($access);

                            $order->users = $access;

                            if($active == 1) {

                                $order->group_status = 2;

                                $transaction = new TransactionController();


                                $transaction->App = $App;

                                $transaction->api = $api;

                                $transaction->transaction_value = $product->price;

                                $transaction->about = $product->title;

                                $transaction->uid = $App->uid;

                                $transaction->transaction_id = $product->id;

                                $transaction->action = 2;

                                $transaction->set('newOrder');


                                foreach($users as $user) {

                                    $transaction->App = $App;

                                    $transaction->api = $api;

                                    $transaction->transaction_value = $product->price;

                                    $transaction->about = $product->title;

                                    $transaction->uid = $user->id;

                                    $transaction->transaction_id = $product->id;

                                    $transaction->action = 2;

                                    $transaction->set('newOrder');
                                }



                            }

                            if($active == 3) {

                                $order->group_status = 3;

                            }


                            $order->save();



                            if($raw->status == 3) {

                                $mess = 'order group delete update';

                            } else {

                                $mess = 'order group update';

                            }


                            JSON::buildJsonContent(
                                Success::getCode($mess,'update'),
                                'ok'

                            );





                        } else {


                            JSON::buildJsonContent(
                                [
                                    0 => 'No balance send user',
                                    1 => Errors::getCode('No balance send user')
                                ],
                                'error'

                            );

                        }


                    } else {

                        JSON::buildJsonContent(
                            'status is required',
                            'error'

                        );
                    }



                } else {

                    JSON::buildJsonContent(
                        'order not found',
                        'error'

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


    public function updateOrderAction($App,$api)
    {

        $raw = JSON::get();

        if (isset($raw->id)) {

            if ($App->accessPermission('updateOrderAction', $raw->id)) {

                $order = Orders::findFirstById($raw->id);

                if (isset($order->id)) {

                        $order->status = System::intLength($raw->status,1);

                        $order->save();

                        $product = Products::findFirstById($order->product_id);




                        if($order->status == 1) {

                            $transaction = new TransactionController();

                            if($order->group_status != 0) {

                                $users = JSON::decode($order->users);

                                foreach($users as $user) {

                                    $transaction->App = $App;

                                    $transaction->api = $api;

                                    $transaction->transaction_value = $product->price;

                                    $transaction->about = $product->title;

                                    $transaction->uid = $user->id;

                                    $transaction->transaction_id = $product->id;

                                    $transaction->action = 2;

                                    $transaction->set('newOrder');


                                }

                            } else {


                                $transaction->App = $App;

                                $transaction->api = $api;

                                $transaction->transaction_value = $product->price;

                                $transaction->about = $product->title;

                                $transaction->uid = $order->uid;

                                $transaction->transaction_id = $product->id;

                                $transaction->action = 2;

                                $transaction->set('newOrder');

                            }

                            JSON::buildJsonContent(
                                Success::getCode('order approve update','update'),
                                'ok'

                            );


                        }

                        if($order->status == 2) {


                            $product->count_limit = $product->count_limit + 1;

                            $product->save();


                            if($order->group_status != 0) {

                                $product = Products::findFirstById($order->product_id);

                                $users = JSON::decode($order->users);

                                $n = $product->price / (count($users) + 1);

                                $n = round($n);

                                $user = Users::findFirstById($order->uid);

                                $user->balance = $user->balance + $n;

                                $user->save();




                                foreach($users as $user_data) {


                                    $user = Users::findFirstById($user_data->id);

                                    $user->balance = $user->balance + $n;

                                    $user->save();


                                }


                            } else {

                                $product = Products::findFirstById($order->product_id);

                                $user = Users::findFirstById($order->uid);

                                $user->balance = $user->balance + $product->price;

                                $user->save();

                            }

                            JSON::buildJsonContent(
                                Success::getCode('order remove update','update'),
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