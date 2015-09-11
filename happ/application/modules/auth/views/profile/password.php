<?php
$display_form = HC_Html_Factory::widget('form')
	->add_attr('action', HC_Lib::link('auth/profile/password') )
	->add_attr('class', 'form-horizontal')
	->add_attr('class', 'form-condensed')
	;

$display_form->add_item(
	HC_Html_Factory::widget('label_row')
		->set_label( HCM::__('New Password') )
		->set_content( 
			$form->input('password')
				->add_attr('size', 24)
			)
		->set_error( $form->input('password')->error() )
	);

$display_form->add_item(
	HC_Html_Factory::widget('label_row')
		->set_label( HCM::__('Confirm Password') )
		->set_content( 
			$form->input('confirm_password')
				->add_attr('size', 24)
			)
		->set_error( $form->input('confirm_password')->error() )
	);

$buttons = HC_Html_Factory::widget('list')
	->add_attr('class', array('list-inline', 'list-separated') )
	;
$buttons->add_item(
	HC_Html_Factory::element('input')
		->add_attr('type', 'submit')
		->add_attr('class', array('btn', 'btn-default'))
		->add_attr('title', HCM::__('Save') )
		->add_attr('value', HCM::__('Save') )
	);
$display_form->add_item(
	HC_Html_Factory::widget('label_row')
		->set_content( $buttons )
	);

echo $display_form->render();
?>