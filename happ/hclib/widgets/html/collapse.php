<?php
include_once( dirname(__FILE__) . '/container.php' );
class HC_Html_Widget_Collapse extends HC_Html_Widget_Container
{
	private $title = '';
	private $content = '';
	private $default_in = FALSE;
	private $indented = TRUE;
	private $panel = NULL;
	protected $no_caret = TRUE;

	public function set_title( $title )
	{
		$this->title = $title;
		return $this;
	}
	public function title()
	{
		return $this->title;
	}

	function set_no_caret( $no_caret = TRUE )
	{
		$this->no_caret = $no_caret;
		return $this;
	}
	function no_caret()
	{
		return $this->no_caret;
	}

	function set_panel( $panel = TRUE )
	{
		$this->panel = $panel;
		return $this;
	}
	public function panel()
	{
		return $this->panel;
	}

	public function set_content( $content )
	{
		$this->content = $content;
		return $this;
	}
	public function content()
	{
		return $this->content;
	}

	public function set_default_in( $default_in = TRUE )
	{
		$this->default_in = $default_in;
		return $this;
	}
	public function default_in()
	{
		return $this->default_in;
	}

	public function set_indented( $indented = TRUE )
	{
		$this->indented = $indented;
		return $this;
	}
	public function indented()
	{
		return $this->indented;
	}

	public function render()
	{
		$panel = $this->panel();

		$out = HC_Html_Factory::element('ul')
			->add_attr('class', 'list-unstyled')
			->add_attr('class', 'collapse-panel')
			;

		$attr = $this->attr();
		foreach( $attr as $k => $v ){
			$out->add_attr( $k, $v );
		}

		if( $panel ){
			if( is_array($panel) ){
				foreach( $panel as $pnl ){
					$out->add_attr('class', array('panel', 'panel-' . $pnl));
				}
			}
			else {
				$out->add_attr('class', array('panel', 'panel-' . $panel));
			}
		}

		if( $this->indented() ){
			$out->add_attr('class', 'list-indented');
		}

	/* build trigger */
		$title = $this->title();
		if( 
			is_object($title) &&
			( $title->tag() == 'a' )
		){
			$trigger = $title;
		}
		else {
			$full_title = $title;
			$title = strip_tags($title);
			$title = trim($title);

			$trigger = HC_Html_Factory::widget('titled', 'a')
				->add_child( 
					$full_title
					)
				;
		}

		$trigger
			->add_attr('href', '#')
			->add_attr('class', 'hc-collapse-next')
			->add_attr('class', 'display-block')
			;

		if( ! $this->no_caret() ){
			$trigger
				->add_child( ' ' )
				->add_child(
					HC_Html_Factory::element('b')
						->add_attr('class', 'caret')
					)
				;
		}

		$wrap_trigger = HC_Html_Factory::element('li')
			->add_child( $trigger )
			;
		if( $panel ){
			$wrap_trigger
				->add_attr('class', 'panel-heading')
				;
		}

		$out->add_child(
			$wrap_trigger
			);

		$content = HC_Html_Factory::element('li')
			->add_attr('class', 'collapse')
			// ->add_child( $this->content() )
			;
		if( $panel ){
			$content->add_attr('class', 'panel-collapse');
			}
		if( $this->default_in() ){
			$content->add_attr('class', 'in');
		}

		if( $panel ){
			$content->add_child( 
				HC_Html_Factory::element('div')
					->add_attr('class', 'panel-body')
					->add_child( $this->content() )
				);
		}
		else {
			$content->add_child( $this->content() );
		}

		$out->add_child( $content );
		return $out->render();
	}
}
?>