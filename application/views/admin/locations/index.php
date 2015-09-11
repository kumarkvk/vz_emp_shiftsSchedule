<?php
$this->layout->set_partial(
	'header', 
	HC_Html::page_header(
		HC_Html_Factory::element('h2')->add_child(HCM::__('Locations'))
		)
	);

$menubar = HC_Html_Factory::widget('list');
$menubar->add_attr('class', array('list-inline', 'list-separated'));
$menubar->add_item(
	'add',
	HC_Html_Factory::widget('titled', 'a')
		->add_attr('href', HC_Lib::link('admin/locations/add'))
		->add_attr('class', array('btn', 'btn-default'))
		->add_child(
			HC_Html::icon('plus')
				->add_attr('class', 'text-success')
			)
		->add_child( HCM::__('Add New Location') )
	);

$t = HC_Lib::time();
$view_entries = array();
foreach( $entries as $e ){
	$wrap = HC_Html_Factory::element('div')
		->add_attr('class', 'alert')
		// ->add_attr('class', 'alert-success-o')
		->add_attr('class', 'alert-default-o')
		;
	// $wrap->add_attr('style', 'border-color: ' . $e->present_color());
	$wrap->add_attr('style', 'background-color: ' . $e->present_color());

	if( $entries->result_count() > 1 ){
		$wrap->add_child(
			HC_Html_Factory::element('a')
				// ->add_child('&times;')
				->add_child(
					HC_Html::icon('times')
						->add_attr('class', array('text-danger'))
					)
				->add_attr('href', HC_Lib::link('admin/locations/delete/' . $e->id) )
				->add_attr('title', HCM::__('Delete'))
				->add_attr('class', array('close', 'text-danger', 'hc-confirm'))
			);
	}

	$wrap->add_child(
		HC_Html_Factory::widget('list')
			->add_attr('class', array('list-unstyled'))
			->add_item(
				HC_Html_Factory::element('a')
					->add_attr('href', HC_Lib::link('admin/locations/edit/' . $e->id) )
					->add_attr('class', 'text-big' )
					->add_child($e->present_title())
				)
			->add_item(
				HC_Html_Factory::element('span')
					->add_attr('class', 'text-italic' )
					->add_attr('class', 'text-smaller' )
					->add_child($e->present_description())
				)
			->add_item(
				HC_Html_Factory::element('span')
					->add_attr('class', 'text-muted')
					->add_attr('class', 'text-smaller')
					->add_child('id: ' . $e->id)
				) 
			->add_item(
				HC_Html_Factory::widget('leftright')
					->set_left(
						HC_Html_Factory::element('a')
							->add_child( HC_Html::icon('arrow-left') )
							->add_attr('class', array('btn', 'btn-default'))
							->add_attr('class', array('btn-xs'))
							->add_attr('href', HC_Lib::link('admin/locations/up/' . $e->id) )
							->add_attr('title', HCM::__('Move Up') )
						)
					->set_right(
						HC_Html_Factory::element('a')
							->add_child( HC_Html::icon('arrow-right') )
							->add_attr('class', array('btn', 'btn-default'))
							->add_attr('class', array('btn-xs'))
							->add_attr('href', HC_Lib::link('admin/locations/down/' . $e->id) )
							->add_attr('title', HCM::__('Move Down') )
						)
				)
			);
	$view_entries[] = $wrap;
}

$tiles = HC_Html_Factory::widget('tiles')
	->set_per_row(3)
	;
$tiles->set_items( $view_entries );

$out = HC_Html_Factory::widget('container');
$out->add_item( $menubar );
$out->add_item( $tiles );

echo $out->render();
?>