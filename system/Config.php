<?php

Class Config {
	
	private static $_configs = array();

	static function load($name = null) {
		$name = (string) $name;
		if (!$name)
			return false;
		elseif (isset(self::$_configs[$name]))
			return self::$_configs[$name];
		elseif ($file = findFile('Config',$name))	{
			$res = include($file);
			$_configs[$name] = $res;
			return $res;
		}
		else {
			throw new Exception('Not find config file "'.$name.'"');
		}
	}
}

?>