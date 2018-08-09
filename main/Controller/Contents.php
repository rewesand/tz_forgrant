<?php 

namespace Controller;

class Contents extends \FrontController {

	public function actionIndex() {
		$this->content = '<p class="text">Интернет контент</p>';
	}
} 
