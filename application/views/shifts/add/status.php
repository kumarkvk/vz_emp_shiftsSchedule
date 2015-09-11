<?php
$list = HC_Html_Factory::widget('tiles')
	->set_per_row(4)
	;

$link = HC_Lib::link('shifts/add/index');
$shift = HC_App::model('shift');

foreach( $statuses as $status ){
	$add_params = $params->to_array();
	$add_params['status'] = $status;

	$shift->type = $params->get('type');
	$item = HC_Html_Factory::widget('titled', 'a')
		->add_attr('href', $link->url($add_params))
		->add_attr('class', 'display-block')
		->add_attr('class', 'no-underline')
		->add_child( 
			$shift->set('status', $status)->present_status()
				->add_attr('class', 'display-block')

			)
		;
	$item->add_attr('class', 'hc-action');

	$list->add_item( $item );
}

$out = HC_Html_Factory::widget('list')
	->add_attr('class', array('list-unstyled', 'list-separated'))
	;

$out->add_item(
	HC_Html_Factory::element('h3')
		->add_child( HCM::__('Status') )
	);

$out->add_item( $list );

echo $out->render();
?>