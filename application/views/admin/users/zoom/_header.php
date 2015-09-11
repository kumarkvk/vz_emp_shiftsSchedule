<?php
echo HC_Html::page_header(
	HC_Html_Factory::widget('list')
		->add_attr('class', 'list-unstyled')
		->add_item( 
			HC_Html_Factory::element('h2')
				->add_child( $object->present_title() )
			)
		->add_item(
			$object->present_title_misc()
			)
	);
?>