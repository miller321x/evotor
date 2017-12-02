<?php

/**
 * Created by PhpStorm.
 * User: miller321x
 * Date: 17.04.17
 * Time: 10:27
 */
use Phalcon\Mvc\Model;

class Transactions extends Model
{

    public $id;

    public $uid;

    public $transaction_type;

    public $transaction_id;

    public $transaction_value;

    public $date_add;

    public $object_type;

    public $about;

    public $action;

    public $company_id;

    public $status;



    public static function readTransactions($api,$App) {


        $phql = "UPDATE Transactions SET status = 1 WHERE uid = :uid: AND status = 0";

        $api->modelsManager->executeQuery($phql, [
            "uid" => $App->uid,
        ]);



        $sql = "group_status LIKE :value:";

        $bind = [
            "value" => '%{"'.$App->uid.'":0}%',
        ];

        $data = self::find(
            [
                $sql,
                "bind" => $bind,

            ]
        );


        if(count($data) > 0) {

            foreach ($data as $val) {

                $group = JSON::decode($val->group_status);

                $new_group = [];

                $i = 0;

                foreach ($group as $user) {

                    foreach ($user as $key => $value) {

                        if($key == $App->uid) {

                            $new_group[$i][$App->uid] = 1;

                        } else {

                            $new_group[$i][$key] = $value;

                        }
                        $i++;
                    }
                }



                $group = JSON::encode($new_group);


                $phql = "UPDATE Transactions SET group_status = '".$group."' WHERE id = :id:";

                $api->modelsManager->executeQuery($phql, [
                    "id" => $val->id,
                ]);


            }

        }


        JSON::buildJsonContent(
            Success::getCode('read transactions','update'),
            'ok'

        );



    }

/**
* INSERT INTO TRANSACTIONS
*
*/

    # add param

    public static function addNewTransaction($data, $api, $App) {


        if(!isset($data->transaction_type)) {
            $data->transaction_type = '';
        }

        if(!isset($data->object_type)) {
            $data->object_type = '';
        }

        if(!isset($data->message)) {
            $data->message = '';
        }


        $company_id = Companies::getDefault($App);

        # SQL Query

        $phql = 'INSERT INTO transactions (uid, transaction_type, '
            . '	transaction_id, transaction_value, transaction_rating, date_add, object_type, about, action, company_id, status, group_status, group_ids, message) '
            . ' VALUES (:uid:, :transaction_type:, :transaction_id:, :transaction_value:, :transaction_rating:, :date_add:, :object_type:, :about:, :action:, :company_id:, :status:, :group_status:, :group_ids:, :message:)';



        $status = $api->modelsManager->executeQuery(

            $phql,

            [

                "uid" => System::intLength($data->uid,11),

                "transaction_type" => $data->transaction_type,

                "transaction_id" => System::intLength($data->transaction_id,11),

                "transaction_value" => System::intLength($data->transaction_value,11),

                "transaction_rating" => System::intLength($data->transaction_rating,11),

                "date_add" => System::toDay(),

                "object_type" => System::strLength($data->object_type,50),

                "about" => $data->about,

                "action" => $data->action,

                "company_id" => $company_id,

                "status" => 0,

                "group_status" => $data->group_status,

                "group_ids" => $data->group_ids,

                "message" => $data->message,

            ]
        );


        if ($status->success() === false) {



            $errors = [];

            foreach ($status->getMessages() as $message) {
                $errors[] = $message->getMessage();
            }

            JSON::buildJsonContent(
                $errors,
                'error'

            );
        } else {

            return $status->getModel()->id;

        }


    }



    /**
     * Builing Model Data for view
     */

    public static function buildDataTransactions($list_keys,$data,$Controller) {

        $data_by_keys = [];

        for($i = 0; $i < count($list_keys); $i++) {


            $key_val = trim($list_keys[$i]);

            if($key_val == 'id') {

                $data_by_keys['id'] = $data->id;

            }

            if($key_val == 'uid') {

                $data_by_keys['uid'] = $data->uid;

                if($data->uid == 0) {


                    $data_by_keys['user_name'] =  Ui::lang($Controller->App, 'ADMIN_NAME');

                } else {

                    $user = Users::findFirstById($data->uid);

                    if(isset($user->id)) {



                        $data_by_keys['user_name'] = $user->name.' '.$user->full_name;

                    }

                }



            }


            if($key_val == 'transaction_type') {

                $data_by_keys['transaction_type'] = $data->transaction_type;

            }

            if($key_val == 'transaction_value') {

                $data_by_keys['transaction_value'] = $data->transaction_value;

            }

            if($key_val == 'transaction_rating') {

                $data_by_keys['transaction_rating'] = $data->transaction_rating;

            }

            if($key_val == 'transaction_id') {

                if($data->transaction_type == 'send_coins') {

                    $user = Users::findFirstById($data->transaction_id);

                    if(isset($user->id)) {

                        if($data->transaction_type == 'send_coins') {

                            if($data->uid == $Controller->App->uid) {

                                $data_by_keys['transaction_sender_name'] = $user->name.' '.$user->full_name;

                            } else {

                                if($data->transaction_id == $Controller->App->uid) {

                                    $data_by_keys['transaction_sender_name'] =  Ui::lang($Controller->App, 'CLIENT_SEND_BALANCE_YOU');

                                } else {

                                    $data_by_keys['transaction_sender_name'] =  $user->name.' '.$user->full_name;

                                }

                            }

                        }  else {

                            $data_by_keys['transaction_sender_name'] = $user->name.' '.$user->full_name;

                        }

                    }
                }

                if($data->transaction_type == 'order_group') {

                    $user = Users::findFirstById($data->transaction_id);

                    $data_by_keys['transaction_sender_name'] = $user->name.' '.$user->full_name;

                }



                if($data->transaction_type == 'balance') {

                    $data_by_keys['transaction_sender_name'] = Ui::lang($Controller->App, 'ADMIN_NAME');

                }

                $data_by_keys['transaction_id'] = $data->transaction_id;

            }

            if($key_val == 'date_add') {

                $date = explode(' ', $data->date_add);

                $data_by_keys['date'] = System::dateFormat($date[0],$Controller->App);
                $data_by_keys['time'] = $date[1];

            }

            if($key_val == 'about') {

                if($data->transaction_type == 'send_coins') {

                    if($data->uid == $Controller->App->uid) {

                        $data_by_keys['about'] = trim($data->about);

                    } else {

                        if($data->transaction_id == $Controller->App->uid) {

                            $data_by_keys['about'] =  trim(Ui::lang($Controller->App, 'CLIENT_SEND_BALANCE'));

                        } else {

                            $data_by_keys['about'] =  trim(Ui::lang($Controller->App, 'USER_SEND_BALANCE'));

                        }




                    }

                } else {

                    $data_by_keys['about'] = $data->about;

                }


            }

            if($key_val == 'message') {

                $data_by_keys['message'] = $data->message;

            }



            if($key_val == 'action') {

                $data_by_keys['action'] = $data->action;

            }


        }

        return $data_by_keys;

    }

}