<?php
class SFT_Html_Widget_Shift_View extends HC_Html_Element
{
	private $shift = NULL;
	private $iknow = array();
	private $wide = TRUE;
	private $nolink = FALSE;
	private $new_window = FALSE;

	function init( $shift )
	{
		$this->set_shift( $shift );
	}

	function set_shift( $shift )
	{
		$this->shift = $shift;
		return $this;
	}
	function shift()
	{
		return $this->shift;
	}

	function set_nolink( $nolink = TRUE )
	{
		$this->nolink = $nolink;
		return $this;
	}
	function nolink()
	{
		return $this->nolink;
	}

	function set_new_window( $new_window = TRUE )
	{
		$this->new_window = $new_window;
		return $this;
	}
	function new_window()
	{
		return $this->new_window;
	}

	function set_wide( $wide = TRUE )
	{
		$this->wide = $wide;
		return $this;
	}
	function wide()
	{
		return $this->wide;
	}

	function set_iknow( $iknow )
	{
		$this->iknow = $iknow;
		return $this;
	}
	function iknow()
	{
		return $this->iknow;
	}

	function render()
	{
		$sh = $this->shift();
		$t = HC_Lib::time();

		$titles = array();

		$iknow = $this->iknow();
		$wide = $this->wide();

		$use_color = FALSE;
		$use_color = TRUE;
		if( $wide && ($wide === 'mini') ){
			$use_color = TRUE;
		}

		if( in_array($sh->type, array($sh->_const("TYPE_TIMEOFF"))) ){
			$display = array( 'date', 'time', 'user', 'location' );
		}
		else {
			if( (! $wide) OR ($wide === 'mini') ){
				$display = array( 'date', 'time', 'location', 'user' );
			}
			elseif( $wide ){
				$display = array( 'date', 'time', 'user', 'location' );
			}
		}

		foreach( $iknow as $ik ){
			$display = HC_Lib::remove_from_array($display, $ik);
		}

		// if( in_array($sh->type, array($sh->_const("TYPE_TIMEOFF"))) ){
			// $display = HC_Lib::remove_from_array($display, 'location');
		// }

		foreach( $display as $ds ){
			$title_view = '';

			switch( $ds ){
				case 'date':
					$title_view = $sh->present_date(HC_PRESENTER::VIEW_RAW);
					break;

				case 'time':
					$title_view = $sh->present_time();
					break;

				case 'location':
					if( in_array($sh->type, array($sh->_const("TYPE_TIMEOFF"))) ){
						$title_view = '';
						// $title_view = HCM::__('Timeoff');
						// $title_view = $sh->present_location();
					}
					else {
						$title_view = $sh->present_location();
					}
					break;

				case 'user':
					if( ($sh->type == $sh->_const('TYPE_TIMEOFF')) && (! in_array('time', $display)) ){
						$title_view = $sh->present_type(HC_PRESENTER::VIEW_HTML_ICON) . $sh->present_user(HC_PRESENTER::VIEW_RAW);
					}
					else {
						// $titles[] = $sh->present_user();
						if( $sh->user_id ){
							$title_view = $sh->present_user(HC_PRESENTER::VIEW_RAW);
						}
						else {
							$title_view = $sh->present_user();
						}
					}
					break;
			}

			// if( $title_view ){
				$titles[] = $title_view;
			// }
		}

		$wrap = HC_Html_Factory::element('div')
			->add_attr('class', array('alert', 'display-block'))
			->add_attr('class', array('alert-default-o'))
			// ->add_attr('class', array('alert-success-o'))
			->add_attr('class', array('no-underline'))
			// ->add_attr('class', array('alert-condensed'))
			->add_attr('class', array('alert-condensed2'))
			->add_attr('class', array('text-smaller'))
			->add_attr('class', array('squeeze-in'))
			;

		foreach( $sh->present_status_class() as $status_class ){
			// $wrap->add_attr('class', 'alert-' . $status_class);
		}

	/* background color depends on location */
		if( $use_color ){
			$color = $sh->location->present_color();
		}
		else {
			$type = $sh->type; 
			switch( $type ){
				case $sh->_const('TYPE_TIMEOFF'):
					$wrap->add_attr('class', array('alert-archive'));
					$color = '#ddd';
					break;
				default:
					$wrap->add_attr('class', array('alert-success-o'));
					$color = '#dff0d8';
					break;
			}
		}

		if( $sh->status == $sh->_const('STATUS_DRAFT') ){
			$color1 = HC_Lib::adjust_color_brightness( $color, 0 );
			$color2 = HC_Lib::adjust_color_brightness( $color, 20 );

			// $color1 = '#fff';
			// $color2 = '#eee';

			$wrap->add_attr('style',
				"background: repeating-linear-gradient(
					-45deg,
					$color1,
					$color1 6px,
					$color2 6px,
					$color2 12px
					);
				"
				);
		}
		else { 
			$wrap->add_attr('style', 'background-color: ' . $color . ';');
			// $wrap->add_attr('class', 'alert-success');
		}
//		echo $color;

