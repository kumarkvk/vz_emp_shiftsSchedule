<?php
$link = HC_Lib::link('conf/admin/update/' . $tab);

$this->layout->set_partial(
	'header',
	HC_Html::page_header(
		HC_Html_Factory::element('h2')
			->add_child(HCM::__('Settings'))
		)
	);

/* check if we need tabs */
$these_fields = array_keys($fields);
if( count($tabs) > 1 ){
	$menubar = HC_Html_Factory::widget('list');
	$menubar->add_attr('class', array('nav', 'nav-tabs'));

	$app_conf = HC_App::app_conf();
	foreach( $tabs as $tk => $ms ){
		$conf_key = 'menu_conf_settings:' . $tk;
		$tab_label = $app_conf->conf( $conf_key );
		if( $tab_label === FALSE ){
			$tab_label = $tk;
		}

		$menubar->add_item(
			$tk,
			HC_Html_Factory::widget('titled', 'a')
				->add_attr('href', HC_Lib::link('conf/admin/index/' . $tk))
				->add_child( $tab_label )
			);
	}

	$menubar->set_active( $tab );
	echo $menubar->render();

	$these_fields = $tabs[$tab];
}
?>
<?php
$display_form = HC_Html_Factory::widget('form')
	->add_attr('action', $link->url() )
	->add_attr('class', 'form-horizontal')
	->add_attr('class', 'form-condensed')
	;

foreach( $these_fields as $fn ){
	$f = $fields[$fn];
	
	$field_input = $form->input($fn);
	switch( $f['type'] ){
		case 'dropdown':
			$field_input->set_options( $f['options'] );
			break;
		case 'checkbox_set':
			$field_input->set_options( $f['options'] );
			break;
	}

	$display_form->add_item(
		HC_Html_Factory::widget('label_row')
			->set_label( $f['label'] )
			->set_content( $field_input )
			->set_error( $form->input($fn)->error() )
		);
}

$buttons = HC_Html_Factory::widget('list')
	->add_attr('class', array('list-inline', 'list-separated') )
	;
$buttons->add_item(
	HC_Html_Factory::element('input')
		->add_attr('type', 'submit')
		->add_attr('class', array('btn', 'btn-default'))
		->add_attr('title', HCM::__('Save') )
		->add_attr('value', HCM::__('Save') )
	);
$display_form->add_item(
	HC_Html_Factory::widget('label_row')
		->set_content( $buttons )
	);

echo $display_form->render();
?>