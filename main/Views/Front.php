<!doctype html>
<html>
<head>

<title><?=$page_title_prefix?$page_title_prefix:'';?></title>

<?=$meta_description?'<meta name="Description" content="'.$meta_description.'" />':'';?>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php if (\Request::isMobile()) { ?>
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
<?php } ?>

<link rel="shortcut icon" href="/favicon.ico" />

<?php if (SITE_MODE === 'DEV') {?>
<script src='/js/jquery.js?rev=<?=rand();?>' charset='utf-8' type='text/javascript'></script>
<link href="/css/style.css?rev=<?=rand();?>" rel="stylesheet" type="text/css" media="all">
<?php } else { ?>
<script src='/js/jquery.js' charset='utf-8' type='text/javascript'></script>
<link href="/css/style.css" rel="stylesheet" type="text/css" media="all">
<?php } ?>

</head>

<body>

<div id="page">
  <div id="wrap">
    <header id="header">    
        <div class="logo">
			<a href="/"><img src="/img/logo.png?v=2" width="100px" /></a>
		</div>
		<span class="slogan">best in quality</span>
		<div class="member-block">
			<?php if (\Auth::isAuth()){ ?>
				<a href="/admin/" class="sign-link">Управление контентом</a>
				<a href="/member/logout" class="sign-link sign-out">Выйти</a>
			<?php } else { ?>
				<a href="/member/login" class="sign-link sing-in">Войти</a>
			<? } ?>
		</div>
		<div class="clear"></div>
    </header>
    
    <main id="main">
		<?php if (isset($main_menu_show) && $main_menu_show === true) { ?>
		<div id="main-menu">	
			<ul class="main-menu-items">
				<li<?=($request->controller()=='\Controller\Products')?' class="active"':'';?>><a href="/">Продукция</a></li>
				<li<?=($request->controller()=='\Controller\Goods')?' class="active"':'';?>><a href="/goods/">Товары</a></li>
				<li<?=($request->controller()=='\Controller\Contents')?' class="active"':'';?>><a href="/contents/">Интернет контент</a></li>
			</ul>
		</div>
		<div class="clear"></div>
		<?php } ?>
		<div class="content">
			<?=$content;?>
		</div>
		
	</main>
	
	<footer id="footer">
		<div id="copyright">Powered and created by BIQ © 2018</div>
	</footer>
  </div><!-- #wrap -->
</div><!-- #page -->

</body>
</html>