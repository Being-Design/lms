<?php 
if( !class_exists('BBoss_Updates_Helper') ) {
    class BBoss_Updates_Helper {
    
    	public 
                $api_url = 'http://update.buddyboss.com/',
                $product_key,
                $plugin_id,
                $plugin_path,
                $plugin_slug,
                $license_key;
        
        //these should be false, unless api site requires http authentication, as in case of our dev sites.
        //@todo set these to false( or update values if required ) on live site.
        protected       
                $_is_http_auth_req  = false,
                $_http_username     = '',
                $_http_password     = '';
        
        protected $_site_domain = '';
    
    	function __construct( $product_key, $plugin_path, $plugin_id, $product_type='plugin' ) {
    		$this->product_key = $product_key;
    		$this->plugin_path = $plugin_path;
            $this->plugin_id = $plugin_id;
            
            $this->api_url = trailingslashit( $this->api_url );
            
            $this->api_url .= $product_type;
            
            $this->_site_domain = $this->get_domain();
            
            if( 'plugin' == $product_type ){
                if(strstr($plugin_path, '/')) list ($t1, $t2) = explode('/', $plugin_path); 
                else $t2 = $plugin_path;
                $this->plugin_slug = str_replace('.php', '', $t2);

                add_filter( 'pre_set_site_transient_update_plugins', array(&$this, 'check_for_update') );
                add_filter( 'plugins_api', array(&$this, 'plugin_api_call'), 10, 3 );
            }
            
            if( 'theme' == $product_type ){
                $this->plugin_slug = $this->plugin_path;
                
                add_filter( 'pre_set_site_transient_update_themes', array( &$this, 'check_for_update_theme' ) );
            }
    		// This is for testing only!
    		//set_site_transient( 'update_plugins', null );
    
    		// Show which variables are being requested when query plugin API
    		//add_filter( 'plugins_api_result', array(&$this, 'debug_result'), 10, 3 );
    	}
    
        function check_for_update_theme( $transient ) {
            if (empty($transient->checked)) { return $transient; }
            
            $request_args = array(
    		    'id' => $this->plugin_id,
    		    'slug' => $this->plugin_slug,
    			'version' => $transient->checked[$this->plugin_path],
    		);
            
            if( 'yes' != bblicenses_switch__updates_without_license() ){
                //check if license is active
                $license_obj = BuddyBoss_Updater_Admin::instance();
                $active_license_key = $license_obj->product_valid_license_key( $this->product_key, true );

                $this->license_key = $active_license_key;
                $request_args['license_key'] = $active_license_key['key'];
                $request_args['activation_email'] = $active_license_key['email'];
                $request_args['instance'] = $this->_site_domain;
            }
            
            $request_string = $this->prepare_request( 'theme_update', $request_args );
            
            $raw_response = wp_remote_post( $this->api_url, $request_string );
            
            
            $response = null;
            if( !is_wp_error($raw_response) && ($raw_response['response']['code'] == 200) ){
                $response = unserialize($raw_response['body']);
            }
            
            //Feed the candy !
            if( is_array($response) && !empty($response) ) {
                //add license keys info into download url
                $args = array( 'domain' => $this->_site_domain );
                
                if( $this->license_key ){
                    $args['license_key'] = $this->license_key['key'];
                    $args['activation_email'] = $this->license_key['email'];
                    $args['instance'] = $this->_site_domain;
                }
                
                $response['package'] = add_query_arg( $args, $response['package'] );
                
                // Feed the update data into WP updater
    			$transient->response[$this->plugin_path] = $response;
            }
            
            return $transient;
        }
        
    	function check_for_update( $transient ) {
    		if(empty($transient->checked)) return $transient;
            
            $request_args = array(
    		    'id' => $this->plugin_id,
    		    'slug' => $this->plugin_slug,
    			'version' => $transient->checked[$this->plugin_path],
    		);
            if( 'yes' != bblicenses_switch__updates_without_license() ){
                //check if license is active
                $license_obj = BuddyBoss_Updater_Admin::instance();
                $active_license_key = $license_obj->product_valid_license_key( $this->product_key, true );

                $this->license_key = $active_license_key;
                $request_args['license_key'] = $active_license_key['key'];
                $request_args['activation_email'] = $active_license_key['email'];
                $request_args['instance'] = $this->_site_domain;
            }
            
    		$request_string = $this->prepare_request( 'update_check', $request_args );
    		$raw_response = wp_remote_post( $this->api_url, $request_string );
            
    		$response = null;
    		if( !is_wp_error($raw_response) && ($raw_response['response']['code'] == 200) )
    			$response = unserialize($raw_response['body']);
    		
    		if( is_object($response) && !empty($response) ) {
                //add license keys info into download url
                $args = array( 'domain' => $this->_site_domain );
                
                if( $this->license_key ){
                    $args['license_key'] = $this->license_key['key'];
                    $args['activation_email'] = $this->license_key['email'];
                    $args['instance'] = $this->_site_domain;
                }
                
                $response->package = add_query_arg( $args, $response->package );
                $response->plugin = $this->plugin_path;
                // Feed the update data into WP updater
    			$transient->response[$this->plugin_path] = $response;
                //return $transient;
            }

            // Check to make sure there is not a similarly named plugin in the wordpress.org repository
            if ( isset( $transient->response[$this->plugin_path] ) ) {
                if ( strpos( $transient->response[$this->plugin_path]->package, 'wordpress.org' ) !== false  ) {
                    unset($transient->response[$this->plugin_path]);
                }
            }

    		return $transient;
    	}
    
    	function plugin_api_call( $def, $action, $args ) {
    		if( !isset($args->slug) || $args->slug != $this->plugin_slug ) return $def;
    		
    		$plugin_info = get_site_transient('update_plugins');
    		$request_args = array(
    		    'id' => $this->plugin_id,
    		    'slug' => $this->plugin_slug,
    			'version' => (isset($plugin_info->checked)) ? $plugin_info->checked[$this->plugin_path] : 0 // Current version
    		);
		    if( $this->license_key ) {
                $request_args['license_key'] = $active_license_key['key'];
                $request_args['activation_email'] = $active_license_key['email'];
                $request_args['instance'] = $this->_site_domain;
            }
    		
    		$request_string = $this->prepare_request( $action, $request_args );
            $raw_response = wp_remote_post( $this->api_url, $request_string );
            
    		if( is_wp_error( $raw_response ) ){
    			$res = new WP_Error('plugins_api_failed', __('An Unexpected HTTP Error occurred during the API request.</p> <p><a href="?" onclick="document.location.reload(); return false;">Try again</a>'), $raw_response->get_error_message());
    		} else {
    			$res = unserialize($raw_response['body']);
    			if ($res === false)
    				$res = new WP_Error('plugins_api_failed', __('An unknown error occurred'), $raw_response['body']);
    		}
    		
    		return $res;
    	}
    
    	function prepare_request( $action, $args ) {
    		global $wp_version;
    		
    		$retval = array(
    			'body' => array(
    				'action' => $action, 
    				'request' => serialize($args),
    				'api-key' => md5(home_url()),
                    'domain'    => $this->_site_domain,
    			),
    			'user-agent' => 'WordPress/'. $wp_version .'; '. home_url()
    		);
            
            if( $this->_is_http_auth_req ){
                $headers = array( 'Authorization' => 'Basic ' . base64_encode( "{$this->_http_username}:{$this->_http_password}" ) );
                $retval['headers'] = $headers;
            }
            
            //timeout for localhost
            $retval['timeout'] = 50;
            
            return $retval;
    	}
    	
    	function debug_result( $res, $action, $args ) {
    		echo '<pre>'.print_r($res,true).'</pre>';
    		return $res;
    	}
        
        public function get_domain(){
            $home_url = "";

            //1. multisite - only the root domain
            if( is_multisite() ){
                $home_url = network_home_url();
            } else {
                $home_url = home_url();
            }

            $home_url = untrailingslashit( $home_url );
            $home_url = str_replace( array( 'http://', 'https://', 'www.' ), array('','',''), $home_url );
            return $home_url;
        }

    }
}
