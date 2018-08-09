<a href="/admin/products/"><strong>Продукция</strong></a> >> <span><?=$product['title'];?></span>
<div class="caption_block" style="width: 500px">
	<span class="caption">Опции продукта</span>
	<form action="" method="POST" class="form-change-production">
	<table class="admin_form_table">
		<tr>
			<td><b>Название продукта</b></td>
			<td><input type="text" name="title" value="<?=$product['title'];?>" /></td>
		</tr>
		<tr>
			<td><b>Описание продукта</b></td>
			<td>
				<textarea name="description" style="height: 50px; width: 100%;"><?=$product['description'];?></textarea>
			</td>
		</tr>
		<tr>
			<td><b>Порядок выборки цены</b></td>
			<td>
				<select name="type_bind_price">
					<option value="0" <?=($product['type_bind_price'] == 0)?' selected':'';?>>короткий диапазон</option>
					<option value="1" <?=($product['type_bind_price'] == 1)?' selected':'';?>>по порядку</option>
				</select>
			</td>
		</tr>
		<tr>
			<td><b>Цена по умолчанию</b></td>
				<td><input type="text" name="default_price" value="<?=$product['default_price'];?>" /></td>
		</tr>
		<tr>
			<td><b>Цена на сегодня</b></td>
			<td><?=$product['custom_price']?$product['custom_price']:$product['default_price'];?></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><button type="submit" name="action" value="product_change" class="">Сохранить</button></td>
		</tr>
	</table>
	</form>
