<?php
$out = HC_Html_Factory::widget('list')
	->add_attr('class', array('list-unstyled'))
	->add_attr('class', array('list-separated'))
	;

$out->add_item(
	$form->input('notifications_email_skip')
		->set_label( HCM::__('Skip Notification Email') )
	);

echo $out->render();
?>