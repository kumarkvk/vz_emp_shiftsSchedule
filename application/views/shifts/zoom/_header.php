<?php
$header = HC_Html_Factory::widget('list')
	->add_attr('class', 'list-unstyled')
	->add_item(
		'title',
		HC_Html_Factory::element('h2')
			->add_child( $object->present_title() )
		)
/*
	->add_item(
		'misc',
		$object->present_title_misc()
		)
*/
/*
		->add_item(
			'misc_misc',
			$object->present_title_misc_misc()
			)
		->add_item_attr(
			'misc_misc',
			'class', array('text-muted', 'text-smaller')
			)
*/
	;


/* 
$header->add_item(
	'staff',
	$object->present_user()
	);

 */
echo HC_Html::page_header( $header );
?>