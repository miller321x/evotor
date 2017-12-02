<?php

/**
 * Created by PhpStorm.
 * User: miller321x
 * Date: 14.04.17
 * Time: 17:43
 */
use Phalcon\Mvc\Model;

class Orders extends Model
{

    public $id;

    public $product_id;

    public $uid;

    public $user_message;

    public $date_add;

    public $company_id;

    public $status;

    public $group_status;

    public $users;





    # new order validation

    public static function validNewOrder($App, $data)
    {

        $exception = [];



        if(!isset($data->product_id)) {
            $exception[0] = 'product_id is required';
            $exception[1] = Errors::getCode('order product_id is required');
        } else {

            $res = Products::findFirstById($data->product_id);

            if (!isset($res->id)) {
                $exception[0] = 'product not found';
                $exception[1] = Errors::getCode('product not found');
            }
        }


        return $exception;

    }

    /**
     * INSERT INTO ORDERS
     *
     */

    # add company

    public static function addNewOrder($value,$api,$App)
    {


        if(!isset($value->company_id)) {

            $value->company_id = Companies::getDefault($App);

        }

        if(!isset($value->user_message)) {
            $value->user_message = '';
        }

        $group = 0;

        $group_users = '';

        if(isset($value->players) and $value->players != '') {

            $group = 1;

            $group_users = $value->players;

        }


        # SQL Query

        $phql = 'INSERT INTO orders (product_id, uid, user_message, date_add, '
            . 'company_id, status, group_status, users) VALUES (:product_id:, :uid:, '
            . ':user_message:, :date_add:, :company_id:, :status:, :group_status:, :users:)';



        $status = $api->modelsManager->executeQuery(

            $phql,

            [

                "product_id" => System::intLength($value->product_id,11),

                "uid" => $App->uid,

                "user_message" => $value->user_message,

                "date_add" => System::toDay(),

                "company_id" => $value->company_id,

                "status" => 0,

                "group_status" => $group,

                "users" => $group_users

            ]
        );


        if ($status->success() === true) {

            $order_id = $status->getModel()->id;

            if($group == 1) {

                $transaction = new TransactionController();

                $users = JSON::decode($value->players);

                $product = Products::findFirstById($value->product_id);

                foreach($users as $user) {


                    $transaction->App = $App;

                    $transaction->api = $api;

                    $transaction->transaction_value = $product->price;

                    $transaction->about = $product->title;

                    $transaction->uid = $user->id;

                    $transaction->transaction_id = $App->uid;

                    $transaction->action = 2;

                    $transaction->set('newOrderGroup');

                }
            }


            $frontend = [];

            $frontend['id'] = $order_id;

            $frontend['code'] = Success::getCode('order create');


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




    /**
     * Builing Model Data for view
     */

    public static function buildDataOrders($list_keys,$data,$Controller) {

        $data_by_keys = [];

        for($i = 0; $i < count($list_keys); $i++) {

            $key_val = trim($list_keys[$i]);

            if($key_val == 'id') {

                $data_by_keys['id'] = $data->id;

            }

            if($key_val == 'product_id') {

                $data_by_keys['product_id'] = $data->product_id;

                $product = Products::findFirstById($data->product_id);

                if(isset($product->id)) {

                    $data_by_keys['product_image'] = System::getImageUrl($product->image);

                    $data_by_keys['product_title'] = $product->title;

                    if($data->group_status > 0) {

                        $users = JSON::decode($data->users);

                        $price =  round($product->price / ((count($users)) + 1));

                        $data_by_keys['product_price'] = $price;

                    } else {

                        $data_by_keys['product_price'] = $product->price;

                    }



                }

            }

            if($key_val == 'uid') {

                $data_by_keys['uid'] = $data->uid;

                if($Controller->App->userPermission == 'admin') {

                    $user = Users::findFirstById($data->uid);

                    if(isset($user->id)) {

                        $data_by_keys['user_photo'] = System::getImageUrl($user->photo);

                        $data_by_keys['user_name'] = $user->name.' '.$user->full_name;

                    }

                }


            }

            if($key_val == 'group_status') {

                $data_by_keys['group_status'] = $data->group_status;

            }

            if($key_val == 'users') {

                if($data->users != '') {

                    $users = JSON::decode($data->users);
                    $user_data = [];

                    $ci = 0;

                    $user_data[$ci] = [];
                    $user_data[$ci]['id'] = $data->uid;
                    $user_data[$ci]['status'] = 2;
                    $user_info = Users::findFirstById($data->uid);
                    $user_data[$ci]['name'] =  $user_info->name.' '.$user_info->full_name;

                    $ci++;

                    $done = 0;

                    foreach($users as $user) {
                        $user_data[$ci] = [];
                        $user_data[$ci]['id'] = $user->id;
                        $user_data[$ci]['status'] = intval($user->status);
                        $user_info = Users::findFirstById($user->id);

                        if($user->id == $Controller->App->uid) {
                            if($user->status == 2) {
                                $done = 1;
                            }
                        }

                        $user_data[$ci]['name'] =  $user_info->name.' '.$user_info->full_name;

                        if($user->id == $Controller->App->uid) {
                            $user_data[$ci]['client'] = 1;
                        }

                        $ci++;
                    }
                    if($done == 1) {
                        $data_by_keys['client_done'] = 1;
                    }

                    $data_by_keys['users'] = $user_data;

                }


            }

            if($key_val == 'description') {

                $data_by_keys['description'] = $data->description;

            }


            if($key_val == 'user_message') {

                if($Controller->App->userPermission == 'admin') {

                    $data_by_keys['user_message'] = $data->user_message;

                }

            }

            if($key_val == 'date_add') {

                $date = explode(' ', $data->date_add);
                $data_by_keys['date'] = $date[0];
                $data_by_keys['time'] = $date[1];

            }

            if($key_val == 'company_id') {

                $data_by_keys['company_id'] = $data->company_id;

            }

            if($key_val == 'status') {

                if($data->status == 0) {

                    $data_by_keys['status_title'] = Ui::lang($Controller->App,'ORDER_STATUS_1');

                } else if($data->status == 1) {

                    $data_by_keys['status_title'] = Ui::lang($Controller->App,'ORDER_STATUS_2');

                } else {

                    $data_by_keys['status_title'] = Ui::lang($Controller->App,'ORDER_STATUS_3');

                }

                $data_by_keys['status'] = $data->status;


            }


        }

        return $data_by_keys;

    }


}