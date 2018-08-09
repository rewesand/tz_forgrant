<div id="products-content">

<h5>Продукция для школьников</h5>

<?php if ($prods = $products->getAll()) { ?>
	<table class="table-products" >
		<tr class="thead">
			<td width="300px">Название продукта</td>
			<td>Описание (сокр.)</td>
			<td align="right" width="70px">Цена (р.)</td>
		</tr>
		<?php 
		$even = true;
		foreach ($prods as $product) { 
			$even = !$even;
		?>
		<tr<?=($even?' class="even"':'');?>>
			<td><?=$product['title'];?></td>
			<td><?=nl2br($product['description']);?></td>
			<td><?=$product['custom_price']?$product['custom_price']:$product['default_price'];?></td>
		</tr>
		<?php } ?>
	</table>
<?php } else { ?>
	На данный момент не имеется продукции для предложения.
<?php } ?>

</div>