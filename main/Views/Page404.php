<?php 

	echo \View::factory('Front')
		->set('content', '<center><p><h1>404 - Not found</h1><br />Произошла ошибка, страница недоступна</p></center>')
		->render();	

?>