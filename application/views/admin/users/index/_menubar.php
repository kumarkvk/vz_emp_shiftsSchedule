<?php
$link_to = 'admin/users/index';

$menubar = HC_Html_Factory::widget('list')
	->add_attr('class', array('nav'))
	// ->add_attr('class', array('nav-tabs'))
	->add_attr('class', array('nav-pills'))
	->add_attr('class', array('nav-stacked'))
	->add_attr('class', array('nav-condensed'))
	;

/* LIST */
$menubar->add_item(
	'list',
	HC_Html_Factory::widget('titled', 'a')
		->add_attr('href', HC_Lib::link($link_to))
		->add_child( HCM::__('All Users') )
	);

/* ADD */
$menubar->add_item(
	'add',
	HC_Html_Factory::widget('titled', 'a')
		->add_attr('href', HC_Lib::link($link_to . '/add'))
		->add_child( HCM::__('Add New User') )
	);

/* EXTENDED TABS */
$extensions = HC_App::extensions();
$subextensions = $extensions->run('admin/users/index/menubar', $object);

foreach( $subextensions as $subtab => $subtitle ){
	if( $subtitle ){
		$menubar->add_item(
			$subtab,
			HC_Html_Factory::element('a')
				->add_attr('href', HC_Lib::link($link_to . '/' . $subtab))
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