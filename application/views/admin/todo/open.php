<?php
if( ! $count ){
	return;
}

/* translators: title for shifts that have no employee assigned yet */
$title = sprintf( HCM::_n('%d Open Shift', '%d Open Shifts', $count), $count );

$linkto = HC_Lib::link('list/browse', 
	array(
		'staff'	=> 0,
		'range'	=> 'upcoming',
		)
	);

$item = HC_Html_Factory::widget('titled', 'a')
	->add_attr('class', 'display-block')
	->add_attr('href', $linkto)
	->add_attr('class', 'alert')
	->add_attr('class', 'alert-danger-o')
	->add_attr('title', $title )
	->add_child( $title )
	;
echo $item->render();