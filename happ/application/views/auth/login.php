<?php
$this->layout->set_partial(
	'header', 
	HC_Html::page_header(
		HC_Html_Factory::element('h2')
			->add_child( HCM::__('Log In') )
		)
	);

$app_conf = HC_App::app_conf();
if( $app_conf->get('login_with') == 'username' ){
	$identity_label = HCM::__('Username');
}
else {
	$identity_label = HCM::__('Email');
}

$display_form = HC_Html_Factory::widget('form')
	->add_attr('action', HC_Lib::link('auth/login')->url() )
	->add_attr('class', 'form-horizontal')
	->add_attr('class', 'form-condensed')
	;

$display_form->add_item(
	HC_Html_Factory::widget('label_row')
		->set_label( $identity_label )
		->set_content( 
			$form->input('identity')
				->add_attr('placeholder', $identity_label)
			)
		->set_error( $form->input('identity')->error() )
	);

$display_form->add_item(
	HC_Html_Factory::widget('label_row')
		->set_label( HCM::__('Password') )
		->set_content( 
			$form->input('password')
				->add_attr('placeholder', HCM::__('Password'))
			)
		->set_error( $form->input('password')->error() )
	);

$display_form->add_item(
	HC_Html_Factory::widget('label_row')
		->set_content( 
			HC_Html_Factory::element('input')
				->add_attr('type', 'submit')
				->add_attr('class', array('btn', 'btn-default'))
				->add_attr('title', HCM::__('Log In') )
				->add_attr('value', HCM::__('Log In') )
			)
	);

$out = HC_Html_Factory::widget('list')
	->add_attr('class', 'list-unstyled')
	->add_attr('class', 'list-separated')
	;

$out->add_item( $display_form );
$out->add_item(
	HC_Html_Factory::widget('titled', 'a')
		->add_attr('href', HC_Lib::link('auth/forgot_password'))
		->add_child( HCM::__('Lost your password?') )
	);

echo $out->render();
?>