<?php


use Phalcon\Mvc\Router;
use Phalcon\Mvc\Micro;

$api = new Micro($di);

$api->getRouter()->setUriSource(\Phalcon\Mvc\Router::URI_SOURCE_SERVER_REQUEST_URI);

$App = new AuthController();
$App->UserAuth();


$Request = new RequestController();
$Request->register($App,$api);


$api->get(
    "/api/create_doc",
    function () use ($App)  {

        include APP_PATH . '/createDoc.php';

    }
);

$api->get(
    "/api/return/folders",
    function () use ($App,$api)  {
        $Controller = new CreateUserController();
        $Controller->returnfoldersPlayersAction($App,$api);
    }
);


/**
 * Установка приложения в Эвотор
 *
 * method POST
 * @url /api/evotor/app
 * @param $token string
 *
 */
$api->post(
    "/api/evotor/app",
    function () use ($App,$api)  {
        $Controller = new evotorController();
        $Controller->appAction($App,$api,'app');
    }
);

/**
 * Получения ключа Эвотор для обращения к облаку
 *
 * method POST
 * @url /api/evotor/token
 * @param $access_token string
 *
 */
$api->post(
    "/api/evotor/token",
    function () use ($App,$api)  {
        $Controller = new evotorController();
        $Controller->appAction($App,$api,'token');
    }
);
/**
 * Обновление сотрудников в Эвотор
 *
 * method POST
 * @url /api/evotor/user
 * @param $access_token string
 *
 */
$api->put(
    "/api/evotor/import_employers",
    function () use ($App,$api)  {
        $Controller = new evotorController();
        $Controller->appAction($App,$api,'importEmployers');
    }
);
/**
 * Обновление магазинов в Эвотор
 *
 * method POST
 * @url /api/evotor/stores
 * @param $access_token string
 *
 */
$api->put(
    "/api/evotor/import_stores",
    function () use ($App,$api)  {
        $Controller = new evotorController();
        $Controller->appAction($App,$api,'importStores');
    }
);
/**
 * Регистрация клиента в приложении в Эвотор
 *
 * method POST
 * @url /api/evotor/register
 * @param $access_token string
 *
 */
$api->post(
    "/api/evotor/register",
    function () use ($App,$api)  {
        $Controller = new evotorController();
        $Controller->appAction($App,$api,'register');
    }
);
/**
 * Авторизация клиента в приложении в Эвотор
 *
 * method POST
 * @url /api/evotor/login
 * @param $access_token string
 *
 */
$api->post(
    "/api/evotor/login",
    function () use ($App,$api)  {
        $Controller = new evotorController();
        $Controller->appAction($App,$api,'login');
    }
);


/**
 * Статистика магазинов Эвотор
 *
 * method POST
 * @url /api/evotor/stores
 * @param $access_token string
 *
 */
$api->post(
    "/api/evotor/stores",
    function () use ($App,$api)  {
        $Controller = new evotorController();
        $Controller->appAction($App,$api,'statDataStores');
    }
);


/**
 * Статистика сотрудников Эвотор
 *
 * method POST
 * @url /api/evotor/employers
 * @auth bearer token string
 *
 */
$api->post(
    "/api/evotor/employers",
    function () use ($App,$api)  {
        $Controller = new evotorController();
        $Controller->appAction($App,$api,'statDataEmployers');
    }
);


/**
 *  Профайл сотрудника Эвотор
 *
 * method POST
 * @url /api/evotor/profile
 * @auth bearer token string
 * @param id string - эвотор ID сотруднка
 */
$api->post(
    "/api/evotor/profile",
    function () use ($App,$api)  {
        $Controller = new evotorController();
        $Controller->appAction($App,$api,'profileEmployer');
    }
);


/**
 *  Получить товары магазина
 *
 * method POST
 * @url /api/evotor/products
 * @auth bearer token string
 *
 */
$api->post(
    "/api/evotor/products",
    function () use ($App,$api)  {
        $Controller = new ViewProductController();
        $Controller->getProducts($App);
    }
);

/**
 *  Получить товар по ID
 *
 * method POST
 * @url /api/evotor/product
 * @auth bearer token string
 * @param id int - ID товара
 */
$api->post(
    "/api/evotor/product",
    function () use ($App,$api)  {
        $Controller = new ViewProductController();
        $Controller->getProductOne($App,$api);
    }
);


