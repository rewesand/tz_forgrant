<?php

Class Auth {
	
	protected $mode = 'file';
	protected $access_file = 'admin_acces';
	
	private $_is_auth = false;
	private $_user = NULL;
	
	static $_instance = NULL;
	
	function __construct(){
		$this->autoLogin();
		return $this;
	}
	
	static function instance(){
		if (self::$_instance === NULL){
			return self::$_instance = new Auth();
		}
		else {
			return self::$_instance;
		}
	}
	
	static function isAuth(){
		return self::instance()->_is_auth;
	}
	
	function login($login, $password){
		
		if ($this->_is_auth){
			return $this->_is_auth;
		}
		else {
			$login = (string) $login;
			$password = (string) $password;
			$user_agent = \Request::getUserAgent();
			
			if ($this->checkPassword($login, $password)){
				
				$user_l = md5($login);
				$user_p = md5($password);
				$user_a = md5($user_agent);
				$user_c = md5($user_l.$user_p);
				
				$user_h = md5($user_c.$user_a);
				
				setcookie('user_c', $user_c, time()+60*60*24*30, '/');
				setcookie('user_h', $user_h, time()+60*60*24*30, '/');
				
				return $this->_is_auth = true;
			}
		}
		$this->logout();
	}
	
	private function checkPassword($login, $password) {
		
		$user_l = md5($login);
		$user_p = md5($password);
		$user_c = md5($user_l.$user_p);
		//todo: Добавить аутентификацию для БД
		if ($access = \Config::load($this->access_file)){
			if (isset($access[$user_c])){
				$p = $access[$user_c]['password'];
				$e = $access[$user_c]['email'];
				if (($p == $user_p) && (md5($e) == $user_l)){
					return true;
				}
			}
		}
		return false;
	}
	
	private function checkHash($user_c, $user_h){
		$user_c = (string) $user_c;
		//todo: Добавить аутентификацию для БД
		if ($access = \Config::load($this->access_file)){
			if (isset($access[$user_c])){
				$p = $access[$user_c]['password'];
				$em = md5($access[$user_c]['email']);
				$user_a = md5(\Request::getUserAgent());
				$h = md5(md5($em.$p).$user_a);				
				if ($user_h == $h){
					return true;
				}
			}
		}
	}
	
	private function autoLogin(){
		if (isset($_COOKIE['user_c']) && isset($_COOKIE['user_h'])){
			$user_c = $_COOKIE['user_c'];
			$user_h = $_COOKIE['user_h'];
			
			if ($this->checkHash($user_c, $user_h)){
				return $this->_is_auth = true;
			}
			else {
				$this->logout();
			}
		}
	}
	
	public function logout(){

		setcookie('user_c', NULL, time()-100, '/');
		setcookie('user_h', NULL, time()-100, '/');
		unset($_COOKIE['user_c']);
		unset($_COOKIE['user_h']);
			
		$this->_is_auth = false;
		return true;
	}
	
}