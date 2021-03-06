<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
Author: Stojan Kukrika
Version: 1.0
*/

// ------------------------------------------------------------------------

class Admin extends Administrator_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model(array("Model_plugin"));		
	}
	
	function index(){
		if (!$this->session->userdata("manage_plugins")){
			redirect($_SERVER['HTTP_REFERER']);
		}
		$data['view'] = 'index';
		
        $plugin = $this->Model_plugin->getall();
				
		$temp = array();
		foreach ($plugin as $item) {
			$temp[]=$item->name;
		}
			
		foreach (glob( FCPATH.'/a2zcms/modules' . '/*', GLOB_ONLYDIR) as $dir) {
			$dir = str_replace(FCPATH.'/a2zcms/modules/', '', $dir);
			if(!in_array($dir,$temp) && $dir!='install' && $dir!='testmodule' && $dir!='offline' && $dir!='menu' && $dir!='adminmenu')
			$plugin[] =(object) array('name' => $dir, 'id'=>0,
			'title' => ucfirst($dir), 'pluginversion' => '', 'created_at' => '', 'can_uninstall' =>0, 'not_installed'=>TRUE);
		}
			
        $data['content'] = array(
            'navigation' => $plugin
        );
 
        $this->load->view('adminpage', $data);
	}
	
	function plugin_reorder()
	{
		if (!$this->session->userdata("manage_plugins")){
			redirect($_SERVER['HTTP_REFERER']);
		}
		$list = $this->input->get('list');
		$items = explode(",", $list);
		$order = 1;
		foreach ($items as $value) {
			if ($value != '') {

				$data = array(
               			'order' => $order,
               			'updated_at' => date("Y-m-d H:i:s")
           		);
				$this->Model_plugin->update($data,$value);
				$order++;
			}
		};
	}
	
	
	
	function dashboard()
	{
		$data['view'] = 'dashboard';
		
		$data['content'] = array(
            'navigation' =>  $this->Model_plugin->menu_admin()
        );
		$this->load->view('adminpage', $data);
	}
	

}