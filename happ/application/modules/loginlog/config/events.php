<?php
$config['user.after_login'][] = create_function( '$object', '
	$loginlog = HC_App::model("loginlog");
	$loginlog->log( $object );
');
