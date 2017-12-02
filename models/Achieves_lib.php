<?php

/**
 * Created by PhpStorm.
 * User: miller321x
 * Date: 19.06.17
 * Time: 15:06
 */
use Phalcon\Mvc\Model;

class Achieves_lib extends Model
{

    public $id;

    public $template_id;

    public $image;



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

            if($key_val == 'template_id') {

                $data_by_keys['template_id'] = $data->template_id;

            }

            if($key_val == 'image') {

                $data_by_keys['image'] = System::getImageUrl($data->image);

            }

        }

        return $data_by_keys;

    }

}