/**
 *  Получить достижения
 *
 * method POST
 * @url /api/evotor/achieves
 * @auth bearer token string
 *
 */
$api->post(
    "/api/evotor/achieves",
    function () use ($App,$api)  {
        $Controller = new ViewGemController();
        $Controller->getAchieves($App);
    }
);


/**
 *  Получить достижение по ID
 *
 * method POST
 * @url /api/evotor/achieve
 * @auth bearer token string
 * @param id int - ID достижения
 */
$api->post(
    "/api/evotor/achieve",
    function () use ($App,$api)  {
        $Controller = new ViewGemController();
        $Controller->getAchieveOne($App,$api);
    }
);


/**
 *  Купить товар
 *
 * method POST
 * @url /api/evotor/order
 * @auth bearer token string
 * @param id string - ID сотрудника в эвотор
 */
$api->post(
    "/api/evotor/order",
    function () use ($App,$api)  {
        $Controller = new CreateOrderController();
        $Controller->createOrderAction($App,$api,'evotor');
    }
);




/**
 * Создать пользователя
 * Создаст нового пользователя
 * method POST
 * @url /api/user/create
 * @param $access_token string
 * @param $name string
 * @param $email string
 * @param $pass string
 */
$api->post(
    "/api/user/create",
    function () use ($App,$api)  {
        $Controller = new CreateUserController();
        $Controller->createUserAction($App,$api);
    }
);

/**
 * Авторизация пользователя
 * Авторизует пользователя
 * method POST
 * @url /api/login
 * @param $login string
 * @param $pass string
 * @param $mode string : sid - через cookies; token - через ключ
 */
$api->post(
    "/api/login",
    function () use ($App,$api)  {
        $App->loginAction($App,$api,'gamer');
    }
);

/**
 * Авторизация пользователя в админке
 * Авторизует пользователя
 * method POST
 * @url /api/admin/login
 * @param $login string
 * @param $pass string
 * @param $mode string : sid - через cookies; token - через ключ
 */
$api->post(
    "/api/admin/login",
    function () use ($App,$api)  {
        $App->loginAction($App,$api,'admin');
    }
);

/**
 * Выход из аккаунта
 * выход и удаление сессии
 * method POST
 * @url /api/logout
 * @param $token string
 */
$api->post(
    "/api/logout",
    function () use ($App) {
        $App->logoutAction();
    }
);

/**
 * Список модулей на странице
 * Узнать название модулей, используемых на странице (компоненте)
 * method POST
 * @url /api/config
 * @param $component string : название компонента. Доступны компоненты и вложенные модули в них
 # member_dashboard
   =>
   => user_profile - модуль профайл пользователя, products - модуль товары, global_rating -  модуль глобальный рейтинг, categories - модуль категорий, banners - модуль банеров, docs - модуль документов, news - объявления и новости, coworker_menu - categories{class=coworker_menu}, media - categories{class=media}, products_wall - модуль бренды, partners_wall - модуль партнеры
 * @param $access_token string
 */
$api->post(

    "/api/config",
    function () use ($App) {
        $App->getConfig();
    }
);
$api->get(

    "/api/config",
    function () use ($App) {
        $App->getConfig();
    }
);

/**
 * Вызов главного конструктора
 * Получить модули, данные через сборщик
 * @method POST / GET
 * @url /api/constructor
 * @param $access_token string
 * @param $methods string : вызов методов через запятую
 */

$api->get(
    "/api/constructor",
    function () use ($App,$api) {

        $Controller = new ConstructorDataController();
        $Controller->getData($App,$api);

    }
);
$api->post(
    "/api/constructor",
    function () use ($App,$api) {

        $Controller = new ConstructorDataController();
        $Controller->getData($App,$api);

    }
);




/**
 * Редактирование email
 *
 * method POST
 * @url /api/user/email
 * @param $data string : email
 */
$api->post(
    "/api/user/email",
    function () use ($App) {
        $Controller = new UpdateUserController();
        $Controller->setContacts($App,'email');
    }
);
/**
 * Редактирование phone
 *
 * method POST
 * @url /api/user/phone
 * @param $data string : phone
 */
$api->post(
    "/api/user/phone",
    function () use ($App) {
        $Controller = new UpdateUserController();
        $Controller->setContacts($App,'phone');
    }
);

/**
 * Редактирование messenger
 *
 * method POST
 * @url /api/user/messenger
 * @param $data string : messenger
 */
