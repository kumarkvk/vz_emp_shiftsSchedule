<?php
$this->layout->set_partial(
	'header', 
	HC_Html::page_header(
		HC_Html_Factory::element('h2')
			->add_child( HCM::__('Add New Shift Template') )
		)
	);

$link = HC_Lib::link('admin/shift_templates/insert');
$display_form = HC_Html_Factory::widget('form')
	->add_attr('action', $link->url() )
	->add_attr('class', 'form-horizontal')
	->add_attr('class', 'form-condensed')
	;

$display_form->add_item(
	HC_Html_Factory::widget('label_row')
		->set_label( HCM::__('Name') )
		->set_content( 
			$form->input('name')
				->add_attr('size', 32)
			)
		->set_error( $form->input('name')->error() )
	);

$display_form->add_item(
	HC_Html_Factory::widget('label_row')
		->set_label( HCM::__('Time') )
		->set_content( 
			$form->input('time')
			)
		->set_error( $form->input('time')->error() )
	);

$buttons = HC_Html_Factory::widget('list')
	->add_attr('class', array('list-inline', 'list-separated') )
	;
$buttons->add_item(
	HC_Html_Factory::element('input')
		->add_attr('type', 'submit')
		->add_attr('class', array('btn', 'btn-default'))
		->add_attr('title', HCM::__('Add New Shift Template') )
		->add_attr('value', HCM::__('Add New Shift Template') )
	);
$display_form->add_item(
	HC_Html_Factory::widget('label_row')
		->set_content( $buttons )
	);

$out = HC_Html_Factory::widget('container');
$out->add_item( $display_form );

echo $out->render();
?>