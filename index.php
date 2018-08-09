<?php

//Состояние системы, может быть два: DEV и PROD
//В режиме DEV, отображение ошибог происходит через дебагер
//В режиме PROD, отображение ошибок не происходит и вместо этого отображается вьюха Views\Page404
define('SITE_MODE', 'DEV');
//DEBUG_MODE можно прописать жестко, задав глобальную константу 
//    define('DEBUG_MODE', true)
//или же это можно сделать прописав однократно в адресной строке
// ?debug_mode = enable - включает
// ?debug_mode = enable - выключает
//define('DEBUG_MODE', true);

define('EXT', '.php');
define('DS', DIRECTORY_SEPARATOR);

define('DOCROOT', realpath(__DIR__) . DS);

require_once (DOCROOT.'system/Bootstrap.php');

\Bootstrap::addClassesPath('MAIN_DIR', 'main/');
\Bootstrap::addClassesPath('APP_DIR', 'app/');
\Bootstrap::addClassesPath('LIB_DIR', 'lib/');

//Инициализируем класс обработки запроса
$request = new \Request();

\Request::$base_url = '/';
			
//Загружаем конфиг файл с роутами \main\Config\routes.php
$routes = \Config::load('routes');

//базовая директрия рабочего урла.
//пример example.com/ - $base_url = '/';
//пример example.com/cus/ - $base_url = 'cus/';
\Request::$base_url = '/';

//и передаем ему конфиг роутов
$request->addRoutes($routes);

//выполняем запрос
$response = $request->execute();

//и отображаем результат выполнения запроса
echo $response->body();

/* */