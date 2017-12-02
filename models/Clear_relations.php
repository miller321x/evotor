<?php

/**
 * Created by PhpStorm.
 * User: miller321x
 * Date: 02.06.17
 * Time: 10:55
 */
use Phalcon\Mvc\Model;

class Clear_relations extends Model
{

    public static function clearConnects($api,$App,$id) {


    }

    public static function clearTeams($api,$App,$id) {


        $phql = "UPDATE Users SET team_id = 0 WHERE team_id = '".$id."'";

        $api->modelsManager->executeQuery($phql);

        $phql = "UPDATE Advert_to SET team_id = 0 WHERE team_id = '".$id."'";

        $api->modelsManager->executeQuery($phql);

    }

    public static function clearDepartments($api,$App,$id) {


        Teams::clearRelationsDep($api,$App,$id);

        Users::clearRelationsDep($api,$App,$id);

        Gem_achieves::clearRelationsDep($api,$App,$id);

        Gem_tasks::clearRelationsDep($api,$App,$id);


    }


    public static function clearRoles($api,$id) {

        $phql = "UPDATE Users SET user_role = 0, level_role = 0 WHERE user_role = '".$id."'";

        $api->modelsManager->executeQuery($phql);

    }


    public static function clearFormula($api,$App,$id) {

        $phql = "UPDATE Gem_params SET formula_id = 0 WHERE formula_id = '".$id."'";

        $api->modelsManager->executeQuery($phql);


        $phql = "UPDATE Gem_points SET formula_id = 0 WHERE formula_id = '".$id."'";

        $api->modelsManager->executeQuery($phql);

        $phql = "UPDATE Gem_achieves SET formula_id = 0 WHERE formula_id = '".$id."'";

        $api->modelsManager->executeQuery($phql);


        $phql = "UPDATE Gem_tasks SET formula_id = 0 WHERE formula_id = '".$id."'";

        $api->modelsManager->executeQuery($phql);

    }

}