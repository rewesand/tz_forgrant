<?php 

namespace Admin\Controller;

class Products extends \Admin\Controller {
	
	private $products;
	
	public function __construct($request){
		parent::__construct($request);
		$this->products = new \Admin\Model\ModProducts();
	}

	public function actionIndex() {
		$this->content = '';
		if ($this->request->post()) {
			$data = $this->request->post();
			if (isset($data['action']) && $data['action'] == 'product_add') {
				unset($data['action']);
				//var_export($data);
				$prod_data = array(
					'title' => (string) $data['prod_name'],
					'description' => (string) $data['prod_desc'],
					'default_price' => (int) $data['prod_price'],
					'type_bind_price' => (int) $data['type_bind'],
				);
				if ($prod_id = $this->products->addProduct($prod_data)) {
					$this->request->redirect('/admin/products/edit/'.$prod_id);
				}
				else {
					$this->content = '<span class="red-text">Произшла ошибка, новый продукт не добавлен</span>';
				}
			}
		}
		$product_list = $this->products->getAll();
		$this->content .= \View::factory('Products/Index')->set('products', $product_list);
	}
	
	public function actionEdit(){
		//echo file_get_contents(DOCROOT.'index.html'); exit;
			if ($prod_id = (int) $this->request->param('id')){
			$this->content = '';
			if ($product = $this->products->getProduct($prod_id)) {
				if (($post = $this->request->post()) && $this->request->post('action')){
					$action = $post['action'];
					unset($post['action']);
					$post['id'] = $prod_id;
					if ($action == 'add_price') {
						if ($this->products->addCustomPrice($post)) {
							//todo: уведеомление об успешности выполнения операции
							$this->request->redirect($_SERVER['REQUEST_URI']);
						}
					}
					elseif ($action == 'product_change') {
						if ($this->products->changeProduct($post)) {
							//todo: уведеомление об успешности выполнения операции
							$this->content .= 'Изменения приняты<br />';
							$product = $this->products->getProduct($prod_id);
						}
					}
					elseif ($action == 'remove_price') {
						if ($price_id = (int) $this->request->post('price_id')){
							if ($this->products->removeCustomPrice($price_id)){
								$this->content = 'succcess';
								
							}
							else {
								$this->content = '{error: "not removed '.$price_id.'"}';
							}
							return true;
						}
					}
				}
				
				$custom_prices = $this->products->getCustomPrices($prod_id);
				
				$this->content .= \View::factory('Products/Edit')
						->set('product', $product)
						->set('custom_prices', $custom_prices);
			}
			else {
				$this->content = '<p>Указан неверный идентификатор продукта, или продукт был удален</p>';
			}
		}
		else {
			$this->content = '<p>Не указан идентификатор продукта</p>';
		}
	}
	
	public function actionAjaxGetProduct(){
		//Функция для AJAX запроса информации о продукте по выборочному дню или типу сортировки
		if ($prod_id = (int) $this->request->param('id')){
			$date = $this->request->post('date');
			if (isset($_POST['type_bind'])) {
				$type_bind = $this->request->post('type_bind');
			}
			if ($product = $this->products->getProduct($prod_id, $date, $type_bind)) {
				$this->auto_render = false;
				echo json_encode($product);
				return true;
			}
		}
	}
	
	public function actionAjaxGetPricePoints(){
		//Функция для AJAX запроса информации о продукте по выборочному дню или типу сортировки
		if ($prod_id = (int) $this->request->param('id')){
			if (isset($_POST['type_bind'])) {
				$type_bind = $this->request->post('type_bind');
			}
			if ($product = $this->products->getCustomPrice($prod_id, $date, $type_bind)) {
				$this->auto_render = false;
				echo json_encode($product);
				return true;
			}
		}
	}
	
} 
