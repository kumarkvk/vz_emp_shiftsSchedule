<?php
$out = HC_Html_Factory::widget('list')
	->add_attr('class', 'list-unstyled')
	;

if( $subheader ){
	$out->add_item(
		HC_Html_Factory::element('h4')
			->add_child($subheader)
		);
	$out->add_divider();
}
$out->add_item( $content );
echo $out->render();
?>