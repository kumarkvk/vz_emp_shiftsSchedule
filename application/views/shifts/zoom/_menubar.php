<?php
$link_to = 'shifts/zoom/index/id/' . $object->id;

$menubar = HC_Html_Factory::widget('list')
	->add_attr('class', array('nav'))
	// ->add_attr('class', array('nav-tabs'))
	->add_attr('class', array('nav-pills'))
	->add_attr('class', array('nav-stacked'))
	->add_attr('class', array('nav-condensed'))
	;

$more_class = $object->present_status_class();
foreach( $more_class as $c ){
	// $menubar->add_item_attr('overview', 'class', 'tab-'. $c);
}
$overview_text = HCM::__('Overview');

/* OVERVIEW */
$menubar->add_item(
	'overview',
	HC_Html_Factory::widget('titled', 'a')
		->add_attr('href', HC_Lib::link($link_to))
		->add_attr('title', $overview_text)
		->add_child( HC_Html::icon('info') . $overview_text )
	);

/* EXTENDED TABS */
$extensions = HC_App::extensions();
$more_content = $extensions->run(
	'shifts/zoom/menubar',
	$object
	);
foreach( $more_content as $subtab => $subtitle ){
	if( $subtitle ){
		$menubar->add_item(
			$subtab,
			HC_Html_Factory::widget('titled', 'a')
				->add_attr('href', HC_Lib::link($link_to, array('tab' => $subtab)))
				->add_child( $subtitle )
			);
	}
}

$menubar->set_active( $tab );
?>
<?php echo $menubar->render(); ?>