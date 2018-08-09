<?php 

namespace Controller;

class Member extends \FrontController {
	
	public function actionIndex(){
		$this->request->redirect('/member/login');
		return true;
	}
	
	public function actionLogin(){
		/*
		$login = 'admin';
		$pass = '123';
		echo $user_l = md5($login);
		echo '<br />';
		echo $user_p = md5($pass);
		echo '<br />';
		echo $user_c = md5($user_l.$user_p); 
		echo '<br />';
		exit;
		*/
		if ($this->request->post()) {
			if (($login = $this->request->post('login')) && ($pass = $this->request->post('password'))){
				if ($res = \Auth::instance()->login($login, $pass))
				{
					if ($return_url = $this->request->post('return_url')) {
						$this->request->redirect($return_url);
					}
					else {
						$this->request->redirect('/admin/');
					}
				}
			}
		}
		$this->content = \View::factory('Member/Login');
	}
	
	public function actionLogout(){
		if (\Auth::instance()->logout()){
			$this->request->redirect('/');
		}
		else {
			$this->content = 'Произошла ошибка, почему-то невозможно вылогиниться.';
		}
	}	
}