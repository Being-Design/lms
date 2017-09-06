<?php
if ( ( class_exists( 'LearnDash_Shortcodes_Section' ) ) && ( !class_exists( 'LearnDash_Shortcodes_Section_learndash_course_progress' ) ) ) {
	class LearnDash_Shortcodes_Section_learndash_course_progress extends LearnDash_Shortcodes_Section {

		function __construct( $fields_args = array() ) {
			$this->fields_args = $fields_args;
			
			$this->shortcodes_section_key 			= 	'learndash_course_progress';
			$this->shortcodes_section_title 		= 	sprintf( _x( '%s Progress', 'placeholder: Course', 'learndash' ), LearnDash_Custom_Label::get_label( 'course' ) );
			$this->shortcodes_section_type			=	1;
			$this->shortcodes_section_description	=	sprintf( _x( 'This shortcode displays users progress bar for the %s in any %s/%s/%s pages.', 'placeholders: course, course, lesson, quiz', 'learndash' ), LearnDash_Custom_Label::label_to_lower( 'course' ), LearnDash_Custom_Label::label_to_lower( 'course' ), LearnDash_Custom_Label::label_to_lower( 'lesson' ), LearnDash_Custom_Label::label_to_lower( 'quiz' ) );
						
			parent::__construct(); 
		}
		
		function init_shortcodes_section_fields() {
			$this->shortcodes_option_fields = array(
				'course_id' => array(
					'id'			=>	$this->shortcodes_section_key . '_course_id',
					'name'  		=> 	'course_id', 
					'type'  		=> 	'number',
					'label' 		=> 	sprintf( _x( '%s ID', 'placeholder: Course', 'learndash' ), LearnDash_Custom_Label::get_label( 'course' ) ),
					'help_text'		=>	sprintf( _x( 'Enter single %s ID. Leave blank for all %s.', 'placeholders: Course, Courses', 'learndash' ), LearnDash_Custom_Label::get_label( 'course' ), LearnDash_Custom_Label::get_label( 'courses' ) ),
					'value' 		=> 	'',
					'class'			=>	'small-text'
				),
				'user_id' => array(
					'id'			=>	$this->shortcodes_section_key . '_user_id',
					'name'  		=> 	'user_id', 
					'type'  		=> 	'number',
					'label' 		=> 	__( 'User ID', 'learndash' ),
					'help_text'		=>	__('Enter specific User ID. Leave blank for current User.', 'learndash' ),
					'value' 		=> 	'',
					'class'			=>	'small-text'
				),
			);
		
			if ( ( !isset( $this->fields_args['post_type'] ) ) || ( ( $this->fields_args['post_type'] != 'sfwd-courses' ) && ( $this->fields_args['post_type'] != 'sfwd-lessons' ) && ( $this->fields_args['post_type'] != 'sfwd-topic' ) ) ) {
				$this->shortcodes_option_fields['course_id']['required'] = 'required';	
				$this->shortcodes_option_fields['course_id']['help_text'] =	sprintf( _x( 'Enter single %s ID', 'placeholders: Course', 'learndash' ), LearnDash_Custom_Label::get_label( 'course' ) );
			} 
		
			$this->shortcodes_option_fields = apply_filters( 'learndash_settings_fields', $this->shortcodes_option_fields, $this->shortcodes_section_key );
			
			parent::init_shortcodes_section_fields();
		}
	}
}
