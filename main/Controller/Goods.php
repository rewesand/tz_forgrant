<?php 

namespace Controller;

class Goods extends \FrontController {

	public function actionIndex() {
		
		$this->content = '<p class="text">На данной стронице находятся товары различных категорий</p>';
		
	}
} 
