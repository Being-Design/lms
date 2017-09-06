<?php
if ( ( class_exists( 'LearnDash_Settings_Section' ) ) && ( !class_exists( 'LearnDash_Settings_Section_PayPal' ) ) ) {
	class LearnDash_Settings_Section_PayPal extends LearnDash_Settings_Section {

		function __construct() {
			$this->settings_page_id					=	'learndash_lms_settings_paypal';
		
			// This is the 'option_name' key used in the wp_options table
			$this->setting_option_key 				= 	'learndash_settings_paypal';

			// This is the HTML form field prefix used. 
			$this->setting_field_prefix				= 	'learndash_settings_paypal';
	
			// Used within the Settings API to uniquely identify this section
			$this->settings_section_key				= 	'settings_paypal';
		
			// Section label/header
			$this->settings_section_label			=	__( 'PayPal Settings', 'learndash' );
		
			$this->reset_confirm_message			=	__('Are you sure want to reset the PayPal values?', 'learndash' );
			
			parent::__construct(); 
		}
		
		
		function load_settings_values() {
			parent::load_settings_values();
			
			if ( $this->setting_option_values === false ) {
				$sfwd_cpt_options = get_option('sfwd_cpt_options');
				
				if ( ( isset( $sfwd_cpt_options['modules']['sfwd-courses_options'] ) ) && ( !empty( $sfwd_cpt_options['modules']['sfwd-courses_options'] ) ) ) {
					foreach( $sfwd_cpt_options['modules']['sfwd-courses_options'] as $key => $val ) {
						$key = str_replace( 'sfwd-courses_', '', $key );
						if ( $key == 'paypal_sandbox' ) {
							if ( $val == 'on' ) $val = 'yes';
							else $val = 'no';
						}
						$this->setting_option_values[$key] = $val;
					}
				}
			}
			
			if ( ( isset( $_GET['action'] ) ) && ( $_GET['action'] == 'ld_reset_settings' ) && ( isset( $_GET['page'] ) ) && ( $_GET['page'] == $this->settings_page_id ) ) {
				if ( ( isset( $_GET['ld_wpnonce'] ) ) && ( !empty( $_GET['ld_wpnonce'] ) ) ) {
					if ( wp_verify_nonce( $_GET['ld_wpnonce'], get_current_user_id() .'-'. $this->setting_option_key ) ) {
						if ( !empty( $this->setting_option_values ) ) {
							foreach( $this->setting_option_values as $key => $val ) {
								$this->setting_option_values[$key] = '';
							}
							$this->save_settings_values();
						}
						
						$reload_url = remove_query_arg( array( 'action', 'ld_wpnonce' ) );
						//$reload_url = add_query_arg('ld-settings-cleared', '1', $reload_url );
						wp_redirect($reload_url);
						die();
					} 
				}
			}
		}
		
		
		function load_settings_fields() {
			global $wp_rewrite;
			
			if ( ( isset( $wp_rewrite ) ) && ( $wp_rewrite->using_permalinks() ) ) {
				$default_paypal_notifyurl = trailingslashit( get_home_url() ) . 'sfwd-lms/paypal';
			} else {
				$default_paypal_notifyurl = add_query_arg( 'sfwd-lms', 'paypal', get_home_url() );
			}
			
			$this->setting_option_fields = array(
				'paypal_email' => array(
					'name'  		=> 	'paypal_email',
					'type'  		=> 	'text',
					'label' 		=> 	__( 'PayPal Email', 'learndash' ),
					'help_text'  	=> 	__( 'Enter your PayPal email here.', 'learndash' ),
					'value' 		=> 	( ( isset( $this->setting_option_values['paypal_email'] ) ) && ( !empty( $this->setting_option_values['paypal_email'] ) ) ) ? $this->setting_option_values['paypal_email'] : '',
					'class'			=>	'regular-text',
					'validate_callback'	=>	array( $this, 'validate_section_paypal_email' )
				),
				'paypal_currency' => array(
					'name'  		=> 	'paypal_currency',
					'type'  		=> 	'text',
					'label' 		=> __( 'PayPal Currency', 'learndash' ),
					'help_text'  	=> __( 'Enter the currency code for transactions. See PayPal <a href="https://developer.paypal.com/docs/classic/api/currency_codes/" target="_blank">Currency Codes</a> Documentation', 'learndash' ),
					'value' 		=> ( ( isset( $this->setting_option_values['paypal_currency'] ) ) && ( !empty( $this->setting_option_values['paypal_currency'] ) ) ) ? $this->setting_option_values['paypal_currency'] : 'USD',
					'class'			=>	'regular-text',
					'validate_callback'	=>	array( $this, 'validate_section_paypal_currency' )
					
					
				),
				'paypal_country' => array(
					'name'  		=> 	'paypal_country',
					'type'  		=> 	'text',
					'label' 		=> __( 'PayPal Country', 'learndash' ),
					'help_text'  	=> __( 'Enter your country code here. See PayPal <a href="https://developer.paypal.com/docs/classic/api/country_codes/" target="_blank">Country Codes</a> Documentation', 'learndash' ),
					'value' 		=> ( ( isset( $this->setting_option_values['paypal_country'] ) ) && ( !empty( $this->setting_option_values['paypal_country'] ) ) ) ? $this->setting_option_values['paypal_country'] : 'US',
					'class'			=>	'regular-text',
					'validate_callback'	=>	array( $this, 'validate_section_paypal_country' )					
				),
				'paypal_cancelurl' => array(
					'name'  		=> 	'paypal_cancelurl',
					'type'  		=> 	'text',
					'label' 		=> __( 'PayPal Cancel URL', 'learndash' ),
					'help_text'  	=> __( 'Enter the URL used for purchase cancellations.', 'learndash' ),
					'value' 		=> ( ( isset( $this->setting_option_values['paypal_cancelurl'] ) )  && ( !empty( $this->setting_option_values['paypal_cancelurl'] ) ) ) ? $this->setting_option_values['paypal_cancelurl'] : get_home_url(),
					'class'			=>	'regular-text'
				),
				'paypal_returnurl' => array(
					'name'  		=> 	'paypal_returnurl',
					'type'  		=> 	'text',
					'label' 		=> __( 'PayPal Return ', 'learndash' ),
					'help_text'  	=> __( 'Enter the URL used for completed purchases (typically a thank you page).', 'learndash' ),
					'value' 		=> ( ( isset( $this->setting_option_values['paypal_returnurl'] ) ) && ( !empty( $this->setting_option_values['paypal_returnurl'] ) ) ) ? $this->setting_option_values['paypal_returnurl'] : get_home_url(),
					'class'			=>	'regular-text'
				),
				'paypal_notifyurl' => array(
					'name'  		=> 	'paypal_notifyurl',
					'type'  		=> 	'text',
					'label' 		=> 	__( 'PayPal Notify URL', 'learndash' ),
					'help_text'  	=> 	__( 'Enter the URL used for IPN notifications.', 'learndash' ),
					'value' 		=> 	( ( isset( $this->setting_option_values['paypal_notifyurl'] ) ) && ( !empty( $this->setting_option_values['paypal_notifyurl'] ) ) ) ? $this->setting_option_values['paypal_notifyurl'] : $default_paypal_notifyurl,
					'class'			=>	'regular-text'
				),
				'paypal_sandbox' => array(
					'name'  		=> 	'paypal_sandbox',
					'type'  		=> 	'checkbox',
					'label' 		=> 	__( 'Use PayPal Sandbox', 'learndash' ),
					'help_text'  	=> 	__( 'Check to enable the PayPal sandbox.', 'learndash' ),
					'value' 		=> 	isset( $this->setting_option_values['paypal_sandbox'] ) ? $this->setting_option_values['paypal_sandbox'] : 'no',
					'options'		=>	array(
											'yes'	=>	__('Yes', 'learndash'),
										)
				),
			);

			$this->setting_option_fields = apply_filters( 'learndash_settings_fields', $this->setting_option_fields, $this->settings_section_key );
			
			parent::load_settings_fields();
		}

		public static function validate_section_paypal_email( $val, $key, $args = array() ) {
			$val = trim ( $val );
			if ( ( !empty( $val ) ) && ( !is_email( $val ) ) ) {
				
				add_settings_error( $args['setting_option_key'], $key, __( 'PayPal Email must be a valid email.', 'learndash' ), 'error' );
			}
			
			return $val;
		}
		
		public static function validate_section_paypal_country( $val, $key, $args = array() ) {
			if ( ( isset( $args['post_fields']['paypal_email'] ) ) && ( !empty( $args['post_fields']['paypal_email'] ) ) ) {
				$val = sanitize_text_field( $val );
				if ( empty( $val ) ) {
					add_settings_error( $args['setting_option_key'], $key, __( 'PayPal Country Code cannot be empty.', 'learndash' ), 'error' );
				} else if ( strlen( $val ) > 2 ) {
					add_settings_error( $args['setting_option_key'], $key, __( 'PayPal Country Code should not be longer than 2 letters.', 'learndash' ), 'error' );
				}
			}
			
			return $val;
		}
		
		public static function validate_section_paypal_currency( $val, $key, $args = array() ) {
			if ( ( isset( $args['post_fields']['paypal_email'] ) ) && ( !empty( $args['post_fields']['paypal_email'] ) ) ) {
				$val = sanitize_text_field( $val );
				if ( empty( $val ) ) {
					add_settings_error( $args['setting_option_key'], $key, __( 'PayPal Currency Code cannot be empty.', 'learndash' ), 'error' );
				} else if ( strlen( $val ) > 3 ) {
					add_settings_error( $args['setting_option_key'], $key, __( 'PayPal Currency Code should not be longer than 3 letters.', 'learndash' ), 'error' );
				}
			}
			
			return $val;
		}
	}
}
add_action( 'learndash_settings_sections_init', function() {
	LearnDash_Settings_Section_PayPal::add_section_instance();
} );