$api->post(
    "/api/user/messenger",
    function () use ($App) {
        $Controller = new UpdateUserController();
        $Controller->setContacts($App,'messenger');
    }
);

/**
 * Редактирование монет у пользователя
 *
 * method POST
 * @url /api/user/balance
 * @param $balance int : монеты
 * @param $id int : ID игрока
 *
 */
$api->post(
    "/api/user/balance",
    function () use ($App,$api) {
        $Controller = new UpdateUserController();
        $Controller->updatePlayerBalance($App,$api);
    }
);



/**
 * Активация игрока
 * При переходе по ссылке с письма
 * method GET
 * @url /api/activate_player
 * @param $key string : ключ доступа
 */
$api->get(
    "/api/activate_player",
    function () use ($App,$api) {
        $Controller = new CreateUserController();
        $Controller->createPlayerAction($App,$api);
    }
);








$api->post(
    "/api/save_mail",
    function () use ($App,$api) {
        $Controller = new SendMailController();
        $Controller->createMailFollowerAction($App,$api);
    }
);

/**
 * Интеграция сервисов | отправка
 * Отправка данных в gemificationlab. Любые данныев формате JSON
 * method POST
 * @url /api/push
 * @param $key string : ключ доступа
 */
$api->post(
    "/api/push",
    function () use ($App,$api) {
        $Controller = new dataHandlerController();
        $Controller->pushNewData($App,$api);
    }
);

/**
 * Интеграция сервисов | запрос
 * Подключение по api url к другим сервисам. Любые данныев формате JSON
 * method POST
 * @url /api/import
 */
$api->post(
    "/api/import",
    function () use ($App,$api) {
        $Controller = new dataHandlerController();
        $Controller->getNewData($App,$api);
    }
);


$api->post(
    "/api/cron",
    function () use ($App,$api) {
        $Controller = new dataHandlerController();
        $Controller->startCron($App,$api);
    }
);



/**
 * Пользователь | новый пароль
 * Записать новый пароль
 * method POST
 * @url /api/player/new_pass
 * @param $token string : ключ, который приходит на почту
 * @param $new_pass string : новый пароль
 */

$api->post(
    "/api/player/new_pass",
    function () use ($App) {
        $Controller = new UpdateUserController();
        $Controller->setNewPass($App);
    }
);

/**
 * Пользователь | отправка монет
 * Отправка монет другом пользователю
 * method POST
 * @url /api/player/send_coins
 * @param $coins int : количество монет
 * @param $uid int : id пользователя
 */

$api->post(
    "/api/player/send_coins",
    function () use ($App,$api) {
        $Controller = new UpdateUserController();
        $Controller->sendCoins($App,$api);
    }
);

/**
 * Пользователь | новое фото
 * сохранить новое фото
 * method POST
 * @url /api/player/photo
 * @param $photo string : файл в формате base64
 */
$api->post(
    "/api/player/photo",
    function () use ($App) {
        $Controller = new UpdateUserController();
        $Controller->setPhoto($App);
    }
);



$api->post(
    "/api/player_info",
    function () use ($App,$api) {
        $Controller = new ViewUserController();
        $Controller->getPlayerInfo($App,$api);
    }
);

/**
 * Пользователь | данные игрока
 * получить данные игрока для рекдактирования
 * method POST
 * @url /api/player/get
 * @param $id int : id игрока
 * @param ответ json : #ok => => id, name - имя, full_name - фамилия, third_name - отчество, photo - фото, rating - рейтинг, game_level - уровень, balance - монеты, achieves - достижения, api_id - подключения, dep_id - отдел, team_id - команда, all_teams - все команды
 */
$api->post(
    "/api/player/get",
    function () use ($App,$api) {
        $Controller = new ViewUserController();
        $Controller->getPlayerById($App,$api);
    }
);

/**
 * Пользователь | сохранить данные
 * сохранить данные игрока
 * method POST
 * @url /api/player/update
 * @param $id int : id игрока
 * @param $status_display_rating int: отображать игрока в рейтинге (0 да / 1 нет)
 * @param $rating int : рейтинг
 * @param $balance int : монеты
 * @param $team_id int : id команды
 * @param $achieves string : json
 * @param $api_id int : подключения в json
 */
$api->post(
    "/api/player/update",
    function () use ($App,$api) {
        $Controller = new UpdateUserController();
        $Controller->updatePlayerAction($App,$api);
    }
);

