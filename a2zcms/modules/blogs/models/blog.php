<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
Author: Stojan Kukrika
Version: 1.0
*/

// ------------------------------------------------------------------------
class Blog extends DataMapper {
	
	function __construct($id = NULL)
    {
        parent::__construct($id);
    }
	var $has_many = array("BlogBlogCategorie");
    public $validation = array(
		'title' => array(
			'rules' => array('required', 'trim', 'max_length' => 255)
		)
	);
	
}

?>