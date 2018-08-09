<?php 

namespace Model;

class Products {
	
	public function __construct(){
		//здес пишем все что касается инициализации
	}
	
	/*********
	Получаем все producsts с ценами
	Цены две, default_price и custom_price
	custom_price формируется посредством подтягивния из таблицы product_custom_prices
	и выборке актуальной согласно правил, типу которых задется в type_bind_price
		type_bind_price:
			0: Приоритетнее цена с меньшим периодом действия 
				(цена на 1 месяц приоритетнее цены установленной на 1 год)
			1: Приоритетнее цена, установленная позднее (используя сортировку)
	естесно custom_price будет NULL если в таблице product_custom_prices нет соответсвий или диапазона под текущую дату
	**************/
	
	public function getAll($time = false) {
		$res = false;
		if ($time) {
			if (is_int($time)){
				$select_time = $time;
			}
			elseif (is_string($time)){
				$select_time = strtotime($time);
			}
		} else {
			$select_time = TIME_NOW;
		}
		
		$qr = 'SELECT *, IF (prod.type_bind_price = 0,
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
			   FROM products as prod';
		
		if ($req = \DB::query($qr))
		{
			$res = $req->fetchAll();
		}
		
		return $res;
	}
	
	/*********
	Получаем один продукт 
		getProduct(int $prod_id, $time = false)
		- prod_id - Идентификатор продукта, числовое значение
		- time - для получения цены на определенный день, не обязательный, понимает как строку даты так и числовое абсолютное UNIX
	Цены две, default_price и custom_price
	custom_price формируется посредством подтягивния из таблицы product_custom_prices
	и выборке актуальной согласно правил, типу которых задется в type_bind_price
		type_bind_price:
			0: Приоритетнее цена с меньшим периодом действия 
				(цена на 1 месяц приоритетнее цены установленной на 1 год)
			1: Приоритетнее цена, установленная позднее (используя сортировку)
	естесно custom_price будет NULL если в таблице product_custom_prices нет соответсвий или диапазона под текущую дату
	**************/
	
	public function getProduct(int $prod_id, $time = false, int $type_bind = -1){
		if ($prod_id = (int) $prod_id) {
			
			if ($time) {
				if (is_int($time)){
					$select_time = $time;
				}
				elseif (is_string($time)){
					$select_time = strtotime($time);
				}
			} else {
				$select_time = TIME_NOW;
			}
			
			if ($type_bind > 0) {
				$type_bind_price = $type_bind;
			}
			else {
				$type_bind_price = 'prod.type_bind_price';
			}
			
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
			   WHERE prod.id = '.$prod_id.'
			   LIMIT 1';
			
			if ($req = \DB::query($qr))
			{
				if ($res = $req->fetch()){
					return $res;
				}
			}
		}
		return false;
	}
	
	public function getCustomPrices($prod_id){
		if ($prod_id = (int) $prod_id) {
			
			$qr = 'SELECT * FROM product_custom_prices WHERE product_id = '.$prod_id;
			
			if ($req = \DB::query($qr))
			{
				if ($res = $req->fetchAll()){
					return $res;
				}
			}
		}
		return false;		
	}
	
}
	
?>