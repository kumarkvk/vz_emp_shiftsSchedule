<?php
$extensions = HC_App::extensions();

$out = HC_Html_Factory::widget('list')
	->add_attr('class', 'list-unstyled')
	->add_attr('class', 'list-padded')
	// ->add_attr('class', 'list-separated')
	// ->add_attr('class', 'hc-list-bordered')
	->add_attr('class', 'hc-list-bordered-full')
	;

if( ! $options ){
	$out = HC_Html_Factory::element('span')
		->add_attr('class', 'display-block')
		->add_attr('class', 'alert')
		->add_attr('class', 'alert-danger-o')
		->add_attr('title', HCM::__('No staff available for this shift') )
		->add_child( 
			HC_Html::icon('exclamation-circle') . HCM::__('No staff available for this shift')
			)
		;
}
else {
	$link = HC_Lib::link('shifts/zoom/index/' . $object->id . '/overview');

	/* unassign */
	if( $skip ){
		$link = HC_Lib::link('shifts/zoom/form/' . $object->id . '/user/' . 0);

		$out->add_item(
			HC_Html_Factory::element('a')
				->add_attr('href', $link->url())
				->add_child( HC_Html::icon('sign-out') )
				->add_child( HCM::__('Release Shift') )
				->add_attr('class', 'hc-flatmodal-closer')
				->add_attr('class', 'hc-flatmodal-return-loader')
			);
	}

	foreach( $options as $option ){
		$wrap = HC_Html_Factory::widget('container');

		$link = HC_Lib::link('shifts/zoom/form/' . $object->id . '/user/' . $option->id);

		$item = HC_Html_Factory::element('a')
			->add_attr('href', $link->url())
			->add_attr('title', HCM::__('Assign Staff') )
			->add_child( $option->present_title() )
			->add_attr('class', 'hc-flatmodal-return-loader')
			;

		$item->add_attr('class', 'hc-action');
		$wrap->add_item( $item );

	/* EXTENSIONS */
		$object->user_id = $option->id;
		$more_content = $extensions->run(
			'shifts/assign/quickview',
			$object
			);
		if( $more_content ){
			$more_wrap = HC_Html_Factory::widget('list')
				->add_attr('class', 'list-unstyled')
				->add_attr('class', 'list-separated')
				->add_attr('class', 'text-small')
				;
			$added = 0;
			foreach($more_content as $mck => $mc ){
				$more_wrap->add_item($mc);
				$added++;
			}
			if( $added ){
				$wrap->add_item($more_wrap);
			}
		}

		$out->add_item($wrap);
	}
}

echo $out->render();
?>