<?php
$link = HC_Lib::link('shifts/add/insert_time');

$display_form = HC_Html_Factory::widget('form')
	->add_attr('action', $link->url($params->to_array()) )
	// ->add_attr('class', 'form-horizontal')
	->add_attr('class', 'form-condensed')
	->add_attr('class', 'form-small-labels')
	;

/* time labels with shift templates */
$templates_label = '';
if( 0 && count($shift_templates) ){
	$templates_label = HC_Html_Factory::widget('dropdown');
	$templates_label->set_wrap();
	$templates_label->set_no_caret(FALSE);

	$templates_label->set_title( 
		HC_Html_Factory::element('a')
			->add_child( HC_Html::icon('clock-o') )
			->add_attr('class', array('btn', 'btn-default'))
		);

	$t = HC_Lib::time();
	foreach( $shift_templates as $sht ){
		$end = ($sht->end > 24*60*60) ? ($sht->end - 24*60*60) : $sht->end;
		$templates_label->add_item( 
			HC_Html_Factory::element('a')
				->add_attr('class', 'hc-shift-templates')
				->add_attr('href', '#')
				->add_attr('data-start', $sht->start)
				->add_attr('data-end', $end)
				->add_child( $sht->name )
				->add_child( '<br/>' )
				->add_child( $t->formatPeriodOfDay($sht->start, $sht->end) )
			);
	}
}

if( $templates_label ){
	$time_input = HC_Html_Factory::widget('list')
		->add_attr('class', 'list-unstyled')
		->add_attr('class', 'list-inline')
		->add_item( 'item', $form->input('time') )

		->add_item( 'label', $templates_label )
		->add_item_attr( 'label', 'style', 'margin-left: 1em;' )
		;
}
else {
	$time_input = $form->input('time');
}

$display_form->add_item(
	HC_Html_Factory::widget('label_row')
		->set_label( HCM::__('Time') )
		->set_content( $time_input )
		->set_error( $form->input('time')->error() )
	);

$display_form->add_item(
	HC_Html_Factory::widget('label_row')
		->set_label( HCM::__('Date') )
		->set_content( $form->input('date') )
		->set_error( $form->input('date')->error() )
	);


$buttons = HC_Html_Factory::widget('list')
	->add_attr('class', array('list-inline', 'list-separated') )
	;
$buttons->add_item(
	HC_Html_Factory::element('input')
		->add_attr('type', 'submit')
		->add_attr('class', array('btn', 'btn-default'))
		->add_attr('title', HCM::__('OK') )
		->add_attr('value', HCM::__('OK') )
	);
$display_form->add_item(
	HC_Html_Factory::widget('label_row')
		->set_content( $buttons )
	);

$out = HC_Html_Factory::widget('list')
	->add_attr('class', array('list-unstyled', 'list-separated'))
	;
/* 
$out->add_item(
	HC_Html_Factory::element('h3')
		->add_child( HCM::__('Date and Time') )
	);
$out->add_divider();
 */
$out->add_item( $display_form );

echo $out->render();
?>
<script language="JavaScript">
jQuery('.hc-shift-templates').on('click', function(e)
{
	jQuery(this).closest('form').find('[name=time_start]').val( jQuery(this).data('start') );
	jQuery(this).closest('form').find('[name=time_start]').attr('value', jQuery(this).data('start') );
	jQuery(this).closest('form').find('[name=time_end]').val( jQuery(this).data('end') );

	jQuery(this).closest('.hc-dropdown').find('.hc-dropdown-toggle').dropdown('toggle');
	return false;
});
</script>