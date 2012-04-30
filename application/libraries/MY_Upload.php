<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * My Upload Class
 *
 * @package		Application
 * @subpackage	Libraries
 * @category	Validation
 * @author		Andreas Gehle
 */
class MY_Upload extends CI_Upload {	
	
	public $min_width	= 0;
    public $min_height	= 0;
	
	/**
	 * Initialize preferences
	 *
	 * Same as in parent class, but also initialize min_width and min_height
	 * 
	 * @param	array
	 * @return	void
	 */
	public function initialize($config = array())
	{
		$defaults = array(
							'max_size'					=> 0,
							'max_width'					=> 0,
							'max_height'				=> 0,
							'min_width'					=> 0,
							'min_height'				=> 0,
							'max_filename'				=> 0,
							'max_filename_increment'	=> 100,
							'allowed_types'				=> "",
							'file_temp'					=> "",
							'file_name'					=> "",
							'orig_name'					=> "",
							'file_type'					=> "",
							'file_size'					=> "",
							'file_ext'					=> "",
							'upload_path'				=> "",
							'overwrite'					=> FALSE,
							'encrypt_name'				=> FALSE,
							'is_image'					=> FALSE,
							'image_width'				=> '',
							'image_height'				=> '',
							'image_type'				=> '',
							'image_size_str'			=> '',
							'error_msg'					=> array(),
							'mimes'						=> array(),
							'remove_spaces'				=> TRUE,
							'xss_clean'					=> FALSE,
							'temp_prefix'				=> "temp_file_",
							'client_name'				=> ''
						);


		foreach ($defaults as $key => $val)
		{
			if (isset($config[$key]))
			{
				$method = 'set_'.$key;
				if (method_exists($this, $method))
				{
					$this->$method($config[$key]);
				}
				else
				{
					$this->$key = $config[$key];
				}
			}
			else
			{
				$this->$key = $val;
			}
		}

		// if a file_name was provided in the config, use it instead of the user input
		// supplied file name for all uploads until initialized again
		$this->_file_name_override = $this->file_name;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Minimum Image Height
	 *
	 * @param	integer
	 * @return	void
	 */
	public function set_min_height($n)
	{
		$this->min_height = ((int) $n < 0) ? 0: (int) $n;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Minimum Image Weight
	 *
	 * @param	integer
	 * @return	void
	 */
	public function set_min_weight($n)
	{
		$this->min_weight = ((int) $n < 0) ? 0: (int) $n;
	}

	// --------------------------------------------------------------------

	/**
	 * Verify that the image is within the allowed width/height
	 *
	 * @return	bool
	 */
	public function is_allowed_dimensions()
	{
		if ( ! $this->is_image())
		{
			return TRUE;
		}

		if (function_exists('getimagesize'))
		{
			$D = @getimagesize($this->file_temp);

			if ($this->max_width > 0 AND $D['0'] > $this->max_width)
			{
				return FALSE;
			}

			if ($this->max_height > 0 AND $D['1'] > $this->max_height)
			{
				return FALSE;
			}

			if ($this->min_width > 0 AND $D['0'] < $this->min_width)
			{
				return FALSE;
			}

			if ($this->min_height > 0 AND $D['1'] < $this->min_height)
			{
				return FALSE;
			}

			return TRUE;
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Perform file upload
	 * 
	 * Can handle multi uploads
	 *
	 * @return	bool
	 */
	public function do_multi_upload($field = 'userfile')
	{
		$errors = array();
		$upload_data = array();
		$multi_upload = FALSE;
		
		// Is $_FILES[$field] set? If not, no reason to continue.
		if ( ! isset($_FILES[$field]))
		{
			$this->set_error('upload_no_file_selected');
			return array();
		}
		
		//Detect multiple upload files
		if(is_array($_FILES[$field]['name']))
		{
			$multi_upload = true;
			//Store FILES input for rebuild after manipulation
			$files = $_FILES[$field];
		}
		
		//Perform uploads
		for($i = 0, $count = count($files['name']); $i < $count; $i++)
		{
			//Manipulate FILES array
			if($multi_upload)
			{
				$_FILES[$field] = array(
					'name' => $files['name'][$i], 
					'type' => $files['type'][$i], 
					'tmp_name' => $files['tmp_name'][$i], 
					'error' => $files['error'][$i], 
					'size' => $files['size'][$i]
				);
			}
		
			if($this->do_upload($field))
			{
				//Get upload data
				$upload_data[] = $this->data();
			}
			else
			{
				if($multi_upload)
				{
					//Get errors and flush them for the next upload
					$errors[] = array('name' => $this->file_name, 'error' => $this->error_msg);
					$this->error_msg = array();
				}
			}
		}
		
		if($multi_upload)
		{
			//Set back FILES array
			$_FILES[$field] = $files;
			
			//Set errors for display_errors
			$this->error_msg = $errors;
		}
		
		return $upload_data;
	}

	// --------------------------------------------------------------------

	/**
	 * Display the error message
	 * For multi file upload: suffix ' (file.ext)'
	 *
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	public function display_errors($open = '<p>', $close = '</p>')
	{
		if(count($this->error_msg) > 0)
		{
			//Multiupload will have multidimensional array
			if(!is_array($this->error_msg[0]))
			{
				return $open.implode($close.$open, $this->error_msg).$close;
			}
			else
			{
				$display_errors = array();
				foreach($this->error_msg as $file)
				{
					foreach($file['error'] as $error)
					{
						$display_errors[] = $error.' ('.$file['name'].')';
					}
				}
				
				return $open.implode($close.$open, $display_errors).$close;
			}
		}
		
		return '';
	}
}

/* End of file MY_Upload.php */
/* Location: ./application/libraries/MY_Upload.php */