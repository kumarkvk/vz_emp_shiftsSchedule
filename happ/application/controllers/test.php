<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Test_HC_controller extends MY_HC_Controller {
//class Test_HC_controller extends MY_HC_Controller {
	function __construct()
	{
		parent::__construct();
		if( defined('NTS_DEVELOPMENT') ){
			$this->output->enable_profiler(TRUE);
		}
	}

	function index()
	{
		echo 'this is a test';
	}
}

/* End of file setup.php */
/* Location: ./application/controllers/setup.php */