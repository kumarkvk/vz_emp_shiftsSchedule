<?php
class HC_Form_Input_Timeframe extends HC_Form_Input_Composite
{
	function __construct( $name )
	{
		$this->fields['start'] = HC_Html_Factory::input( 'timepicker', $name . '_start' );
		$this->fields['end'] = HC_Html_Factory::input( 'timepicker', $name . '_end' );
	}

	function render()
	{
		$wrap = HC_Html_Factory::widget( 'list' )
			->add_attr('class', array('list-inline', 'list-separated'))
			;
		$wrap->add_item( $this->fields['start'] );
		$wrap->add_item( ' - ' );
		$wrap->add_item( $this->fields['end'] );

		return $this->decorate( $wrap->render() );
	}
}