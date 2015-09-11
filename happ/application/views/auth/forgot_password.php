<?php
$this->layout->set_partial(
	'header', 
	HC_Html::page_header(
		HC_Html_Factory::element('h2')
			->add_child( HCM::__('Lost your password?') )
		)
	);

$display_form = HC_Html_Factory::widget('form')
	->add_attr('action', HC_Lib::link('auth/forgot_password')->url() )
	->add_attr('class', 'form-horizontal')
	->add_attr('class', 'form-condensed')
	;

$display_form->add_item(
	HC_Html_Factory::widget('label_row')
		->set_label( HCM::__('Email') )
		->set_content( 
			$form->input('email')
				->add_attr('placeholder', HCM::__('Email'))
			)
		->set_error( $form->input('email')->error() )
	);

$display_form->add_item(
	HC_Html_Factory::widget('label_row')
		->set_content( 
			HC_Html_Factory::element('input')
				->add_attr('type', 'submit')
				->add_attr('class', array('btn', 'btn-default'))
				->add_attr('title', HCM::__('Get New Password') )
				->add_attr('value', HCM::__('Get New Password') )
			)
	);

$out = HC_Html_Factory::widget('list')
	->add_attr('class', 'list-unstyled')
	->add_attr('class', 'list-separated')
	;

$out->add_item( $display_form );

echo $out->render();
?>