/**
 * Пользователь | сохранить выбранную команду и получить роли
 * сохранить данные игрока
 * method POST
 * @url /api/player/update_team
 * @param $id int : id игрока
 * @param $team_id int : id команды

 */
$api->post(
    "/api/player/update_team",
    function () use ($App,$api) {
        $Controller = new UpdateUserController();
        $Controller->updateTeamPlayerAction($App,$api);
    }
);

/**
 * Пользователь | изменить текстовый статус
 * обновить текстовый статус в профиле
 * method POST
 * @url /api/player/status
 * @param $status
 *
 */
$api->post(
    "/api/player/status",
    function () use ($App) {
        $Controller = new UpdateUserController();
        $Controller->updatePlayerStatusAction($App);
    }
);





/**
 * Пользователь | удаление
 * отправить приглошения игрока
 * method POST
 * @url /api/player/delete
 * @param $id string : id игрока
 */
$api->post(
    "/api/player/delete",
    function () use ($App,$api) {
        $Controller = new DeleteUserController();
        $Controller->deletePlayerAction($App,$api);
    }
);
/**
 * Пользователь | восстановление пароля
 * запрос на восстановление пароля
 * method POST
 * @url /api/player/forgot
 * @param $email string : email игрока
 */
$api->post(
    "/api/player/forgot",
    function () use ($App,$api) {
        $Controller = new CreateUserController();
        $Controller->forgotPasswordAction($App,$api);
    }
);

/**
 * Пользователь | смена пароля
 *
 * method POST
 * @url /api/player/pass_change
 * @param $email string : email игрока
 * @param $pass string : пароль
 *
 */
$api->post(
    "/api/player/pass_change",
    function () use ($App) {
        $Controller = new UpdateUserController();
        $Controller->changePasswordAction($App);
    }
);

/**
 * Получить логотип компании
 *
 * method POST
 * @url /api/cсompany/logo
 * @param $company int : id компании
 */

$api->post(
    "/api/company/logo",
    function () use ($App) {
        $Controller = new ViewCompanyController();
        $Controller->getCompaniesLogo($App);
    }
);

/**
 * Получить компанию
 * по умолчанию вернет компанию к которой относится авторизованный пользователь
 * method GET / POST
 * @url /api/companies
 */
$api->get(
    "/api/companies",
    function () use ($App) {
        $Controller = new ViewCompanyController();
        $Controller->getCompanies($App);
    }
);
$api->post(
    "/api/companies",
    function () use ($App) {
        $Controller = new ViewCompanyController();
        $Controller->getCompanies($App);
    }
);


/**
 * Добавить компанию
 * создаст новую компанию
 * method POST
 * @url /api/company/create
 * @param $company_name string : название компании
 * @param $company_image string : картинка в формате base64
 * @param $email string : email
 * @param $timezone int : временая зона
 */
$api->post(
    "/api/company/create",
    function () use ($App,$api)  {
        $Controller = new CreateCompanyController();
        $Controller->createCompanyAction($App,$api);
    }
);

/**
 * Изменить компанию
 * обновит компанию
 * method POST
 * @url /api/company/update
 * @param $company_name string : название компании
 * @param $company_image string : картинка в формате base64
 * @param $email string : email
 * @param $timezone int : временая зона
 */
$api->post(
    "/api/company/update",
    function () use ($App,$api)  {
        $Controller = new UpdateCompanyController();
        $Controller->updateCompanyAction($App,$api);
    }
);






/**
 * Получить магазин
 *
 * method POST
 * @url /api/department/get
 * @param $id int : ID отдела
 */

$api->post(
    "/api/department/get",
    function () use ($App)  {
        $Controller = new ViewDepartmentController();
        $Controller->getDepartmentOne($App);
    }
);



/**
 * Создать магазин
 * в администраторской панели
 * method POST
 * @url /api/department/create
 * @param $dep_name string : название отдела
 * @param $dep_image string : картинка отдела в base64
 */

$api->post(
    "/api/department/create",
    function () use ($App,$api)  {
        $Controller = new CreateDepTeamController();
        $Controller->createDepartmentAction($App,$api);
    }
);

/**
 * Обновить магазин
 * в администраторской панели
 * method POST
 * @url /api/department/update
 * @param $dep_name string : название отдела
 * @param $dep_image string : картинка отдела в base64
 */

