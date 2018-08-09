<?php

return array
(

	'admin' => array(
		'uri' => 'admin(/<controller>(/<action>(/<id>)))',
		'default' => array('app'=>'Admin', 'controller' => 'Products', 'action'=>'Index')
	),
	
	'index' => array(
		'uri' => '(<controller>(/<action>(/<id>)))',
		'default' => array('controller' => 'Products', 'action'=>'Index')
	),
);