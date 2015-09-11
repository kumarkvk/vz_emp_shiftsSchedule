<?php
$out = HC_Html_Factory::widget('container');

$out->add_item( $form->input('user') );

$location_view = HC_Html_Factory::element('div')
	->add_attr('class', 'alert')
	->add_attr('class', 'alert-default-o')

	->add_child(
		HC_Html_Factory::element('a')
			->add_attr('class', 'hc-flatmodal-loader' )
			->add_attr('href', HC_Lib::link('shifts/zoom/change/' . $object->id . '/user/' . $form->input('user')->value() ) )
			->add_attr('class', 'display-block' )

			->add_child( $object->present_user() )
			->add_child(
					HC_Html::icon('caret-down')
						->add_attr('style', 'float: right;')
				)

		)
	;

$out->add_item( $location_view );
echo $out->render();
