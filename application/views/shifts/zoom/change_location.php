<?php
$out = HC_Html_Factory::widget('list')
	->add_attr('class', 'list-unstyled')
	->add_attr('class', 'list-padded')
	// ->add_attr('class', 'list-separated')
	// ->add_attr('class', 'hc-list-bordered')
	->add_attr('class', 'hc-list-bordered-full')
	;

foreach( $options as $option ){
	$wrap = HC_Html_Factory::widget('container');

	$link = HC_Lib::link('shifts/zoom/form/' . $object->id . '/location/' . $option->id);

	$item = HC_Html_Factory::widget('titled', 'a')
		->add_attr('href', $link->url())
		->add_child( $option->present_title() )
		->add_attr('class', 'hc-flatmodal-return-loader')
		;

	$wrap->add_item( $item );
	$out->add_item( $wrap );
}

echo $out->render();
?>