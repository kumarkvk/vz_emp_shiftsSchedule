<?php
if( ! count($entries) ){
	return;
}

$list = HC_Html_Factory::widget('list')
	->add_attr('class', array('list-unstyled'))
	->add_attr('class', array('list-padded'))
	->add_attr('class', array('list-bordered'))
	// ->add_attr('class', array('list-separated'))
	;

foreach( $entries as $e ){
	$item_view = HC_Html_Factory::widget('list')
		->add_attr('class', array('list-unstyled'))
		->add_attr('class', array('list-separated'))
		;
	$item_view->add_item( $e->present_type() );
	$item_view->add_item( $e->present_details() );

	$list->add_item( $item_view );
}

echo $list->render();
?>