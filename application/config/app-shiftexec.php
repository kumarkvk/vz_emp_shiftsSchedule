<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* base stuff */
$config['nts_app_title'] = 'ShiftExec';
$config['nts_app_url'] = 'http://www.shiftexec.com';

$config['nts_promo_url'] = 'http://www.shiftexec.com/order/';
$config['nts_promo_title'] = 'ShiftExec Pro';

$config['nts_track_setup'] = '2:2';

$config['modules'] = array(
	'auth',
//	'license',
	'conf',
//	'wordpress',
	'wall',
	'conflicts',
//	'shift_groups',
//	'trades',
//	'notes'	=> array(
//		'relations'	=> array(
//			'shift_id'
//			)
//		),
	'loginlog',
	'logaudit',

	// 'release',
	// 'pickup',
	// 'availability',

	'messages',
//	'notifications_db',
	'notifications_email',
	);
