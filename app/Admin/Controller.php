<?php 

namespace Admin;

class Controller extends \FrontController {

	public function __construct($request) {
		parent::__construct($request);
		if (!\Auth::isAuth()){
			$this->request->redirect('/member/login');
		}
	}
	public function after() {
		if ($this->auto_render === TRUE && !$this->request->isAjax()){
			$this->content = \View::factory('AdminMain')->set('content', $this->content);
			$this->main_menu_show = false;
		}
		parent::after();		
	}
} 
