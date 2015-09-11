<?php
if( ! $count ){
	return;
}

$title = sprintf( HCM::_n('%d Conflict', '%d Conflicts', $count), $count );

$linkto = HC_Lib::link('list/index', 
	array(
		'filter'	=> 'conflicts',
		'range'		=> 'upcoming',
		)
	);

$item = HC_Html_Factory::widget('titled', 'a')
	->add_attr('class', 'display-block')
	->add_attr('href', $linkto)
	->add_attr('class', 'alert')
	->add_attr('class', 'alert-danger')
	->add_child( $title )
	;
echo $item->render();