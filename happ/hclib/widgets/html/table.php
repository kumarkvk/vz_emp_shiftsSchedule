<?php
include_once( dirname(__FILE__) . '/container.php' );
class HC_Html_Widget_Table extends HC_Html_Widget_Container
{
	protected $header = array();
	protected $rows = array();
	protected $row_attr = array();
	protected $cell_attr = array();
	protected $engine = 'table';

	public function set_cell( $rid, $cid, $value )
	{
		$this->rows[ $rid ][ $cid ] = $value;
	}

	public function set_engine( $engine )
	{
		$this->engine = $engine;
		return $this;
	}
	public function engine()
	{
		return $this->engine;
	}

	function add_row($row)
	{
		$this->rows[] = $row;
	}
	function rows()
	{
		return $this->rows;
	}

	public function add_row_attr( $rid, $attr ){
		if( ! isset($this->row_attr[$rid]) ){
			$this->row_attr[$rid] = array();
		}
		$this->row_attr[$rid] = array_merge( $this->row_attr[$rid], $attr );
		return $this;
	}

	public function add_cell_attr( $rid, $cid, $attr ){
		if( ! isset($this->cell_attr[$rid]) ){
			$this->cell_attr[$rid] = array();
		}
		if( ! isset($this->cell_attr[$rid][$cid]) ){
			$this->cell_attr[$rid][$cid] = array();
		}
		$this->cell_attr[$rid][$cid] = array_merge( $this->cell_attr[$rid][$cid], $attr );
		return $this;
	}

	public function row_attr( $rid )
	{
		$return = isset($this->row_attr[$rid]) ? $this->row_attr[$rid] : array();
		return $return;
	}

	public function cell_attr( $rid, $cid )
	{
		$return = isset($this->cell_attr[$rid][$cid]) ? $this->cell_attr[$rid][$cid] : array();
		return $return;
	}

	function set_header( $header )
	{
		$this->header = $header;
		return $this;
	}
	function header()
	{
		return $this->header;
	}

	function render()
	{
		switch( $this->engine() ){
			case 'table':
				$t_out = HC_Html_Factory::element( 'table' );
				$t_tr = HC_Html_Factory::element('tr');
				$t_td = HC_Html_Factory::element('td');
				break;

			case 'div':
				$t_out = HC_Html_Factory::element('div')
					->add_attr('class', 'div-table')
					;
				$t_tr = HC_Html_Factory::element('div')
					->add_attr('class', 'div-table-row')
					;
				$t_td = HC_Html_Factory::element('div')
					->add_attr('class', 'div-table-cell')
					// ->add_attr('class', 'cal-cell-day')
					->add_attr('class', 'squeeze-in')
					// ->add_attr('style', 'text-align: center;')
					// ->add_attr('class', 'noborder')
					;

				break;
		}

		$out = clone $t_out;
		$attr = $this->attr();
		foreach( $attr as $k => $v ){
			$out->add_attr( $k, $v );
		}
		// $out->add_attr('border', 1);

		$header = $this->header();
		if( $header ){
			$tr = HC_Html_Factory::element('tr');
			foreach( $header as $r ){
				$td = HC_Html_Factory::element('th');
				$td->add_child( $r );
				$tr->add_child( $td );
				}
			$out->add_child( $tr );
		}

		$rows = $this->rows();
		foreach( array_keys($rows) as $rid ){
			$row = $rows[$rid];
			$tr = clone $t_tr;

			$attr = $this->row_attr( $rid );
			foreach( $attr as $k => $v ){
				$tr->add_attr( $k, $v );
			}

			foreach( array_keys($row) as $cid ){
				$cell = $row[$cid];
				$td = clone $t_td;
				$td->add_child( $cell );

				$attr = $this->cell_attr( $rid, $cid );
				foreach( $attr as $k => $v ){
					$td->add_attr( $k, $v );
				}

				$tr->add_child( $td );
			}

			$out->add_child( $tr );
		}

		return $out->render();
	}
}
?>