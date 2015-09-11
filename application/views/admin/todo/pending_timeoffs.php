<?php
if( ! $count ){
	return;
}

$title = sprintf( HCM::_n('%d Pending Timeoff Request', '%d Pending Timeoff Requests', $count), $count );

$temp_shift = HC_App::model('shift');

$linkto = HC_Lib::link('list/browse', 
	array(
		'type'		=> $temp_shift->_const('TYPE_TIMEOFF'),
		'status'	=> $temp_shift->_const('STATUS_DRAFT'),
		'range'		=> 'upcoming',
		)
	);

$item = HC_Html_Factory::widget('titled', 'a')
	->add_attr('class', 'display-block')
	->add_attr('href', $linkto)
	->add_attr('class', 'alert')
	->add_attr('class', 'alert-archive')
	->add_attr('title', $title )
	->add_child( $title )
	;
echo $item->render();