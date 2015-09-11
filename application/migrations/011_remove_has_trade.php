<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_remove_has_trade extends CI_Migration {
	public function up()
	{
		if( $this->db->field_exists('has_trade', 'shifts') ){
			$this->dbforge->drop_column(
				'shifts',
				'has_trade'
				);

			if( $this->db->table_exists('logaudit') ){
				$this->db->where('property_name', 'has_trade');
				$this->db->where('object_class', 'shift');
				$this->db->delete('logaudit');
			}
		}
	}

	public function down()
	{
	}
}