<?php
if( $layout->has_partial('header') ){
	echo $layout->partial('header');
}
if( $layout->has_partial('menubar') && $layout->has_partial('content') ){
	$sublayout = HC_Html_Factory::widget('grid')
		->set_scale('md')
		;
	$sublayout->add_item(
		$layout->partial('content'),
		9
		// array('class' => 'col-md-pull-3')
		);
	$sublayout->add_item(
		$layout->partial('menubar'),
		3
		// array('class' => 'col-md-push-9')
		);
	echo $sublayout->render();
}
else {
	if( $layout->has_partial('content') ){
		echo $layout->partial('content');
	}
}
?>