</div>
<?php if ($custom_prices) {?>
<br />
<div class="caption_block" style="width: 500px">
	<span class="caption">Поведение цены</span>
	<div>
		<table class="admin_form_table" style="width: 300px;">
			<tr><td class="label-fix">Цена на:</td><td><input type="text" value="<?=date('d-m-Y', TIME_NOW);?>" id="preview_date" /></td></tr>
			<tr><td class="label-fix">При сортировке:</td>
				<td>
					<select name="type_bind_price" id="type_bind_preview">
						<option value="0" <?=($product['type_bind_price'] == 0)?' selected':'';?>>короткий диапазон</option>
						<option value="1" <?=($product['type_bind_price'] == 1)?' selected':'';?>>по порядку</option>
					</select>
				</td>
			</tr>
			<tr><tr><td class="label-fix">Составляет:</td><td><b id="priview_price_value"><?=$product['custom_price'];?></b></td></tr>
		</table>
	</div>
	<?php if (count($custom_prices)>1) { ?>
	<hr />
	<div class="graphic" title="Ooops, что то пошло не так. Здесь должны были быть графики по двум правилам определения цен.:(">
		<br />
		<!-- Ooops, что то пошло не так. Здесь должны были быть графики по двум правилам определения цен.:( -->
	</div>
	<?php } ?>
</div>
<br />
<div class="caption_block" style="width: 500px;">
	<span class="caption">Дополнительные цены</span>
	<table class="admin_form_table">
		<tr class="thead">
			<td>Дата начала</td>
			<td>Дата завершения</td>
			<td align="right" width="80px">Цена (рю)</td>
			<td width="10px">&nbsp;</td>
		</tr>
		<?php 
		$even = true;
		foreach ($custom_prices as $price) { 
			$even = !$even;
		?>
		<tr<?=($even?' style="background: #eee;"':'');?>>
			<td><?=date('d-m-Y',$price['start']);?></td>
			<td><?=($price['end']?date('d-m-Y', $price['end']):'--');?></td>
			<td align="right"><?=$price['custom_price'];?></td>
			<td><a href="" onClick="return remove_filter(<?=$price['id'];?>)" title="Удалить"><img src="/img/icons/delete.png" height="16px" /></a></td>
		</tr>
		<?php } ?>
	</table>
</div>
<?php } else { ?>
<br />
<div class="caption_block" style="width: 500px">
	<span class="caption">Поведение цены</span>
	<span>Дополнительные цены отсутствуют</span>
</div>
<?php } ?>
<br />
<div class="caption_block" style="width: 500px;">
	<span class="caption">Добавить дополнительную цену</span>
	<form action="" method="POST" id="form-add-price">
	<table class="admin_form_table">
		<tr class="thead">
			<td>Дата начала</td>
			<td>Дата завершения</td>
			<td align="left" colspan="2">Цена (руб)</td>
		</tr>
		<tr>
			<input type="hidden" name="poduct_id" value="<?=$product['id'];?>" />
			<td><input type="text" name="start" value="" id="price-start" style="width: 80px;" placeholder="30-02-2021" /></td>
			<td><input type="text" name="end" value="0" id="price-end" style="width: 80px;" placeholder="" /></td>
			<td><input type="text" name="custom_price" id="custom-price" value="" style="width: 70px;" /></td>
			<td align="right"><button type="submit" name="action" value="add_price" class="">Добавить</button></td>
		</tr>
		<tr><td colspan="4"><span style="font-size: 11px;">*Дата завершения = 0, если новая цена не имеет срока завершения</span></td></tr>
	</table>
	</form>
</div>

<link href="/css/jquery-ui.css" rel="stylesheet">
<script src="/js/jquery-ui.js" type='text/javascript'></script>

<script type='text/javascript'>

var get_date = '<?=date('d-m-Y',TIME_NOW);?>';
var get_bind_type = -1;

$( function() {
	  var dateFormat = "dd-mm-yy",
      from = $( "#form-add-price #price-start" )
        .datepicker({
          defaultDate: "+1w",
          changeMonth: true,
		  changeYear: true,
          //numberOfMonths: 3, - отображает кол-во блоков месяцев
		  dateFormat: 'dd-mm-yy'
        })
        .on( "change", function() {
          to.datepicker( "option", "minDate", getDate( this ) );
        }),
      to = $( "#form-add-price #price-end" ).datepicker({
        defaultDate: "+1w",
        changeMonth: true,
		changeYear: true,
        //numberOfMonths: 3,
		dateFormat: 'dd-mm-yy'
      })
      .on( "change", function() {
        from.datepicker( "option", "maxDate", getDate( this ) );
      });
 
    function getDate( element ) {
      var date;
      try {
        date = $.datepicker.parseDate( dateFormat, element.value );
      } catch( error ) {
        date = null;
      }
 
      return date;
    }
  });
  
  $( "#preview_date" ).datepicker({
	changeMonth: true,
	changeYear: true,
	dateFormat: 'dd-mm-yy'
  }).change(function(){
	  var date = $(this).val();
	  update_preview(date);
  });
  
  $('#type_bind_preview').change(function(){
	  var type = $(this).val();
	  update_preview(false, type);
  });
  
function update_preview(date = false, bind_type = false){
	if (date != false) get_date = date;
	if (bind_type !== false	) get_bind_type = bind_type;
	
	$.ajax({
		url: "/admin/products/ajaxgetproduct/<?=$product['id'];?>?ajax=1'",
		type: 'POST',
		data: {'date':get_date, 'type_bind': get_bind_type},
		success: function(r){
			$('#priview_price_value').html(r.custom_price);
		},
		dataType: "json",
	});
}
  
  
  $("#form-add-price").submit(function(){
	  var err = '';
	  var start_date = $("#form-add-price #price-start").val().trim();
	  if (start_date == '') {
		  err = 'дата начала';
	  }
	  var price = $("#form-add-price #custom-price").val().trim();
	  if (price == '') {
		  if (err) err += ' и ';
		  err += 'цена';
	  }
	  if (err != ''){
		  alert('Не указано: '+err);
		  return false;
	  }
  });
  
	function remove_filter(price_id)
	{
		if (confirm('Вы действительно хотите удалить фильтр?'))
		{
			$.post("/admin/products/edit/<?=$product['id'];?>?ajax=1'",
				{'price_id':price_id, 'product_id':<?=$product['id'];?>, 'action': 'remove_price'},
				function(r) {
					if (r == 'succcess') {
						location.reload();
					}
				}
			);
		}
		return false;
	}
	
	function remove_filter(price_id)
	{
		if (confirm('Вы действительно хотите удалить фильтр?'))
		{
			$.post("/admin/products/edit/<?=$product['id'];?>?ajax=1'",
				{'price_id':price_id, 'product_id':<?=$product['id'];?>, 'action': 'remove_price'},
				function(r) {
					if (r == 'succcess') {
						location.reload();
					}
				}
			);
		}
		return false;
	}
	
</script>