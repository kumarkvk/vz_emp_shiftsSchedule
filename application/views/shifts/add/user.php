<?php
$extensions = HC_App::extensions();
$link = HC_Lib::link('shifts/add/index');

$out = HC_Html_Factory::widget('list')
	->add_attr('class', array('list-unstyled', 'list-separated'))
	;
$out->add_item(
	HC_Html_Factory::element('h3')
		->add_child( HCM::__('Staff') )
	);
$out->add_divider();

/* assign later */
$out2 = HC_Html_Factory::widget('list')
	->add_attr('class', 'list-group')
	// ->add_attr('class', 'list-separated')
	// ->add_attr('class', 'list-bordered')
	// ->add_attr('class', 'list-padded')
	;

if( $params->get('type') != SHIFT_HC_MODEL::TYPE_TIMEOFF ){
	$add_params = $params->to_array();
	$add_params['user+'] = 0;
	$out2->add_item(
		'select_later',
		HC_Html_Factory::element('a')
			->add_attr('href', $link->url($add_params))
			// ->add_attr('class', 'display-block')
			// ->add_attr('class', 'alert')
			->add_attr('title', HCM::__('Select Later') )
			->add_child(HCM::__('Select Later') )
			// ->add_attr('class', 'alert-info-o')
			->add_attr('class', 'hc-action')
		);
	$out2->add_item_attr( 'select_later', 'class', 'list-group-item' );
}

if( ! $free_staff ){
	$out2->add_item(
		HC_Html_Factory::element('span')
			->add_attr('class', 'display-block')
			->add_attr('class', 'alert')
			->add_attr('class', 'alert-danger-o')
			->add_attr('title', HCM::__('No staff available for this shift') )
			->add_child( 
				HC_Html::icon('exclamation-circle') . HCM::__('No staff available for this shift')
				)
		);
}
else {
	reset( $free_staff );
	foreach( $free_staff as $st ){
		$add_params = $params->to_array();
		$add_params['user+'] = $st->id;
		$main = HC_Html_Factory::element('a')
			->add_attr('href', $link->url($add_params))
			->add_attr('title', HCM::__('Assign Staff') )
			->add_attr('class', 'hc-action')
			// ->add_attr('class', array('btn', 'btn-default'))
			->add_child( $st->present_title() )
			;

		$item = HC_Html_Factory::element('div')
			// ->add_attr('class', 'display-block')
			// ->add_attr('class', 'list-group-item')
			// ->add_attr('class', 'alert')
			// ->add_attr('class', 'alert-condensed')
			// ->add_attr('class', 'alert-default-o')
			;

		$item->add_child( $main );

	/* EXTENSIONS SUCH AS CONFLICTS */
		for( $mi = 0; $mi < count($models); $mi++ ){
			$models[$mi]->user_id = $st->id;
		}

		$more_content = $extensions->run('shifts/assign/quickview', $models);
		if( $more_content ){
			$more_wrap = HC_Html_Factory::widget('list')
				->add_attr('class', 'list-unstyled')
				->add_attr('class', 'list-separated')
				->add_attr('class', 'text-small')
				->add_attr('style', 'margin-top: 0.5em;')
				;
			$added = 0;
			foreach($more_content as $mck => $mc ){
				$more_wrap->add_item($mc);
				$added++;
			}
			if( $added ){
				$item->add_child( $more_wrap );
			}
		}

		$out2->add_item( 'staff_' . $st->id, $item );
		$out2->add_item_attr( 'staff_' . $st->id, 'class', 'list-group-item' );
	}
}

$out->add_item( $out2 );

echo $out->render();

return;
?>

<?php
$extensions = HC_App::extensions();
$link = HC_Lib::link('shifts/add/index');

$out = HC_Html_Factory::widget('list')
	->add_attr('class', array('list-unstyled', 'list-separated'))
	;
$out->add_item(
	HC_Html_Factory::element('h3')
		->add_child( HCM::__('Staff') )
	);
$out->add_divider();

/* assign later */
$out2 = HC_Html_Factory::widget('tiles')
	->set_per_row(4)
	;

if( $params->get('type') != SHIFT_HC_MODEL::TYPE_TIMEOFF ){
	$add_params = $params->to_array();
	$add_params['user+'] = 0;
	$out2->add_item(
		HC_Html_Factory::element('a')
			->add_attr('href', $link->url($add_params))
			->add_attr('class', 'display-block')
			->add_attr('class', 'alert')
			->add_attr('title', HCM::__('Select Later') )
			->add_child(HCM::__('Select Later') )
			->add_attr('class', 'alert-info-o')
			->add_attr('class', 'hc-action')

		);
	$out->add_item($out2);
}

if( ! $free_staff ){
	$list = HC_Html_Factory::widget('tiles')
		->set_per_row(4)
		;
	$list->add_item(
		HC_Html_Factory::element('span')
			->add_attr('class', 'display-block')
			->add_attr('class', 'alert')
			->add_attr('class', 'alert-danger-o')
			->add_attr('title', HCM::__('No staff available for this shift') )
			->add_child( 
				HC_Html::icon('exclamation-circle') . HCM::__('No staff available for this shift')
				)
		);
}
else {
	$list = HC_Html_Factory::widget('tiles')
		->set_per_row(4)
		->set_per_row(2)
		;

	reset( $free_staff );
	foreach( $free_staff as $st ){
		$add_params = $params->to_array();
		$add_params['user+'] = $st->id;
		$main = HC_Html_Factory::element('a')
			->add_attr('href', $link->url($add_params))
			->add_attr('title', HCM::__('Assign Staff') )
			->add_attr('class', 'hc-action')
			->add_attr('class', array('btn', 'btn-default'))
			->add_child( $st->present_title() )
			;

		$item = HC_Html_Factory::element('div')
			->add_attr('class', 'display-block')
			->add_attr('class', 'alert')
			// ->add_attr('class', 'alert-condensed')
			->add_attr('class', 'alert-default-o')
			;

		$item->add_child( $main );

	/* EXTENSIONS SUCH AS CONFLICTS */
		for( $mi = 0; $mi < count($models); $mi++ ){
			$models[$mi]->user_id = $st->id;
		}

		$more_content = $extensions->run('shifts/assign/quickview', $models);
		if( $more_content ){
			$more_wrap = HC_Html_Factory::widget('list')
				->add_attr('class', 'list-unstyled')
				->add_attr('class', 'list-separated')
				->add_attr('class', 'text-small')
				->add_attr('style', 'margin-top: 0.5em;')
				;
			$added = 0;
			foreach($more_content as $mck => $mc ){
				$more_wrap->add_item($mc);
				$added++;
			}
			if( $added ){
				$item->add_child( $more_wrap );
			}
		}

		$list->add_item( $item );
	}
	$out->add_item( $list );
}
echo $out->render();
?>