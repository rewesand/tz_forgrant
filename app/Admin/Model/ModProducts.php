<?php 

namespace Admin\Model;

class ModProducts extends \Model\Products {
	
	public function __construct(){
		parent::__construct();
		if (!\Auth::isAuth()){
			\Request::initial()->redirect('/');
		}
	}
	
	public function addProduct($data){
		//todo: добавить верификацию данных
		if (isset($data['title'])) {
			$product['title'] = (string) $data['title'];
			$product['description'] = (string) isset($data['description'])?$data['description']:'';
			$product['default_price'] = (int) isset($data['default_price'])?$data['default_price']:0;
			$product['type_bind_price'] = (int) isset($data['type_bind_price'])?$data['type_bind_price']:0;
			if ($id = \DB::insert('products', $product)){
				return $id[0];
			}
		}
		return false;
	}
	
	public function changeProduct($data){
		if (isset($data['id']) && $prod_id = (int) $data['id']) {
			if ($product = $this->getProduct($prod_id)){
				if (isset($data['title'])) $product['title'] = (string) $data['title'];
				if (isset($data['description'])) $product['description'] = (string) $data['description'];
				if (isset($data['default_price'])) $product['default_price'] = (int) $data['default_price'];
				if (isset($data['type_bind_price'])) $product['type_bind_price'] = (int)$data['type_bind_price'];
				if (\DB::update('products', $product, 'id = '.$prod_id)){
					return true;
				}
			}
		}
		return false;
	}
	
	public function addCustomPrice($data){
		if (isset($data['id']) && $prod_id = (int) $data['id']) {
			if ($product = $this->getProduct($prod_id)){
				$price['product_id'] = $prod_id;
				if (isset($data['start'])) $price['start'] = strtotime($data['start']);
				if (isset($data['end']) && $data['end']) $price['end'] = strtotime($data['end']);
				if (isset($data['custom_price'])) $price['custom_price'] = (int)$data['custom_price'];
				if ($res = \DB::insert('product_custom_prices', $price)){
					return $res[0];
				}
			}
		}
		return false;
	}
	
	public function removeCustomPrice(int $price_id) {
		if ($price_id) {
			if (\DB::query('DELETE FROM product_custom_prices WHERE id = '.$price_id)) {
				return true;
			}
		}
		return false;
	}
	
	public function getCustomPricePoints($prod_id){
		if (($prod_id = (int) $prod_id) && ($product = $this->getProduct($prod_id))) {
			$qr = 'SELECT *, IF ('.$type_bind_price.' = 0,
				(
					SELECT price.custom_price
					FROM product_custom_prices as price
					WHERE price.product_id = prod.id AND (price.end >= '.$select_time.' OR price.end = 0) AND price.start <= '.$select_time.'
					ORDER BY abs((IF (price.end > 0, price.end, 2147483647))-price.start) ASC LIMIT 1
				),
				(
					SELECT price.custom_price
					FROM product_custom_prices as price
					WHERE price.product_id = prod.id AND (price.end >= '.$select_time.' OR price.end = 0) AND price.start <= '.$select_time.'
					ORDER BY price.start DESC LIMIT 1
				)
			) as custom_price
			FROM products as prod 
			WHERE prod.id = '.$prod_id.'';
			   
			if ($req = \DB::query($qr))
			{
				if ($res = $req->fetch()){
					return $res;
				}
			}
		}
	}
	
}
	
?>