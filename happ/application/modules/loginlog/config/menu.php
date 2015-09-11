<?php
$config = array();
$ri = HC_Lib::ri();
if( ! $ri ){
	$config[ USER_HC_MODEL::LEVEL_ADMIN . '/conf/loginlog' ] = array(
		'title'	=> 'Login Log',
		'icon'	=> 'list',
		'link'	=> 'loginlog/index',
		);
}
?>