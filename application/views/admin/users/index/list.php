<?php
$t = HC_Lib::time();
$view_entries = array();
$current_user_id = $this->auth->check();

foreach( $entries as $e ){
	$wrap = HC_Html_Factory::element('div')
		->add_attr('class', array('alert'))
		;

	if( $e->id != $current_user_id ){
		$wrap->add_child(
			HC_Html_Factory::element('a')
				// ->add_child('&times;')
				->add_child(
					HC_Html::icon('times')
						->add_attr('class', array('text-danger'))
					)
				->add_attr('href', HC_Lib::link('admin/users/delete/' . $e->id) )
				->add_attr('title', HCM::__('Delete'))
				->add_attr('class', array('close', 'text-danger', 'hc-confirm'))
			);
	}

	$wrap
		->add_child(
			HC_Html_Factory::widget('list')
				->add_attr('class', array('list-unstyled'))
				->add_item(
					HC_Html_Factory::element('a')
						->add_child( $e->present_title() )
						->add_attr('href', HC_Lib::link('admin/users/zoom/index/id/' . $e->id) )
					)
				->add_item( $e->present_level() )
				->add_item( 
					HC_Html_Factory::element('span')
						->add_attr('class', 'text-muted')
						->add_child( $e->present_email() )
					)
			);

	$status_class = $e->present_status_class();
	foreach( $status_class as $sc ){
		$wrap->add_attr('class', 'alert-' . $sc . '-o');
	}

	$view_entries[] = $wrap;
}

$tiles = HC_Html_Factory::widget('tiles')
	->set_per_row(3)
	;
$tiles->set_items( $view_entries );

$out = HC_Html_Factory::widget('container');
$out->add_item( $tiles );

echo $out->render();
?>