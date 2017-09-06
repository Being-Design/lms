<?php
if ( ( class_exists( 'LearnDash_Shortcodes_Section' ) ) && ( !class_exists( 'LearnDash_Shortcodes_Section_ld_video' ) ) ) {
	class LearnDash_Shortcodes_Section_ld_video extends LearnDash_Shortcodes_Section {

		function __construct( $fields_args = array() ) {
			$this->fields_args = $fields_args;

			$this->shortcodes_section_key 			= 	'ld_video';
			$this->shortcodes_section_type			=	1;
			//$this->shortcodes_section_description	=	__( 'Placeholder shortcode to allow control of video player within the content.', 'learndash' );
			$this->shortcodes_section_description			=	sprintf( _x( 'This shortcode is used on %s and %s where Video Progression is enabled. The video player will be added above the content. This shortcode allows positioning the player elsewhere within the content. This shortcode does not take any parameters.', 'placeholders: Lessons, Topics', 'learndash' ), LearnDash_Custom_Label::get_label( 'lessons' ), LearnDash_Custom_Label::get_label( 'topics' ) );
			

			if ( $this->fields_args['post_type'] == 'sfwd-lessons' ) {
				$this->shortcodes_section_title 		= 	sprintf( _x( '%s Video', 'learndash' ), LearnDash_Custom_Label::get_label( 'lesson' ) );
			} else if ( $this->fields_args['post_type'] == 'sfwd-topic' ) {
				$this->shortcodes_section_title 		= 	sprintf( _x( '%s Video', 'learndash' ), LearnDash_Custom_Label::get_label( 'topic' ) );
			}
			
			parent::__construct(); 
		}
		
		function init_shortcodes_section_fields() {
			$this->shortcodes_option_fields = array();
		
			$this->shortcodes_option_fields = apply_filters( 'learndash_settings_fields', $this->shortcodes_option_fields, $this->shortcodes_section_key );
			
			parent::init_shortcodes_section_fields();
		}
	}
}
