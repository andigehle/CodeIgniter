<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class User_Controller extends MY_Controller {
	
	function __construct()
	{
		parent::__construct();
		
		$this->load->library('user/auth');
		
		if(!$this->auth->logged_in())
		{
			redirect('user/login');
		}
		
		if(!$this->auth->is_admin())
		{
			show_error('Sorry, but you have no permission for this!');
		}
		
		$this->template->set_theme('admin');
	}
	
}