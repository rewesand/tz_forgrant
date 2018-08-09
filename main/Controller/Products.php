<?php 

namespace Controller;

class Products extends \FrontController {

	public function actionIndex() {
		$products = new \Model\Products();
		$this->content = \View::factory('Products/Index')->set('products', $products);
	}
} 
