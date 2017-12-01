<?php

/**
 * Created by PhpStorm.
 * User: miller321x
 * Date: 08.05.17
 * Time: 17:13
 */

use Phalcon\Mvc\Controller;

class ViewOrderController extends Controller
{

    public $model = 'Orders';
    public $data = null;
    public $limit = 30;
    public $App = null;

    public function getOrders($App)
    {


    }




}