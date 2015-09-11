<?php
$link_to = 'auth/profile/index';

$menubar = HC_Html_Factory::widget('list')
	->add_attr('class', array('nav'))
	// ->add_attr('class', array('nav-tabs'))
	->add_attr('class', array('nav-pills'))
	->add_attr('class', array('nav-stacked'))
	->add_attr('class', array('nav-condensed'))
	;

/* PROFILE */
$menubar->add_item(
	'edit',
	HC_Html_Factory::widget('titled', 'a')
		->add_attr('href', HC_Lib::link($link_to))
		->add_attr('title', HCM::__('Edit My Profile'))
		->add_child( HC_Html::icon('user') . HCM::__('Edit My Profile') )
	);

/* PASSWORD */
$menubar->add_item(
	'password',
	HC_Html_Factory::widget('titled', 'a')
		->add_attr('href', HC_Lib::link($link_to, array('tab' => 'password')))
		->add_child( HC_Html::icon('lock') . HCM::__('Change Password') )
	);

/* EXTENDED TABS */
$extensions = HC_App::extensions();
$subextensions = $extensions->run('auth/profile/menubar', $object);

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