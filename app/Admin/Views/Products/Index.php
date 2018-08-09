<?php if ($products) {?>
<div class="caption_block">
	<span class="caption">Продукция</span>
	<table class="admin_form_table">
		<tr class="thead">
			<td width="20px">&nbsp;</td>
			<td>Название продукта</td>
			<td>Описание (сокр.)</td>
			<td>Порядок выборки цены</td>
			<td align="right" width="70px">Цена (р.)</td>
			<td width="50px">&nbsp;</td>
		</tr>
		<?php 
		$even = true;
		foreach ($products as $product) { 
			$even = !$even;
		?>
		<tr<?=($even?' style="background: #eee;"':'');?>>
			<td><?=$product['id'];?></td>
			<td><a href="/admin/products/edit/<?=$product['id'];?>"><?=$product['title'];?></a></td>
			<td><?=\View::cutString($product['description'], 20);?></td>
			<td><?=($product['type_bind_price']?'по порядку':'короткий диапазон');?></td>
			<td><?=$product['default_price'];?></td>
			<td align="right"><a href="/admin/products/edit/<?=$product['id'];?>" class="button">Опции</a></td>
		</tr>
		<?php } ?>
	</table>
</div>
<?php } else { ?>
Продукты пока отсутствуют
<?php } ?>

<div class="caption_block">
	<span class="caption">Добавить продукт</span>
	<form action="" method="POST" id="form-add-product">
	<table class="admin_form_table" class="add-form">
		<tr class="thead">
			<td>Название продукта</td>
			<td>Описание</td>
			<td>Порядок выборки цены</td>
			<td align="right" width="70px">Цена (р.)</td>
			<td width="50px">&nbsp;</td>
		</tr>
		<tr>
			<td><input type="text" name="prod_name" class="prod-name" value="" /></td>
			<td><textarea name="prod_desc" height="2"></textarea></td>
			<td>
				<select name="type_bind">
					<option value="0">короткий диапазон</option>
					<option value="1">по порядку</option>
				</select>
			</td>
			<td><input type="text" name="prod_price" class="prod-price" value="" /></td>
			<td><button type="submit" name="action" value="product_add" class="">Добавить</button></td>
		</tr>
	</table>
	</form>
</div>

<script type='text/javascript'>

  $("#form-add-product").submit(function(){
	  var err = '';
	  var prod_name = $("#form-add-product .prod-name").val().trim();
	  if (prod_name == '') {
		  err = 'название';
	  }
	  var price = $("#form-add-product .prod-price").val().trim();
	  if (price == '') {
		  if (err) err += ' и ';
		  err += 'цена';
	  }
	  if (err != ''){
		  alert('Не указано: '+err);
		  return false;
	  }
  });
</script>