	/* ID */
		if( in_array('id', $iknow) ){
			$wrap->add_child($sh->present_id());
		}

	/* build link title */
		$nolink = $this->nolink();
		$new_window = $this->new_window();
		$a_link = HC_Html_Factory::widget('titled', 'a');

		$link_to = 'shifts/zoom/index/id/' . $sh->id;
		$a_link->add_attr('href', HC_Lib::link($link_to)->url());
		if( ! $new_window ){
			$a_link->add_attr('class', 'hc-flatmodal-loader');
		}
		else {
			$a_link->add_attr('target', '_blank');
			$a_link->add_attr('class', 'hc-parent-loader');
		}

		if( $nolink ){
			$a_title = HC_Html_Factory::widget('titled', 'span');
		}
		else {
			$a_title = HC_Html_Factory::widget('titled', 'a');
		}
		$a_title = HC_Html_Factory::widget('titled', 'span');

		$a_title
			->add_attr('class', array('squeeze-in'))
			;
		// $a_title->add_attr('style', 'border: red 1px solid;');
		// $a_title->add_attr('style', 'border-color: ' . $sh->location->present_color());
		if( $wide === 'mini' ){
			if( ! $nolink ){
				$final_ttl = clone $a_link;
				$final_ttl
					->add_child('&nbsp;')
					->add_attr('style', 'display: block;')
					;
				$final_ttl->add_attr('title', join(' ', $titles));
			}
			$a_title->add_child( $final_ttl );
		}
		else {
			if( count($display) > 1 ){
				if( $wide ){
					$titles2 = HC_Html_Factory::widget('grid');
					$titles2->set_slim();
					$grid_width = array(
						2	=> 6,
						3	=> 4,
						4	=> 3,
						5	=> 2,
						6	=> 2
						);
					$grid_width = isset($grid_width[count($display)]) ? $grid_width[count($display)] : 2;
					for( $ti = 0; $ti < count($titles); $ti++ ){
						$ttl = $titles[$ti];
						// next title is empty?
						if( ($ti < count($titles)-1) && (! strlen($titles[$ti+1])) ){
							$ti++;
							$grid_width += $grid_width;
						}

						$final_ttl = $ttl;
						if( ! $nolink ){
							$final_ttl = clone $a_link;
							$final_ttl->add_child( $ttl );
						}

						$titles2->add_item( 
							$final_ttl,
							$grid_width,
							array('class' => 'squeeze-in')
							);
					}
				}
				else {
					$titles2 = HC_Html_Factory::widget('list')
						->add_attr('class', 'list-unstyled')
						;
					$this_index = 0;
					foreach( $titles as $ttl ){
						if( ! strlen($ttl) ){
							continue;
						}

						$final_ttl = $ttl;
						if( ! $nolink ){
							$final_ttl = clone $a_link;
							$final_ttl->add_child( $ttl );
						}

						$titles2->add_item( $this_index, $final_ttl );
						$titles2->add_item_attr( $ttl, 'class', array('squeeze-in') );
						$this_index++;
					}
				}
				$a_title->add_attr('title', join(' ', $titles));
				$a_title->add_child( $titles2 );
			}
			else {
				$final_ttl = $titles;
				if( ! $nolink ){
					$final_ttl = clone $a_link;
					$final_ttl->add_child( $titles );
				}
				$final_ttl->add_attr('title', join(' ', $titles));
				$a_title->add_child( $final_ttl );
			}
		}

		$wrap->add_child($a_title);

	/* EXTENSIONS */
		$extensions = HC_App::extensions();
		$more_content = $extensions->set_skip($iknow)->run('shifts/quickview', $sh, $wrap);

		if( $wide !== 'mini' ){
			if( $more_content ){
				$more_wrap = HC_Html_Factory::widget('list')
					->add_attr('class', 'list-unstyled')
					->add_attr('class', 'list-separated')
					->add_attr('class', 'text-small')
					;
				$added = 0;
				foreach($more_content as $mck => $mc ){
					if( $mck && in_array($mck, $iknow) ){
						continue;
					}

					$more_wrap->add_item($mc);
					$added++;
				}
				if( $added ){
					$wrap->add_child($more_wrap);
				}
			}
		}

	/* THIS CHILDREN */
		if( $wide !== 'mini' ){
			$children = $this->children();
			foreach( $children as $child ){
				$wrap->add_child($child);
			}
		}

		$wrap->add_attr( 'class', 'common-link-parent' );
		return $wrap->render();
	}
}