$api->post(
    "/api/department/update",
    function () use ($App)  {
        $Controller = new UpdateDepTeamController();
        $Controller->updateDepartmentAction($App);
    }
);

/**
 * удалить магазин
 * в администраторской панели
 * method POST
 * @url /api/department/delete
 * @param $id int : ID отдела
 */

$api->post(
    "/api/department/delete",
    function () use ($App,$api)  {
        $Controller = new DeleteDepTeamController();
        $Controller->deleteDepartmentAction($App,$api);
    }
);



/**
 * Заказы
 */


$api->post(
    "/api/orders",
    function () use ($App)  {
        $Controller = new ViewOrderController();
        $Controller->getOrders($App);
    }
);


/**
 * Прибавление / удаление монет
 *
 * method POST
 * @url /api/balance/add
 */
$api->post(
    "/api/balance/add",
    function () use ($App,$api)  {
        $Controller = new UpdateUserController();
        $Controller->addRemoveOneBalance($App,$api,1);
    }
);
$api->post(
    "/api/balance/remove",
    function () use ($App,$api)  {
        $Controller = new UpdateUserController();
        $Controller->addRemoveOneBalance($App,$api,2);
    }
);

/**
 * Заказ товара в магазине
 *
 * method POST
 * @url /api/order/create
 * @param $product_id int : ID товара
 * @param $group int : посылать если груповой
 * @param $players array : список с ID игроков (для групового товара)
 */
$api->post(
    "/api/order/create",
    function () use ($App,$api)  {
        $Controller = new CreateOrderController();
        $Controller->createOrderAction($App,$api);
    }
);

$api->post(
    "/api/order/update",
    function () use ($App,$api)  {
        $Controller = new UpdateOrderController();
        $Controller->updateOrderAction($App,$api);
    }
);

/**
 * подтверждение групового товара другими игроками
 *
 * method POST
 * @url /api/order/group_accept
 * @param $id int : ID заказа
 * @param $status int : 2 - подтверждение игроком покупки 3 - отклонение игроком покупки
 *
 */
$api->post(
    "/api/order/group_accept",
    function () use ($App,$api)  {
        $Controller = new UpdateOrderController();
        $Controller->updateGroupOrderAction($App,$api);
    }
);






$api->post(
    "/api/clear",
    function () use ($App,$api)  {
        $Controller = new UpdateSettingsController();
        $Controller->clearAll($App,$api);

    }
);




/**
 * Получить товар
 * В администраторской панели
 * method POST
 * @url /api/product/get
 * @param $id : ID продукта
 *
 *
 */

$api->post(
    "/api/product/get",
    function () use ($App)  {
        $Controller = new ViewProductController();
        $Controller->getProductOne($App);
    }
);

/**
 * Создать товар
 * В администраторской панели
 * method POST
 * @url /api/product/create
 * @param $title : название продукта
 * @param $description : описание продукта
 * @param $image : preview original обрезанная оригинальная
 * @param $price : цена продукта
 * @param $group_status : груповой товар или нет
 */

$api->post(
    "/api/product/create",
    function () use ($App,$api)  {
        $Controller = new CreateProductController();
        $Controller->createProductAction($App,$api);
    }
);

/**
 * Изменить товар
 * В администраторской панели
 * method POST
 * @url /api/product/update
 * @param $id : ID продукта
 * @param $title : название продукта
 * @param $description : описание продукта
 * @param $image : preview original обрезанная оригинальная
 * @param $price : цена продукта
 * @param $group_status : груповой товар или нет
 */

$api->post(
    "/api/product/update",
    function () use ($App)  {
        $Controller = new UpdateProductController();
        $Controller->updateProductAction($App);
    }
);

/**
 * Удалить товар
 * В администраторской панели
 * method POST
 * @url /api/product/delete
 * @param $id : ID продукта
 */

$api->post(
    "/api/product/delete",
    function () use ($App,$api)  {
        $Controller = new DeleteProductController();
        $Controller->deleteProductAction($App,$api);
    }
);



/**
 * ACHIEVE add, update, delete formula
 */


/**
 * Получить уведомления
 *
 * method POST
 * @url /api/achieves
 * @param $dep_id : ID отдела
 */

$api->post(
    "/api/achieves",
    function () use ($App)  {
        $Controller = new ViewGemController();
        $Controller->getAchieves($App);
    }
);

