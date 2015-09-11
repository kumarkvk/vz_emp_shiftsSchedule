<?php
$current_user_id = $this->auth->check();

$display_form = HC_Html_Factory::widget('form')
	->add_attr('action', HC_Lib::link('admin/users/update/index/' . $object->id) )
	->add_attr('class', 'form-horizontal')
	->add_attr('class', 'form-condensed')
	;

/* BUTTONS */
$buttons = HC_Html_Factory::widget('list')
	->add_attr('class', array('list-inline', 'list-separated') )
	;
if( ! $form->readonly() ){
	$buttons->add_item(
		HC_Html_Factory::element('input')
			->add_attr('type', 'submit')
			->add_attr('class', array('btn', 'btn-default'))
			->add_attr('title', HCM::__('Save') )
			->add_attr('value', HCM::__('Save') )
		);
}

if( $object->id != $current_user_id ){
	$buttons->add_divider();

	if( $object->active == USER_HC_MODEL::STATUS_ACTIVE ){
		$buttons->add_item(
			HC_Html_Factory::widget('titled', 'a')
				->add_attr('href', HC_Lib::link('admin/users/update/index/' . $object->id . '/active/' . USER_HC_MODEL::STATUS_ARCHIVE))
				->add_attr('class', array('btn', 'btn-warning-o'))
				->add_attr('class', array('btn-sm'))
				->add_attr('class', array('hc-confirm'))
				->add_attr('title', HCM::__('Archive User') )
				->add_child( HCM::__('Archive User') )
			);
	}
	else {
		$buttons->add_item(
			HC_Html_Factory::widget('titled', 'a')
				->add_attr('href', HC_Lib::link('admin/users/update/index/' . $object->id . '/active/' . USER_HC_MODEL::STATUS_ACTIVE))
				->add_attr('class', array('btn', 'btn-success-o'))
				->add_attr('class', array('btn-sm'))
				->add_attr('class', array('hc-confirm'))
				->add_attr('title', HCM::__('Restore User') )
				->add_child( HCM::__('Restore User') )
			);
	}
}

$display_form
	->add_item(
		HC_Html_Factory::widget('label_row')
			->set_label( HCM::__('First Name') )
			->set_content( 
				$form->input('first_name')
					->add_attr('size', 24)
				)
			->set_error( $form->input('first_name')->error() )
		)
	->add_item(
		HC_Html_Factory::widget('label_row')
			->set_label( HCM::__('Last Name') )
			->set_content( 
				$form->input('last_name')
					->add_attr('size', 24)
				)
			->set_error( $form->input('last_name')->error() )
		)
	->add_item(
		HC_Html_Factory::widget('label_row')
			->set_label( HCM::__('Email') )
			->set_content( 
				$form->input('email')
					->add_attr('size', 32)
				)
			->set_error( $form->input('email')->error() )
		)
	;

$app_conf = HC_App::app_conf();
if( $app_conf->get('login_with') == 'username' ){
	$display_form
		->add_item(
			HC_Html_Factory::widget('label_row')
				->set_label( HCM::__('Username') )
				->set_content( 
					$form->input('username')
						->add_attr('size', 24)
					)
				->set_error( $form->input('username')->error() )
			)
		;
}

$input_level = $form->input('level')
	->set_options(
		array(
			USER_HC_MODEL::LEVEL_STAFF		=> HCM::__('Staff'),
			USER_HC_MODEL::LEVEL_MANAGER	=> HCM::__('Manager'),
			USER_HC_MODEL::LEVEL_ADMIN		=> HCM::__('Admin'),
			)
		)
	;

/* can't change own level */
if( $object->id == $this->auth->check() ){
	// $input_level->add_attr('disabled', 'disabled');
	$input_level->set_readonly();
}

$display_form
	->add_item(
		HC_Html_Factory::widget('label_row')
			->set_label( HCM::__('User Level') )
			->set_content( $input_level )
			->set_error( $input_level->error() )
			->set_content_static( $input_level->readonly() )
		)
	;

$display_form
	->add_item(
		HC_Html_Factory::widget('label_row')
			->set_content( $buttons )
		)
	;

echo $display_form->render();
?>