<?php
$link_to = 'admin/users/zoom/index/id/' . $object->id;

$menubar = HC_Html_Factory::widget('list')
	->add_attr('class', array('nav'))
	// ->add_attr('class', array('nav-tabs'))
	->add_attr('class', array('nav-pills'))
	->add_attr('class', array('nav-stacked'))
	->add_attr('class', array('nav-condensed'))
	;

$more_class = $object->present_status_class();
foreach( $more_class as $c ){
	// $menubar->add_item_attr('edit', 'class', 'tab-'. $c);
}

/* EDIT */
$menubar->add_item(
	'edit',
	HC_Html_Factory::widget('titled', 'a')
		->add_attr('href', HC_Lib::link($link_to))
		->add_child( HC_Html::icon('edit') . HCM::__('Edit') )
	);

/* PASSWORD */
$menubar->add_item(
	'password',
	HC_Html_Factory::widget('titled', 'a')
		->add_attr('href', HC_Lib::link($link_to, array('tab' => 'password')))
		->add_child( HC_Html::icon('lock') . HCM::__('Password') )
	);

/* EXTENDED TABS */
$extensions = HC_App::extensions();
$subextensions = $extensions->run('admin/users/zoom/menubar', $object);

foreach( $subextensions as $subtab => $subtitle ){
	if( $subtitle ){
		$menubar->add_item(
			$subtab,
			HC_Html_Factory::element('a')
				->add_attr('href', HC_Lib::link($link_to, array('tab' => $subtab)))
				->add_child( $subtitle )
			);
	}
	else {
		$menubar->remove_item( $subtab );
	}
}

$menubar->set_active( $tab );
echo $menubar->render();
?>