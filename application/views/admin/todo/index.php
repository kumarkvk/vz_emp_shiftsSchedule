<?php
$out = HC_Html_Factory::widget('list')
	->add_attr('class', 'list-unstyled')
	->add_attr('class', 'list-separated')
	;

/* EXTENDED TABS */
$extensions = HC_App::extensions();
$more_content = $extensions->run('admin/todo');

foreach( $more_content as $subtab => $subtitle ){
	if( $subtitle ){
		$out->add_item(
			$subtab,
			$subtitle
			);
	}
}

if( ! $out->items() ){
	$out->add_item( HCM::__('No action needed') );
}

echo $out;
?>