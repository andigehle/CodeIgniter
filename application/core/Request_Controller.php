<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Request_Controller extends Public_Controller {
	
	private $csrf	= TRUE;
		
	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();
	}

	// --------------------------------------------------------------------
	
	/**
	 * Remap requests of post/ajax
	 * 
	 * Remap ajax requests to ajax_{$methode} and post requests to post_{$methode}
	 */	
	public function _remap($method, $params = array())
	{
		if($this->input->is_ajax_request())
		{
	    	if(method_exists($this, '_ajax_'.$method))
	    	{
	    		//$this->output->parse_exec_vars = FALSE;
				$this->output->enable_profiler(FALSE);
				$this->output->set_content_type('application/json');
	        	return call_user_func_array(array($this, '_ajax_'.$method), $params);
	    	}
			else 
			{
            	show_error('No ajax-handler.');
			}
		}
		elseif($this->input->post())
		{
	    	if(method_exists($this, '_post_'.$method))
	    	{
	        	return call_user_func_array(array($this, '_post_'.$method), $params);
	    	}
			else 
			{
            	show_error('No post-handler.');
			}
		}
		else
		{
		    if (method_exists($this, $method))
		    {
		        return call_user_func_array(array($this, $method), $params);
		    }
            show_404();
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Allow multiple ajax requests
	 * 
	 * Set to TRUE if multiple ajax requests should be allow.
	 * If only one ajax request should be allow set to FALSE.
	 * 
	 * !WARNING: To handle multiple ajax request csrf protection will be disabled
	 * 
	 * @access public
	 * @param boolean Sets csrf protection
	 */	
	public function set_multi_ajax($multi)
	{
		if($this->input->is_ajax_request())
		{
			$this->csrf = !$multi;
			if($this->csrf)
			{
				$this->load->library('security');
			}
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Override output
	 * 
	 * For ajax request the output data is encoded to json
	 * else the data will be echoed.
	 * 
	 * @access public
	 * @param data Output
	 */	
	public function _output($output)
	{
		if($this->input->is_ajax_request())
		{
			$json = array();
			$json['data'] = $output;
			$json['errors'] = array();
			
			if($this->load->is_loaded('form_validation') && $form_errors = $this->form_validation->error_array())
			{
				$json['errors'] = $form_errors;
			}
			
			if($this->load->is_loaded('upload') && $upload_errors = $this->upload->error_msg)
			{
				//Rewrite errors
				if(isset($upload_errors[0]) && is_array($upload_errors[0]))
				{
					$display_errors = array();
					foreach($upload_errors as $file)
					{
						foreach($file['error'] as $error)
						{
							$display_errors[] = $error.' ('.$file['name'].')';
						}
					}
					$upload_errors = $display_errors;
				}
				$json['errors'] = array_merge($json['errors'],$upload_errors);
			}
			
			if(!$this->csrf)
			{
				form_open();
				$json['csrf_token_name'] = $this->security->get_csrf_token_name();
				$json['csrf_hash'] = $this->security->get_csrf_hash();
				$this->security->csrf_set_cookie();
			}
		
			$output = json_encode($json);
		}
		
		echo $output;
	}
	
}