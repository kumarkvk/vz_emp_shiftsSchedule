<?php
class HC_Form_Input_Date extends HC_Form_Input_Text
{
	protected $options = array();

	function add_option( $k, $v )
	{
		$this->options[$k] = $v;
	}
	function options()
	{
		return $this->options;
	}

	function render()
	{
		$name = $this->name();
		$value = $this->value();
		$id = 'nts-' . $name;

		$t = HC_Lib::time();
		$value ? $t->setDateDb( $value ) : $t->setNow();
		$value = $t->formatDate_Db();

		$out = HC_Html_Factory::widget('container');

	/* hidden field to store our value */
		$hidden = HC_Html_Factory::input('hidden')
			->set_name( $name )
			->set_value( $value )
			->set_id($id)
			;
		$out->add_item( $hidden );

	/* text field to display */
		$display_name = $name . '_display';
		$display_id = 'nts-' . $display_name;
		$datepicker_format = $t->formatToDatepicker();
		$display_value = $t->formatDate();

		$text = HC_Html_Factory::input('text')
			->set_name( $display_name )
			->set_value( $display_value )
			->set_id($display_id)
			->add_attr('data-date-format', $datepicker_format)
			->add_attr('data-date-week-start', $t->weekStartsOn)
			->add_attr( 'style', 'width: 8em' )
			;
		$out->add_item( $text );

	/* JavaScript to make it work */
		$js_options = array();
		
		$options = $this->options();
		foreach( $options as $k => $v )
		{
			switch( $k )
			{
				case 'startDate':
					if( $v > $value )
					{
						$value = $v;
					}
					$t->setDateDb( $v );
					$v = $t->formatDate();
					break;
			}
			$js_options[] = "$k: \"$v\"";
		}

		$js_options[] = "weekStart: " . $t->weekStartsOn;
		$js_options = join( ",\n", $js_options );

		$script = HC_Html_Factory::element( 'script' );
		$script->add_attr( 'language', 'JavaScript' );

		$cal_language = array(
			'days'			=> array( HCM::__('Sun'), HCM::__('Mon'), HCM::__('Tue'), HCM::__('Wed'), HCM::__('Thu'), HCM::__('Fri'), HCM::__('Sat'), HCM::__('Sun') ),
			'daysShort'		=> array( HCM::__('Sun'), HCM::__('Mon'), HCM::__('Tue'), HCM::__('Wed'), HCM::__('Thu'), HCM::__('Fri'), HCM::__('Sat'), HCM::__('Sun') ),
			'daysMin'		=> array( HCM::__('Sun'), HCM::__('Mon'), HCM::__('Tue'), HCM::__('Wed'), HCM::__('Thu'), HCM::__('Fri'), HCM::__('Sat'), HCM::__('Sun') ),
			'months'		=> array( HCM::__('Jan'), HCM::__('Feb'), HCM::__('Mar'), HCM::__('Apr'), HCM::__('May'), HCM::__('Jun'), HCM::__('Jul'), HCM::__('Aug'), HCM::__('Sep'), HCM::__('Oct'), HCM::__('Nov'), HCM::__('Dec') ),
			'monthsShort'	=> array( HCM::__('Jan'), HCM::__('Feb'), HCM::__('Mar'), HCM::__('Apr'), HCM::__('May'), HCM::__('Jun'), HCM::__('Jul'), HCM::__('Aug'), HCM::__('Sep'), HCM::__('Oct'), HCM::__('Nov'), HCM::__('Dec') ),
			'today'			=> 'Today',
			'clear'			=> 'Clear',
			);

		$cal_language_js_code = array();
		foreach( $cal_language as $k => $v ){
			$cal_language_js_code_line = '';

			$cal_language_js_code_line .= $k . ': ';
			if( is_array($v) ){
				$cal_language_js_code_line .= '[';
				$cal_language_js_code_line .= join(', ', array_map(create_function('$v', 'return "\"" . $v . "\"";'), $v));
				$cal_language_js_code_line .= ']';
			}
			else {
				$cal_language_js_code_line .= '"' . $v . '"';
			}
			$cal_language_js_code[] = $cal_language_js_code_line;
		}
		$cal_language_js_code = join(",\n", $cal_language_js_code);
		// echo $cal_language_js_code;

		$js_code = <<<EOT

;(function($){
	$.fn.datepicker.dates['en'] = {
		$cal_language_js_code
	};
}(jQuery));

jQuery('#$display_id').datepicker({
	$js_options,
	dateFormat: '$datepicker_format',
	autoclose: true,
	})
	.on('changeDate', function(ev)
		{
		var dbDate = 
			ev.date.getFullYear() 
			+ "" + 
			("00" + (ev.date.getMonth()+1) ).substr(-2)
			+ "" + 
			("00" + ev.date.getDate()).substr(-2);
		jQuery('#$id').val( dbDate );
		});

EOT;
		$script->add_child( $js_code );
		$out->add_item( $script );

		$return = $this->decorate( $out->render() );
		return $return;
	}
}
