<?php
$out = HC_Html_Factory::widget('container')
	->add_item( HC_Html::icon('list') )
	->add_item( 'Login Log' )
	;
echo $out;