<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class User_Controller extends Public_Controller {
	
	function __construct()
	{
		parent::__construct();
		
		$this->load->library('user/auth');
		
		if(!$this->auth->logged_in())
		{
			redirect('user/login');
		}
	}
	
}