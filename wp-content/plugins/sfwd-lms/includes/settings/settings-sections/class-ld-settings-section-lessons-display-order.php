<?php
if ( ( class_exists( 'LearnDash_Settings_Section' ) ) && ( !class_exists( 'LearnDash_Settings_Section_Lessons_Display_Order' ) ) ) {
	class LearnDash_Settings_Section_Lessons_Display_Order extends LearnDash_Settings_Section {

		function __construct() {
			$this->settings_screen_id				=	'sfwd-lessons_page_lessons-options';
			
			// The page ID (different than the screen ID)
			$this->settings_page_id					=	'lessons-options';
		
			// This is the 'option_name' key used in the wp_options table
			$this->setting_option_key 				= 	'learndash_settings_lessons_display_order';

			// This is the HTML form field prefix used. 
			$this->setting_field_prefix				= 	'learndash_settings_lessons_display_order';
	
			// Used within the Settings API to uniquely identify this section
			$this->settings_section_key				= 	'display_order';
		
			// Section label/header
			$this->settings_section_label			=	sprintf( _x( '%s Display Settings', 'placeholder: Lesson', 'learndash' ), LearnDash_Custom_Label::get_label( 'lesson') );
		
			parent::__construct(); 
		}
		
		function load_settings_values() {
			parent::load_settings_values();
			
			// If all the settings are empty then this is probably the first run. So we import the 
			// settings from the previous version. 
			if ( $this->setting_option_values === false ) {
				//$lessons_options = learndash_get_option( 'sfwd-lessons' );
				//if ( ( !empty( $lessons_options ) ) && (is_array( $lessons_options ) ) ) {
				//	$this->setting_option_values = $lessons_options;
				//}
				
				$lessons_options = array();
				$options = get_option( 'sfwd_cpt_options' );
				if ( ( empty( $setting ) )  && ( !empty( $options['modules'][ 'sfwd-lessons_options'] ) ) ) {
					foreach ( $options['modules'][ 'sfwd-lessons_options'] as $key => $val ) {
						$lessons_options[str_replace( 'sfwd-lessons_', '', $key )] = $val;
					}
					$this->setting_option_values = $lessons_options;
				}
			}
			
			if ( !isset( $this->setting_option_values['orderby'] ) )
				$this->setting_option_values['orderby'] = 'date';
			
			if ( !isset( $this->setting_option_values['order'] ) )
				$this->setting_option_values['order'] = 'DESC';
				
			if ( !isset( $this->setting_option_values['posts_per_page'] ) )
				$this->setting_option_values['posts_per_page'] = 25;
			else
				$this->setting_option_values['posts_per_page'] = intval( $this->setting_option_values['posts_per_page'] );

			if ( empty( $this->setting_option_values['posts_per_page'] ) )
				$this->setting_option_values['posts_per_page'] = 25;
			
		}
		
		
		function load_settings_fields() {

			$this->setting_option_fields = array(
				'orderby' => array(
					'name'  		=> 	'orderby', 
					'type'  		=> 	'select',
					'label' 		=> 	__( 'Sort By', 'learndash' ),
					'help_text'  	=> 	__( 'Choose the sort order.', 'learndash' ),
					'value' 		=> 	$this->setting_option_values['orderby'],
					'options'		=>	array(
											'title'			=> 	__( 'Title', 'learndash' ),
											'date'			=> 	__( 'Date', 'learndash' ),
											'menu_order' 	=> __( 'Menu Order', 'learndash' ),
										)
				),
				'order' => array(
					'name'  		=> 	'order', 
					'type'  		=> 	'select',
					'label' 		=> 	__( 'Sort Direction', 'learndash' ),
					'help_text'  	=> 	__( 'Choose the sort direction.', 'learndash' ),
					'value' 		=> 	$this->setting_option_values['order'],
					'options'		=>	array(
											'ASC'	=> __( 'Ascending', 'learndash' ),
											'DESC'	=> __( 'Descending', 'learndash' ),
										)
				),
				'posts_per_page' => array(
					'name'  		=> 	'posts_per_page', 
					'type'  		=> 	'number',
					'label' 		=> 	__( 'Posts Per Page', 'learndash' ),
					'help_text'  	=> 	__( 'Enter the number of posts to display per page.', 'learndash' ),
					'value' 		=> 	$this->setting_option_values['posts_per_page'],
					'class'			=>	'small-text',
					'attrs'			=>	array(
											'step'	=>	1,
											'min'	=>	0
					)
				),
			);
		
			$this->setting_option_fields = apply_filters( 'learndash_settings_fields', $this->setting_option_fields, $this->settings_section_key );
			
			parent::load_settings_fields();
		}
	}
}
add_action( 'learndash_settings_sections_init', function() {
	LearnDash_Settings_Section_Lessons_Display_Order::add_section_instance();
} );
