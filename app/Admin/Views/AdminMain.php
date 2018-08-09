<link href="/css/admin.css?rev=<?=rand();?>" rel="stylesheet" type="text/css" media="all">
<table class="admin_main_page" width="100%">
	<tr>
		<td class="leftside">
			<span class="title">Admin panel</span>
			<ul class="vertical_menu">
				
				<li><a href="/admin/" class="<?=\Request::initial()->controller()=='Admin\Controller\Products'?'active':'';?>">Продукция</a>
				<li><a href="/admin/goods" class="<?=\Request::initial()->controller()=='Admin\Controller\Goods'?'active':'';?>">Товары</a>
				<li><a href="/admin/contents" class="<?=\Request::initial()->controller()=='Admin\Controller\Contents'?'active':'';?>">Интернет контент</a>
				
			</ul>
		</td>
		<td class="admin_content_cell">
			<?=$content;?>
		</td>
	</tr>
</table>