/**
 * Получить уведомления
 *
 * method POST
 * @url /api/achieves/select
 *
 */

$api->post(
    "/api/achieves/select",
    function () use ($App)  {
        $Controller = new ViewGemController();
        $Controller->getAchieves($App,'all');
    }
);

/**
 * Получить уведомление по ID
 *
 * method POST
 * @url /api/achieve/get
 * @param $id : ID отдела
 */
$api->post(
    "/api/achieve/get",
    function () use ($App)  {
        $Controller = new ViewGemController();
        $Controller->getAchieveOne($App);
    }
);

/**
 * Создать уведомление
 * В администраторской панели
 * method POST
 * @url /api/achieve/create
 * @param $title : название уведомления
 * @param $description : описание уведомление
 * @param $image : картинка уведомления
 * @param $rating : рейтинг
 * @param $coins : манеты
 * @param $rank : статус
 * @param $days_limit : количество дней на выполнение
 * @param $dep_id : ID отдела
 * @param $formula_id : ID формула
 */
$api->post(
    "/api/achieve/create",
    function () use ($App,$api)  {
        $Controller = new CreateAchieveController();
        $Controller->createAchieveAction($App,$api);
    }
);


/**
 * Создать уведомление
 * В администраторской панели
 * method POST
 * @url /api/achieve/create
 * @param $id : ID уведомления
 * @param $title : название уведомления
 * @param $description : описание уведомление
 * @param $image : картинка уведомления
 * @param $rating : рейтинг
 * @param $coins : манеты
 * @param $rank : статус
 * @param $days_limit : количество дней на выполнение
 * @param $dep_id : ID отдела
 * @param $formula_id : ID формула
 */

$api->post(
    "/api/achieve/update",
    function () use ($App,$api)  {
        $Controller = new UpdateAchieveController();
        $Controller->updateAchieveAction($App,$api);
    }
);

/**
 * Удалить уведомление
 *
 * method POST
 * @url /api/achieve/delete
 * @param $id : ID уведомления
 */
$api->post(
    "/api/achieve/delete",
    function () use ($App,$api)  {
        $Controller = new DeleteAchieveController();
        $Controller->deleteAchieveAction($App,$api);
    }
);


$api->post(
    "/api/player_achieves/delete",
    function () use ($App)  {
        $Controller = new DeleteAchieveController();
        $Controller->deleteAchieveUserAction($App);
    }
);



/**
 * получить команду по ID
 *
 * method POST
 * @url /api/team/get
 * @param $id : ID задачи
 */
$api->post(
    "/api/team/get",
    function () use ($App)  {
        $Controller = new ViewTeamController();
        $Controller->getTeamOne($App);
    }
);



/**
 * создать команду
 * в администраторской панели
 * method POST
 * @url /api/team/create
 * @param $team_name string : название
 * @param $team_image string : картинка
 * @param $dep_id int : id отдела
 *
 */
$api->post(
    "/api/team/create",
    function () use ($App,$api)  {
        $Controller = new CreateDepTeamController();
        $Controller->createTeamAction($App,$api);
    }
);

/**
 * редактировать команду
 * в администраторской панели
 * method POST
 * @url /api/team/update
 * @param $team_name string : название
 * @param $team_image string : картинка
 * @param $dep_id int : id отдела
 *
 */
$api->post(
    "/api/team/update",
    function () use ($App,$api)  {
        $Controller = new UpdateDepTeamController();
        $Controller->updateTeamAction($App,$api);
    }
);

/**
 * удалить команду
 * в администраторской панели
 * method POST
 * @url /api/team/delete
 * @param $id int : id команды
 *
 */
$api->post(
    "/api/team/delete",
    function () use ($App,$api)  {
        $Controller = new DeleteDepTeamController();
        $Controller->deleteTeamAction($App,$api);
    }
);




/**
 * искать пользователя
 *
 * method POST
 * @url /api/user/search
 * @param $search string : искомое
 */

$api->post(
    "/api/user/search",
    function () use ($App,$api) {
        $Controller = new ViewUserController();
        $Controller->searchUser($App,$api);
    }
);



/**
 * get user by ID
 */
$api->get(
    "/api/user/{id:[0-9]+}",
    function ($id) use ($App)  {
        $Controller = new ViewUserController();
        $Controller->getUserById($App,$id);
    }
);















$api->handle();


