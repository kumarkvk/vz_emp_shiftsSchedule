<?php
$object = clone $object;
$display_form = HC_Html_Factory::widget('list')
	->add_attr('class', 'list-unstyled')
	->add_attr('class', 'list-separated')
	// ->add_attr('class', 'form-condensed')
	->add_attr('class', 'form-small-labels')
	;

if( $object->type != $object->_const('TYPE_TIMEOFF') ){
	if( strlen($object->location->description) ){
		$location_view = HC_Html_Factory::widget('list')
			->add_attr('class', 'list-unstyled')
			->add_attr('class', 'list-separated')
			;
		$location_view->add_item( $object->present_location() );
		$location_view->add_item(
			HC_Html_Factory::element('span')
				->add_attr('class', 'text-italic' )
				->add_attr('class', 'text-smaller' )
				->add_child($object->location->present_description())
			);
	}
	else {
		$location_view = $object->present_location();
	}

	$display_form->add_item(
		HC_Html_Factory::widget('label_row')
			->set_label( HCM::__('Location') )
			->set_content( $location_view )
		);
}

$display_form->add_item(
	HC_Html_Factory::widget('grid')
		->add_item(
			HC_Html_Factory::widget('label_row')
				->set_label( HCM::__('Time') )
				->set_content( $object->present_time() )
			, 6
			)
		->add_item(
			HC_Html_Factory::widget('label_row')
				->set_label( HCM::__('Date') )
				->set_content(
					$object->present_date()
					)
			, 6
			)
	);

$display_form->add_item(
	HC_Html_Factory::widget('label_row')
		->set_label( HCM::__('Staff') )
		->set_content(
			$object->present_user()
			)
	);

if( $object->status == $object->_const('STATUS_DRAFT') ){
	$display_form->add_item(
		HC_Html_Factory::widget('label_row')
			->set_label( HCM::__('Status') )
			->set_content(
				$object->present_status()
				)
		);
}

echo $display_form->render();
?>