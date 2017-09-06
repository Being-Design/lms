<?php
if ( ( class_exists( 'LearnDash_Settings_Section' ) ) && ( !class_exists( 'LearnDash_Settings_Section_Permalinks' ) ) ) {
	class LearnDash_Settings_Section_Permalinks extends LearnDash_Settings_Section {

		function __construct() {
			$this->settings_page_id					=	'permalink';
		
			// This is the 'option_name' key used in the wp_options table
			$this->setting_option_key 				= 	'learndash_settings_permalinks';

			// This is the HTML form field prefix used. 
			$this->setting_field_prefix				= 	'learndash_settings_permalinks';
	
			// Used within the Settings API to uniquely identify this section
			$this->settings_section_key				= 	'learndash_settings_permalinks';
		
			// Section label/header
			$this->settings_section_label			=	__( 'LearnDash Permalinks', 'learndash' );
		
			// Used to show the section description above the fields. Can be empty
			$this->settings_section_description		=	__( 'Controls the URL slugs for the custom posts used by LearnDash.', 'learndash' );

			add_action( 'admin_init', array( $this, 'admin_init' ) );
			
			parent::__construct(); 
			
			$this->save_settings_fields();
		}
		
		function admin_init() {
			do_action( 'learndash_settings_page_init', $this->settings_page_id );
		}
		
		
		function load_settings_values() {
			parent::load_settings_values();
			
			if ( $this->setting_option_values === false ) {
				$this->setting_option_values = array();
				
				// On the initial if we don't have saved values we grab them from the Custom Labels
				$custom_label_settings = get_option( 'learndash_custom_label_settings', array() );

				if ( ( isset( $custom_label_settings['courses'] ) ) && ( !empty( $custom_label_settings['courses'] ) ) ) {
					$this->setting_option_values['courses'] = LearnDash_Custom_Label::label_to_slug( 'courses' );
				}

				if ( ( isset( $custom_label_settings['lessons'] ) ) && ( !empty( $custom_label_settings['lessons'] ) ) ) {
					$this->setting_option_values['lessons'] = LearnDash_Custom_Label::label_to_slug( 'lessons' );
				}

				if ( ( isset( $custom_label_settings['topic'] ) ) && ( !empty( $custom_label_settings['topic'] ) ) ) {
					$this->setting_option_values['topics'] = LearnDash_Custom_Label::label_to_slug( 'topic' );
				}

				if ( ( isset( $custom_label_settings['quizzes'] ) ) && ( !empty( $custom_label_settings['quizzes'] ) ) ) {
					$this->setting_option_values['quizzes'] = LearnDash_Custom_Label::label_to_slug( 'quizzes' );
				}
				
				// As we don't have existing values we want to save here and force the flush rewrite
				update_option( $this->settings_section_key, $this->setting_option_values );
				set_transient( 'sfwd_lms_rewrite_flush', true );
			}
			
			$this->setting_option_values = wp_parse_args(
				$this->setting_option_values, 
				array(
					'courses' 	=>	'courses',
					'lessons' 	=> 	'lessons',
					'topics'	=> 	'topic',
					'quizzes' 	=> 	'quizzes'
				)
			);
		}
		
		
		function load_settings_fields() {
			
			$this->setting_option_fields = array(
				'courses' => array(
					'name'  		=> 	'courses',
					'type'  		=> 	'text',
					'label' 		=> 	__( 'Courses', 'learndash' ),
					'value' 		=> 	$this->setting_option_values['courses'],
					'class'			=>	'regular-text'
				),
				'lessons' => array(
					'name'  		=> 	'lessons',
					'type'  		=> 	'text',
					'label' 		=> 	__( 'Lessons', 'learndash' ),
					'value' 		=> 	$this->setting_option_values['lessons'],
					'class'			=>	'regular-text'
				),
				'topics' => array(
					'name'  		=> 	'topics',
					'type'  		=> 	'text',
					'label' 		=> 	__( 'Topics', 'learndash' ),
					'value' 		=> 	$this->setting_option_values['topics'],
					'class'			=>	'regular-text'
				),
				'quizzes' => array(
					'name'  		=> 	'quizzes',
					'type'  		=> 	'text',
					'label' 		=> 	__( 'Quizzes', 'learndash' ),
					'value' 		=> 	$this->setting_option_values['quizzes'],
					'class'			=>	'regular-text'
				),
				'nonce' => array(
					'name'  		=> 	'nonce',
					'type'  		=> 	'hidden',
					'label' 		=> 	'', 
					'value' 		=> 	wp_create_nonce( 'learndash_permalinks_nonce' ),
					'class'			=>	'hidden'
				),
			);

			$this->setting_option_fields = apply_filters( 'learndash_settings_fields', $this->setting_option_fields, $this->settings_section_key );
			
			parent::load_settings_fields();
		}
		
		function save_settings_fields() {

			if ( isset( $_POST['learndash_settings_permalinks'] ) ) {
				if ( ( isset( $_POST['learndash_settings_permalinks']['nonce'] ) ) 
				  && ( wp_verify_nonce( $_POST['learndash_settings_permalinks']['nonce'], 'learndash_permalinks_nonce' ) ) ) {

					if ( ( isset( $_POST['learndash_settings_permalinks']['courses'] ) ) && ( !empty( $_POST['learndash_settings_permalinks']['courses'] ) ) ) {
						$this->setting_option_values['courses'] = $this->esc_url( $_POST['learndash_settings_permalinks']['courses'] );
						
						// We set a transient. This is checked during the 'shutdown' action where the rewrites will then be flushed. 
						set_transient( 'sfwd_lms_rewrite_flush', true );
					}

					if ( ( isset( $_POST['learndash_settings_permalinks']['lessons'] ) ) && ( !empty( $_POST['learndash_settings_permalinks']['lessons'] ) ) ) {
						$this->setting_option_values['lessons'] = $this->esc_url( $_POST['learndash_settings_permalinks']['lessons'] );
						
						// We set a transient. This is checked during the 'shutdown' action where the rewrites will then be flushed. 
						set_transient( 'sfwd_lms_rewrite_flush', true );
					}

					if ( ( isset( $_POST['learndash_settings_permalinks']['topics'] ) ) && ( !empty( $_POST['learndash_settings_permalinks']['topics'] ) ) ) {
						$this->setting_option_values['topics'] = $this->esc_url( $_POST['learndash_settings_permalinks']['topics'] );
						
						// We set a transient. This is checked during the 'shutdown' action where the rewrites will then be flushed. 
						set_transient( 'sfwd_lms_rewrite_flush', true );
					}

					if ( ( isset( $_POST['learndash_settings_permalinks']['quizzes'] ) ) && ( !empty( $_POST['learndash_settings_permalinks']['quizzes'] ) ) ) {
						$this->setting_option_values['quizzes'] = $this->esc_url( $_POST['learndash_settings_permalinks']['quizzes'] );
						
						// We set a transient. This is checked during the 'shutdown' action where the rewrites will then be flushed. 
						set_transient( 'sfwd_lms_rewrite_flush', true );
					}

					update_option( $this->settings_section_key, $this->setting_option_values );
				}
			}
		}
		
		function esc_url( $value = '' ) {
			if ( !empty( $value ) ) {
				$value = esc_url_raw( trim( $value ) );
				$value = str_replace( 'http://', '', $value );
				return untrailingslashit( $value );
			}
		}
	}
}
add_action( 'learndash_settings_sections_init', function() {
	LearnDash_Settings_Section_Permalinks::add_section_instance();
} );
