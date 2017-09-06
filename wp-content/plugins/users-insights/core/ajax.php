<?php

class USIN_Ajax{

	protected $user_capability;
	protected $nonce_key;

	protected function get_nonce(){
		return $_REQUEST['nonce'];
	}
	
	/**
	 * Deprecated
	 */
	protected function validate_request($check_nonce = false, $capability = null){
		$valid = $this->is_request_valid($check_nonce, $capability);
		
		if(is_wp_error($valid)){
			echo json_encode(array('error' => $valid->get_error_message()));
			return false;
		}
		
		return true;
	}
	
	private function is_request_valid($check_nonce = false, $capability = null){
		if($capability === null){
			$capability = $this->user_capability;
		}
		
		if(!current_user_can($capability)){
			return new WP_Error('usin_not_allowed',  __('You are not allowed to perform this action', 'usin'));
		}
		if($check_nonce && !wp_verify_nonce( $this->get_nonce(), $this->nonce_key )){
			return new WP_Error('usin_not_allowed',  __('Nonce did not verify', 'usin'));
		}
		return true;
	}
	
	/**
	 * Deprecated
	 */
	protected function print_error_and_exit($error){
		$res = array('error' => $error);
		echo json_encode($res);
		exit;
	}

	protected function get_request_array($key){
		$arr = null;
		if(isset($_GET[$key])){
			$conv_arr = json_decode(stripcslashes($_GET[$key]));
			if(!empty($conv_arr)){
				$arr = $conv_arr;
			}
		}
		return $arr;
	}
	
	protected function array_values_to_integer($arr){
		if(empty($arr)){
			return array();
		}
		
		$new_arr = array();
		foreach ($arr as $key => $value) {
			$new_arr[$key] = intval($value);
		}
		
		return $new_arr;
	}
	
	/*
	New functions since 3.1:
	 */
	
	protected function validate_required_post_params($required_params){
		foreach ($required_params as $param ) {
			if(empty($_POST[$param])){
				$this->respond_error( __('Missing required param: ', 'usin').$param);
			}
		}
		
	}
	
	protected function verify_request($capability){
		$valid = $this->is_request_valid(true, $capability);
		if(is_wp_error($valid)){
			$this->respond_error( $valid->get_error_message() );
		}
		
		return true;
	}
	
	protected function respond_error($message = 'Failed to execute your request'){
		status_header(400);
		wp_send_json(array('error' => $message));
	}
	
	protected function respond_success($data = array()){
		$res = empty($data) || $data === true ? array('success' => true) : $data;
		wp_send_json($res);
	}
	
	protected function respond($res){
		if(is_wp_error($res)){
			$this->respond_error($res->get_error_message());
		}elseif($res === false){
			$this->respond_error();
		}else{
			$this->respond_success($res);
		}
	}

}