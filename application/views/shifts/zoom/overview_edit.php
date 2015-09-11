<?php
$object = clone $object;
$display_form = HC_Html_Factory::widget('form')
	->add_attr('action', HC_Lib::link('shifts/update/index/' . $object->id) )
	// ->add_attr('class', 'form-horizontal')
	->add_attr('class', 'form-condensed')
	->add_attr('class', 'form-small-labels')
	;

/* BUTTONS */
$buttons = HC_Html_Factory::widget('list')
	->add_attr('class', array('list-inline', 'list-separated') )
	;
$buttons->add_item(
	HC_Html_Factory::element('input')
		->add_attr('type', 'submit')
		->add_attr('class', array('btn'))
		->add_attr('class', array('btn-success'))
		->add_attr('title', HCM::__('Save') )
		->add_attr('value', HCM::__('Save') )
	);

/* DELETE BUTTON */
$link = HC_Lib::link('shifts/delete/index/' . $object->id);
$btn = HC_Html_Factory::element('a')
	->add_attr('class', array('btn'))
	->add_attr( 'href', $link->url() )
	->add_attr( 'title', HCM::__('Delete') )
	->add_attr( 'class', 'btn-danger' )
	->add_attr( 'class', 'btn-sm' )
	->add_attr( 'class', 'hc-confirm' )
	->add_child(
		HC_Html::icon('times') . HCM::__('Delete')
		)
	;

$buttons->add_item( 'delete', $btn );
$buttons->add_item_attr( 'delete', 'class', 'pull-right' );

/* LOCATION */
if( $object->type != $object->_const('TYPE_TIMEOFF') ){
	$location_view = HC_Html_Factory::widget('module')
		->set_url('shifts/zoom/form')
		->pass_arg( $object )
		->pass_arg( 'location' )
		->set_self_target( TRUE )
		->set_skip_src( TRUE )
		;
	$display_form
		->add_item(
			HC_Html_Factory::widget('label_row')
				->set_label( HCM::__('Location') )
				->set_content(
					$location_view
					)
			)
		;
}

/* TIME */
$time_input = $form->input('time');
$display_form
	->add_item(
		// HC_Html_Factory::widget('list')
			// ->add_attr('class', 'list-inline')
			// ->add_attr('class', 'list-separated-hori')
		HC_Html_Factory::widget('grid')
			->add_item(
				// 'time',
				HC_Html_Factory::widget('label_row')
					->set_label( HCM::__('Time') )
					->set_content( $form->input('time') )
					->set_error( $form->input('time')->error() )
					,
					6
				)
			->add_item(
				// 'date',
				HC_Html_Factory::widget('label_row')
					->set_label( HCM::__('Date') )
					->set_content( $form->input('date') )
					->set_error( $form->input('date')->error() )
					,
					6
				)
/*
			->add_item('status',
				HC_Html_Factory::widget('label_row')
					->set_label( HCM::__('Status') )
					->set_content(
						$form->input('status')
							->set_inline( TRUE )
							->add_option( $object->_const('STATUS_DRAFT'), $object->set('status', $object->_const('STATUS_DRAFT'))->present_status() )
							->add_option( $object->_const('STATUS_ACTIVE'), $object->set('status', $object->_const('STATUS_ACTIVE'))->present_status() )
						)
					->set_error( $form->input('status')->error() )
				)
*/
			// ->add_item_attr( 'date', 'style', 'vertical-align: text-top;' )
			// ->add_item_attr( 'time', 'style', 'vertical-align: text-top;' )
			// ->add_item_attr( 'status', 'style', 'vertical-align: text-top;' )
		)
	;

/* STAFF */
$display_form
	->add_item( $form->input('user') )
	;

$staff_view = HC_Html_Factory::widget('module')
	->set_url('shifts/zoom/form')
	->pass_arg( $object )
	->pass_arg( 'user' )
	->set_self_target( TRUE )
	->set_skip_src( TRUE )
	;

$display_form
	->add_item(
		HC_Html_Factory::widget('label_row')
			->set_label( HCM::__('Staff') )
			->set_content(
				$staff_view
				)
		)
	;

/* STATUS */
$display_form
	->add_item(
		HC_Html_Factory::widget('label_row')
			->set_label( HCM::__('Status') )
			->set_content(
				$form->input('status')
					->set_inline( TRUE )
					->add_option(
						$object->_const('STATUS_DRAFT'),
						$object->set('status', $object->_const('STATUS_DRAFT'))->present_status()
						)
					->add_option( 
						$object->_const('STATUS_ACTIVE'),
						$object->set('status', $object->_const('STATUS_ACTIVE'))->present_status()
						)
					->render()
				)
			->set_error( $form->input('status')->error() )
		)
	;

/* ADD NOTE IF POSSIBLE */
$extensions = HC_App::extensions();
$more_content = $extensions->run('shifts/zoom/confirm');
if( $more_content ){
	$more_holder = HC_Html_Factory::widget('list')
		->add_attr('class', 'list-unstyled')
		->add_attr('class', 'list-separated2')
		;

	foreach($more_content as $mc ){
		$more_holder->add_item( $mc );
	}

	$display_form->add_item( $more_holder );
}

$display_form
	->add_item(
		HC_Html_Factory::widget('label_row')
			->set_content( $buttons )
			->add_attr('class', 'padded2')
			->add_attr('style', 'border-top: #eee 1px solid; margin-top: 1em;')
	);

// echo $display_form->render();

$out = HC_Html_Factory::widget('flatmodal');
$out->set_content( $display_form );
/* 
$out->set_closer(
	HC_Html_Factory::element('a')
		->add_child( HC_Html::icon('arrow-left') . HCM::__('Cancel') )
		->add_attr('class', array('btn', 'btn-info-o'))
	);
*/
echo $out->render();
?>
<script language="JavaScript">
jQuery('.hc-shift-templates').on('click', function(e)
{
	jQuery(this).closest('form').find('[name=time_start]').val( jQuery(this).data('start') );
	jQuery(this).closest('form').find('[name=time_end]').val( jQuery(this).data('end') );

	jQuery(this).closest('.hc-dropdown').find('.hc-dropdown-toggle').dropdown('toggle');
	return false;
});
</script>