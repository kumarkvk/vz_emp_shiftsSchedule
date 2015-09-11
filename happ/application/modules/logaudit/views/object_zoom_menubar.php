<?php
$out = HC_Html_Factory::widget('container')
	->add_item( HC_Html::icon('list-ul') )
	->add_item( HCM::__('History') )
	;
echo $out;