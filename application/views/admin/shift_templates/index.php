<?php
$this->layout->set_partial(
	'header', 
	HC_Html::page_header(
		HC_Html_Factory::element('h2')
			->add_child(HCM::__('Shift Templates'))
		)
	);

$menubar = HC_Html_Factory::widget('list');
$menubar->add_attr('class', array('list-inline', 'list-separated'));
$menubar->add_item(
	'add',
	HC_Html_Factory::element('a')
		->add_attr('href', HC_Lib::link('admin/shift_templates/add'))
		->add_attr('class', array('btn', 'btn-default'))
		->add_attr('title', HCM::__('Add New Shift Template'))
		->add_child(
			HC_Html::icon('plus')
				->add_attr('class', 'text-success')
			)
		->add_child( HCM::__('Add New Shift Template') )
	);

$t = HC_Lib::time();
$view_entries = array();

foreach( $entries as $e ){
	$wrap = HC_Html_Factory::element('div')
		->add_attr('class', array('alert', 'alert-default-o'))
		->add_child(
			HC_Html_Factory::element('a')
				// ->add_child('&times;')
				->add_child(
					HC_Html::icon('times')
						->add_attr('class', array('text-danger'))
					)
				->add_attr('href', HC_Lib::link('admin/shift_templates/delete/' . $e->id) )
				->add_attr('title', HCM::__('Delete'))
				->add_attr('class', array('close', 'text-danger', 'hc-confirm'))
			)
		->add_child(
			HC_Html_Factory::widget('list')
				->add_attr('class', array('list-unstyled'))
				->add_item(
					HC_Html_Factory::element('a')
						->add_child( $e->name )
						->add_attr('href', HC_Lib::link('admin/shift_templates/edit/' . $e->id) )
					)
				->add_item(
					$t->formatPeriodOfDay($e->start, $e->end)
					)
				->add_item(
					'[' . $t->formatPeriodExtraShort($e->get_duration(), 'hour') . ']'
					)
			);

	$view_entries[] = $wrap;
}

$tiles = HC_Html_Factory::widget('tiles');
$tiles->set_items( $view_entries );

$out = HC_Html_Factory::widget('container');
$out->add_item( $menubar );
$out->add_item( $tiles );

echo $out->render();
?>