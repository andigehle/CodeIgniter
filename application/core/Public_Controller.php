<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Public_Controller extends MY_Controller{
	
	function __construct()
	{
		parent::__construct();
		
		$this->load->helper('url');
		
		if($this->config->item('maintenance') === TRUE)
        {
            redirect('maintenance');
        }
	}
	
}