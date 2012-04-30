<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller{
	
	function __construct()
	{
		parent::__construct();
		
		if(ENVIRONMENT == 'development')
		{
			$this->output->enable_profiler(TRUE);
		}
		
        // If the user is using a mobile, use a mobile theme
        // $this->load->library('user_agent');
        // if( $this->agent->is_mobile() )
        // {
            // $this->template->set_theme('mobile');
        // }
	}
	
}