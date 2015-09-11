<?php
$list = HC_Html_Factory::widget('tiles')
	->set_per_row(4)
	;

$link = HC_Lib::link('shifts/add/index');

foreach( $locations as $obj ){
	$link_content = HC_Html_Factory::widget('list')
		->add_attr('class', 'list-unstyled')
		->add_item(
			$obj->present_title()
			)
		;

	$add_params = $params->to_array();
	$add_params['location'] = $obj->id;

	$item = HC_Html_Factory::widget('titled', 'a')
		->add_attr('href', $link->url($add_params))
		->add_attr('class', 'display-block')
		->add_attr('class', 'alert')
		->add_child( $link_content )
		;

	$item
		// ->add_attr('class', 'alert-success-o')
		->add_attr( 'class', 'alert-default-o' )
		// ->add_attr('style', 'background-color: ' . $obj->present_color())
		->add_attr('class', 'hc-action')
		;
	
	$list->add_item( $item );
}

$out = HC_Html_Factory::widget('list')
	->add_attr('class', array('list-unstyled', 'list-separated'))
	;

$out->add_item(
	HC_Html_Factory::element('h3')
		->add_child( HCM::__('Location') )
	);

$out->add_divider();
$out->add_item( $list );

echo $out->render();
?>