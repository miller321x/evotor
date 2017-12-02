<?php

/**
 * Created by PhpStorm.
 * User: miller321x
 * Date: 23.05.17
 * Time: 16:33
 */
use Phalcon\Mvc\Model;

class Errors extends Model
{

    public static function getCode($name) {

        $dataErrors = [

            "company_name is required" => 41,

            "company isset yet" => 42,

            "ERROR_COMPANY_NAME_MAX" => 43,

            "ERROR_COMPANY_NAME_INVALID" => 44,

            "ERROR_COMPANY_ISSET" => 45,

            "dep_name is required" => 51,

            "company undefined" => 52,

            "ERROR_DEP_NAME_MAX" => 53,

            "ERROR_DEP_NAME_INVALID" => 54,

            "dep_name is empty" => 55,

            "dep name isset yet" => 56,

            "achieve title is required" => 61,

            "achieve image is required" => 62,

            "achieve coins is required" => 63,

            "achieve dep_id is required" => 64,

            "achieve name max length 50" => 65,

            "achieve name invalid title" => 66,

            "achieve name title used" => 67,

            "achieve rank max length 100" => 68,

            "achieve rank invalid string rank" => 69,

            "achieve name is empty" => 70,

            "connect_type is required" => 71,

            "api_url is required" => 72,

            "custom_name is required" => 73,

            "connect max length 100" => 74,

            "connect invalid name" => 75,

            "connect name yet isset" => 76,

            "api_connect_name is empty" => 77,

            "formula_name is required" => 81,

            "formula is required" => 82,

            "formula max length 100" => 83,

            "formula invalid name" => 84,

            "formula_name yet isset" => 85,

            "formula_name is empty" => 86,

            "api_connect_id is required" => 91,

            "param_name is required" => 92,

            "param max length 30" => 93,

            "param invalid name" => 94,

            "param name used" => 95,

            "param_name is empty" => 96,

            "point_name is required" => 101,

            "point formula_id is required" => 102,

            "period_mode is required" => 103,

            "interval_have is required" => 104,

            "point max length 30" => 105,

            "point invalid name" => 105,

            "point name used" => 105,

            "point_name is empty" => 106,

            "task title is required" => 111,

            "task formula_id is required" => 112,

            "task dep_id is required" => 113,

            "task invalid title" => 114,

            "task title yet isset" => 115,

            "task_name is empty" => 116,

            "product title is required" => 121,

            "product image is required" => 122,

            "product price is required" => 123,

            "product price not int" => 124,

            "product max length 100" => 125,

            "product invalid title" => 125,

            "product title used" => 127,

            "product_name is empty" => 128,

            "price very long" => 129,

            "team_name is required" => 131,

            "team dep_id is required" => 132,

            "team company undefined" => 133,

            "team_name is empty" => 134,

            "ERROR_TEAM_NAME_MAX" => 135,

            "ERROR_TEAM_NAME_INVALID" => 136,

            "team name isset yet" => 137,

            "localisation is required" => 141,

            "email_notifications is required" => 142,

            "black_list_users is required" => 143,

            "page_lock is required" => 144,

            "mode_anonim is required" => 145,

            "old_pass is required" => 146,

            "new_pass is required" => 147,

            "ERROR_USER_OLD_PASS_INCORRECT" => 148,

            "ERROR_USER_PASS_MIN" => 149,

            "ERROR_USER_PASS_MAX" => 150,

            "user name is required" => 151,

            "user email is required" => 152,

            "user pass is required" => 153,

            "rating is required" => 154,

            "balance is required" => 155,

            "user year is required" => 156,

            "ERROR_USER_NAME_MIN" => 157,

            "ERROR_USER_NAME_MAX" => 158,

            "ERROR_USER_NAME_INVALID" => 159,

            "ERROR_USER_ISSET" => 160,

            "ERROR_USER_EMAIL_MAX" => 161,

            "ERROR_USER_EMAIL_INVALID" => 162,

            "ERROR_EMAIL_ISSET" => 163,

            "full_name is required" => 164,

            "ERROR_USER_FULL_NAME_INVALID" => 165,

            "gender is required" => 166,

            "phone is required" => 167,

            "ERROR_USER_PHONE_MAX" => 168,

            "ERROR_USER_PHONE_INVALID" => 169,

            "ERROR_USER_PASS_INVALID" => 170,

            "order product_id is required" => 171,

            "product not found" => 172,

            "max players in order" => 173,

            "no balance" => 181,

            "advert message is required" => 191,

            "advert teams is required" => 192,

            "advert teams is empty" => 193,

            "advert message is empty" => 194,

            "advert max length 200" => 195,

            "message is required" => 201,

            "message is empty" => 202,

            "message max length 1000" => 203,

            "ERROR_EMAIL_ISSET_INVITE" => 204,

            "achieves not found" => 210,

            "Teams not deleted" => 220,

            "user with this email not found" => 230,

            "No balance send user" => 240,

            "bad formula" => 250,

            "negative int" => 260,

            "user not found (invite)" => 270,

            "approve email is required" => 280,

            "not approve" => 281,

            "ERROR_ALBUM_TITLE_MAX" => 290,

            "ERROR_ALBUM_DESC_MAX" => 292,

            "title_vacancy is required" => 300,

            "ERROR_VACANCY_TITLE_MAX" => 301,

            "description_vacancy is required" => 302,

            "ERROR_VACANCY_DESC_MAX" => 303,

            "pay is required" => 304,

            "experience is required" => 305,

            "schedule is required" => 306,

            "title_article is required" => 310,

            "title is required" => 311,

            "ERROR_ARTICLE_TITLE_MAX" => 312,

            "content_article is required" => 313,

            "ERROR_ARTICLE_DESC_MAX" => 314,

            "category_id_article is required" => 315,

            "type_article is required" => 316,

            "title_album is required" => 317,

            "description_album is required" => 318,

            "preview_article is required" => 320,

            "title_doc is required" => 330,

            "file_doc is required" => 331,

            "max file size 6" => 340,

            "incorrect image extension" => 341,

            "max file size 100" => 342,

            "max file size 500" => 343,

            "incorrect doc extension" => 344,

            "incorrect video extension" => 345,

            "need valid user" => 350,

            "error login pass undefined" => 360,

            "error login name undefined" => 361,

            "messenger is required" => 370,

            "ERROR_USER_MESSENGER_MAX" => 371,

            "ERROR_USER_MESSENGER_INVALID" => 372,

            "You must choose at least one file to send. Please try again." => 380,

            "error user rules" => 390,

            "name_rol is required" => 400,

            "name_rol max 100" => 401,

            "balance_add is required" => 402,

            "balance_add not int" => 403,

            "balance_add very long" => 404,


            "description task is required" => 410,

            "add_coins is required" => 411,

            "add_coins not int" => 412,

            "add_coins very long" => 413,

            "invalid format" => 414,

            "date_end task is required" => 415,

            "description event is required" => 416,

            "min file size 2" => 417,

            "players is required" => 418,

            "main role isset" => 419,

            "company name is empty" => 420,

            "field is required" => 421,

            "runk name max length 50" => 422,

            "email is required" => 423,

            "max int len 99999999" => 424,

            "max tasks static" => 425,

            "achieve max length 200" => 426,

            "description task max 200" => 427,

            "count products limit done" => 428,

            "product count_limit is required" => 430,

            "product count_limit not int" => 431,

            "count_limit very long" => 432,

            "count_limit need more null" => 433,

            "access denied" => 1000,

            "need department" => 434,

            "ERROR_USER_NAME_SECOND_MIN" => 435,



        ];


        if(isset($dataErrors[$name])) {

            return $dataErrors[$name];

        } else {

            return 0;

        }


    }

}