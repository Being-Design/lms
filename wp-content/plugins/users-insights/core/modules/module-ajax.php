<?php

/**
 * Includes the AJAX functionality for the Modules page
 */
class USIN_Module_Ajax extends USIN_Ajax{

	protected $user_capability;
	protected $module_options;
	protected $nonce_key;

	/**
	 * @param USIN_Module_Options $module_options  the module options object
	 * @param string $user_capability the required user capability to access the modules page
	 * @param string $nonce_key       the nonce key for the security checks
	 */
	public function __construct($module_options, $user_capability, $nonce_key){
		$this->module_options = $module_options;
		$this->user_capability = $user_capability;
		$this->nonce_key = $nonce_key;
	}

	/**
	 * Registers the required actions hooks.
	 */
	public function add_actions(){
		add_action('wp_ajax_usin_add_license', array($this, 'add_license'));
		add_action('wp_ajax_usin_deactivate_license', array($this, 'deactivate_license'));
		add_action('wp_ajax_usin_activate_module', array($this, 'activate_module'));
		add_action('wp_ajax_usin_deactivate_module', array($this, 'deactivate_module'));
		add_action('wp_ajax_usin_refresh_license_status', array($this, 'refresh_license_status'));
	}
	
	/**
	 * Handler for the Add & Activate License functionality.
	 */
	public function add_license(){
		if(!$this->validate_request(true)){
			exit;
		}

		$res = array();

		if(empty($_POST['license_key']) || empty($_POST['module_id'])){
			$this->print_error_and_exit(__('Invalid request - required data missing', 'usin'));
		}

		$license_key = $_POST['license_key'];
		$module_id = $_POST['module_id'];


		$license_manager = new USIN_Module_License();
		$license_data = $license_manager->activate_license($license_key, $module_id);

		if(!$license_data){
			$this->print_error_and_exit(__('Remote HTTP request failed. Please try again later.', 'usin'));
		}

		if($license_data->success === true && $license_data->license === 'valid'){
			$saved_options = $this->module_options->set_license_active(
				$module_id, $license_key, $license_data->item_name, $license_data->expires);
			$res = array(
				'success' => true,
				'options' => $saved_options
			);
		}else{
			$error = isset($license_data->error_msg) ? $license_data->error_msg : __('Invalid license', 'usin');
			$res = array(
				'error' => $error
			);
		}

		echo json_encode($res);
		exit;
	}

	/**
	 * Handler for the Deactivate & Remove License functionality.
	 */
	public function deactivate_license(){
		if(!$this->validate_request(true)){
			exit;
		}

		$res = array();

		if(empty($_POST['license_key']) || empty($_POST['module_id'])){
			$this->print_error_and_exit(__('Invalid request - required data missing', 'usin'));
		}

		$license_key = $_POST['license_key'];
		$module_id = $_POST['module_id'];


		$license_manager = new USIN_Module_License();
		$license_data = $license_manager->deactivate_license($license_key, $module_id);

		if(!$license_data){
			$this->print_error_and_exit(__('Remote HTTP request failed. Please try again later.', 'usin'));
		}

		if( ($license_data->success === true && $license_data->license === 'deactivated') ||
			(!$license_data->success && $license_data->license == 'failed' && empty($license_data->item_name)) ){  //the license doesn't exist anymore, just remove it from the options
			$saved_options = $this->module_options->set_license_inactive(
				$module_id, $license_key);
			$res = array(
				'success' => true,
				'options' => $saved_options
			);
		}else{
			$error = isset($license_data->error_msg) ? $license_data->error_msg : __('Invalid license', 'usin');
			$res = array(
				'error' => $error
			);
		}

		echo json_encode($res);
		exit;
	}
	
	public function refresh_license_status(){
		if(!$this->validate_request(true)){
			exit;
		}
		
		if(empty($_POST['module_id'])){
			$this->print_error_and_exit(__('Invalid request - missing module ID', 'usin'));
		}
		
		$module_id = $_POST['module_id'];
		$license_manager = new USIN_Module_License();
		$license_key = $this->module_options->get_license($module_id);
		
		$license_data = $license_manager->load_license_status($license_key, $module_id);
		
		if(!$license_data){
			$this->print_error_and_exit(__('Remote HTTP request failed. Please try again later.', 'usin'));
		}
		
		
		if( $license_data->success === true ){
			$status = $license_data->license;
			$expires = $license_data->expires;
			$module_options = $this->module_options->update_license_status($module_id, $status, $expires);
			
			$res = array(
				'success' => true,
				'options' => $module_options
			);
			echo json_encode($res);
			exit;
			
		}else{
			$this->print_error_and_exit(__('Invalid license', 'usin'));
		}
	}

	/**
	 * Activates a module.
	 */
	public function activate_module(){
		if(!$this->validate_request(true)){
			exit;
		}

		if(isset($_POST['module_id'])){
			$this->module_options->activate_module($_POST['module_id']);
		}
		echo json_encode(array('success'=>true));
		exit;
	}


	/**
	 * Deactivates a module.
	 */
	public function deactivate_module(){
		if(!$this->validate_request(true)){
			exit;
		}

		if(isset($_POST['module_id'])){
			$this->module_options->deactivate_module($_POST['module_id']);
		}
		echo json_encode(array('success'=>true));
		exit;
	}


}