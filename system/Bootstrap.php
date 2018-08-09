<?php

/*
Класс аля бутстраб для движка
Туточки происходит регистрация autoloader, error handler-a
и имеются функции 
findFile($dir, $file)
mark_debug_time($str) str-не обязательно
*/

if (version_compare(phpversion(), '5.5.0', '<') == true) { die ('>PHP5.5 Only'); }

session_start();

static $_find_routes = false;

if (!defined('SITE_MODE')) define('SITE_MODE', 'DEV');

if (!defined('DOCROOT')) define('DOCROOT', realpath(__DIR__.'/../').DS);
if (!defined('SYSPATH')) define('SYSPATH', realpath(__DIR__).DS);

define('TIME_NOW', time());

//Eсли не объявлена константа DEBUG_MODE то создаем ее
if (!defined('DEBUG_MODE')) {
	if (isset($_GET['debug_mode'])) {
		if($_GET['debug_mode'] === 'enable'){
			$_SESSION['DEBUG_MODE'] = true;
			define('DEBUG_MODE', true);
		}
		elseif ($_GET['debug_mode'] === 'disable') {
			unset($_SESSION['DEBUG_MODE']);
			define('DEBUG_MODE', false);
		}
	}
	else {
		if (isset($_SESSION['DEBUG_MODE']) && ($_SESSION['DEBUG_MODE'] === true)){
			define('DEBUG_MODE', true);
		}
		else 
			define('DEBUG_MODE', false);
	} 
}

function autoloader($class_name) {
		
	$dir = '';
	
	$elements = explode('\\', $class_name);
	if (($last_element = count($elements) - 1) > 0){
		$class_name = $elements[$last_element];
		unset($elements[$last_element]);
		$dir = implode('/',$elements);
	}
	else {
		$class_name = $elements[$last_element];
	}
	
	try {
		if ($file = findFile($dir,$class_name)){
			return require_once ($file);
		}
		return false;
	}
	catch (Exception $e){
		throw new Exception($e);
	}
}

function findFile($dir, $file){
	
	$path = $dir.DS.$file.EXT;
	
	if ($pathes = \Bootstrap::getClassesPathes()){
		foreach ($pathes as $find_dir){
			if (file_exists($find_dir['realpath'].$path)) {
				return $find_dir['realpath'].$path;
				break;
			}
		}
	}
	
	if (file_exists(SYSPATH.$path)) {
		return SYSPATH.$path;
	}
	
	return false;

}

function dbg_last_error() {
	if ($error = error_get_last() AND in_array($error['type'], array(E_PARSE, E_ERROR, E_USER_ERROR))){	
		// Clean the output buffer
		ob_get_level() AND ob_clean();
		// Start an output buffer
		ob_start();
		if (SITE_MODE == 'DEV'){
			$error_view = 'error';
		}
		else {
			$error_view = 'Page404';
		}
		if ($view_file = findFile('Views', $error_view)){
			extract($error);
			$code = 500;
			$trace = debug_backtrace();
			unset($trace[0]);
			include $view_file;
		}
		else {
			throw new ErrorException($error, $code, 0, $file, $line);
		} 
		// Display the contents of the output buffer
		echo ob_get_clean();
		// Shutdown now to avoid a "death loop"
		exit(1);
	}
	else {
		show_debug_time();
	}

}

function error_dispetcher($code, $error, $file = NULL, $line = NULL, $args = NULL){
	if (error_reporting() & $code){
		if (SITE_MODE == 'DEV'){
			$error_view = 'error';
		}
		else {
			$error_view = 'Page404';
		}
		if ($view_file = findFile('Views', error_view)){
			$type = 1;
			$message = $error;
			$trace = debug_backtrace();
			unset($trace[0]);
			include $view_file;
			exit;
		}
		else {
			throw new ErrorException($error, $code, 0, $file, $line);
		}
	}
	return TRUE;
}

//Назначаем функцию обработчик ошибок
set_error_handler('error_dispetcher');

//Если вдруг произошла фатальная ошибка, все равно пробуем перехватить ошибки
register_shutdown_function("dbg_last_error");

// Загрузка классов «на лету»
spl_autoload_register('autoloader');

function showErrors($se = false) {
	$se = (boolean) $se;
	if ($se === true) {
		define('DEBUG', TRUE);
		error_reporting(E_ALL | E_STRICT);
		ini_set('display_errors', 1);
	}
	else
	{
		define('DEBUG', FALSE);
		error_reporting(FALSE);
		ini_set('display_errors', false);
	}
}

function debug($value){
	GLOBAL $dump_info;
	$dump_info .= '<pre>'.var_export($value, TRUE).'</pre>';
}

function show_debug_time()
{
	GLOBAL $dump_times;
	
	if (DEBUG_MODE === true) {
		
		if ($dump_times && count($dump_times)>1){
			$dmp = '';
			
			$rest = count($dump_times);
			$dmp .= 'All run times: '.($dump_times[$rest-1]['time'] - $dump_times[0]['time']). '<br />';
			$dmp .= '<table style="color: white;">';
			
			foreach ($dump_times as $key => $val) 
			{
				$dmp .= '<tr><td align="right">'.$val['title'].': </td><td>'.$val['relative']."</td></tr>";
			}
			
			echo '<hr />';
			echo '<pre style="color: white; background: black; text-align: left;">'.$dmp.'</pre>';
			echo '<pre style="color: white; background: black; text-align: left;">Memory usage:';
			echo memory_get_usage();
			echo '</pre>';
		}
	}
	return false;
}

function mark_debug_time($str = false)
{	
	GLOBAL $dump_times;		
	if (DEBUG_MODE === true) {
		$time = microtime(true)*1000;
		$count = is_array($dump_times)?count($dump_times):0;
		
		if ($str != false) $mark = (string) $str; else $mark = $count+1;	
		
		$rel = $count>0?($time - $dump_times[$count-1]['time']):false;
		$dump_times[$count] = array('title' => $mark, 'time' => $time, 'relative' => $rel);
	}
	return false;
}

if (SITE_MODE == 'DEV') {
	showErrors(true);
} else {
	showErrors(false);
}

Class Bootstrap {
	
	private static $_find_pathes = false;

	public static function addClassesPath(string $path_name, string $path_src) {
		if (($path_name) && ($path_src)){
			$path = DOCROOT . trim($path_src, ' /') . DS;
			self::$_find_pathes[$path_name]['dir'] = $path_src;
			self::$_find_pathes[$path_name]['realpath'] = $path;
		}
	}
	
	public static function getClassesPathes() {
		return self::$_find_pathes;
	}

}

?>