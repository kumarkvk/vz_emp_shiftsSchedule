<?php
if( ! file_exists(dirname(__FILE__) . '/db.php') )
{
	echo "<p><b>db.php</b> file doesn't exist! Please rename the sample <b>db.rename_it.php</b> to <b>db.php</b>, then edit your MySQL database information there.";
	exit;
}

include_once( dirname(__FILE__) . '/db.php' );
$GLOBALS['NTS_APPPATH'] = dirname(__FILE__) . '/application';
$GLOBALS['NTS_APP'] = 'shiftexec';

if( defined('NTS_DEVELOPMENT') )
	require( NTS_DEVELOPMENT . '/application/index_ci.php' );
else
	require( dirname(__FILE__) . '/happ/application/index_ci.php' );