<?php

/**
 * Incudes functionality for license management, such as activation
 * and deactivation.
 */
class USIN_Module_License{

	protected $remote_url = 'http://usersinsights.com/';

	/**
	 * Actuvates a license via a remote request to the UsersInsights site.
	 * @param  string $license_key the license key
	 * @param  string $module_id   the module id
	 * @return array              the result of the request for activation
	 */
	public function activate_license($license_key, $module_id){
		$args = array( 
			'usinr_action'=> 'activate_license', 
			'license' 	=> $license_key, 
			'usin_key' => $module_id, // the name of our product in EDD
			'url'       => home_url()
		);

		// Call the custom API.
		$response = wp_remote_get( 
			add_query_arg( $args, $this->remote_url ), 
			array( 'timeout' => 15, 'sslverify' => false ) 
		);

		if ( is_wp_error( $response ) ){
			return false;
		}

		return json_decode( wp_remote_retrieve_body( $response ) );
	}

	/**
	 * Deactivates a license via a remote request to the UsersInsights site.
	 * @param  string $license_key the license key
	 * @param  string $module_id   the module id
	 * @return array              the result of the request for deactivation
	 */
	public function deactivate_license($license_key, $module_id){
		$args = array( 
			'usinr_action'=> 'deactivate_license', 
			'license' 	=> $license_key, 
			'usin_key' => $module_id, // the name of our product in EDD
			'url'       => home_url()
		);

		// Call the custom API.
		$response = wp_remote_get( 
			add_query_arg( $args, $this->remote_url ), 
			array( 'timeout' => 15, 'sslverify' => false ) 
		);

		if ( is_wp_error( $response ) ){
			return false;
		}

		return json_decode( wp_remote_retrieve_body( $response ) );
	}

	/**
	 * Retrieves the license status including the number of days remaining 
	 * in a human friendly format from an expiry date
	 * @param  string $expires the expiry date
	 * @param  string $status the default status of the license.
	 * This status might be changed to expired in this function if the expiry date has passed.
	 * @return array  containing the status (if it is changed based on the expiry date) and
	 * the user friendly status text
	 */
	public static function get_license_status($expires, $status){
		
		$expires = intval(mysql2date('U', $expires));
		$diff = $expires - time();

		if($diff<=0){
			$status = 'expired';
		}
		
		if($status == 'valid'){
			$days_diff = $diff/(60*60*24);
			
			$status_text = $days_diff <= 1 ? __('License expires today', 'usin') :
				sprintf( __('License active - expires in %d days', 'usin'), floor($days_diff));
		}else{
			$status_text = sprintf(__('License %s', 'usin'), $status);
		}
		
		return array(
			'status' => $status,
			'status_text' => $status_text
		);
	}
	
	public function load_license_status($license_key, $module_id){
		$args = array( 
			'usinr_action'=> 'check_license', 
			'license' 	=> $license_key, 
			'usin_key' => $module_id, // the name of our product in EDD
			'url'       => home_url()
		);

		// Call the custom API.
		$response = wp_remote_get( 
			add_query_arg( $args, $this->remote_url ), 
			array( 'timeout' => 15, 'sslverify' => false ) 
		);
		
		if ( is_wp_error( $response ) ){
			return false;
		}

		return json_decode( wp_remote_retrieve_body( $response ) );
	}
}