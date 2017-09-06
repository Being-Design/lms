<?php
if ( ( class_exists( 'LearnDash_Shortcodes_Section' ) ) && ( !class_exists( 'LearnDash_Shortcodes_Section_ld_profile' ) ) ) {
	class LearnDash_Shortcodes_Section_ld_profile extends LearnDash_Shortcodes_Section {

		function __construct( $fields_args = array() ) {
			$this->fields_args = $fields_args;

			$this->shortcodes_section_key 			= 	'ld_profile';
			$this->shortcodes_section_title 		= 	__( 'Profile', 'learndash' );
			$this->shortcodes_section_type			=	1;
			$this->shortcodes_section_description	=	sprintf( _x( "Displays user's enrolled %s, %s progress, %s scores, and achieved certificates.", 'placeholder: courses, course, quiz', 'learndash' ), LearnDash_Custom_Label::label_to_lower( 'courses' ), LearnDash_Custom_Label::label_to_lower( 'course' ), LearnDash_Custom_Label::label_to_lower( 'quiz' ) );
			
			parent::__construct(); 
		}
		
		function init_shortcodes_section_fields() {
			$this->shortcodes_option_fields = array(
				'orderby' => array(
					'id'			=>	$this->shortcodes_section_key . '_orderby',
					'name'  		=> 	'orderby', 
					'type'  		=> 	'select',
					'label' 		=> 	__( 'Order by', 'learndash' ),
					'help_text'		=>	__( 'See <a target="_blank" href="https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters">the full list of available orderby options here.</a>', 'learndash' ),
					'value' 		=> 	'ID',
					'options'		=>	array(
											''			=>	__('ID - Order by post id. (default)', 'learndash'),
											'title'			=>	__('Title - Order by post title', 'learndash'),
											'date'			=>	__('Date - Order by post date', 'learndash'),
											'menu_order'	=>	__('Menu - Order by Page Order Value', 'learndash')
										)
				),
				'order' => array(
					'id'			=>	$this->shortcodes_section_key . '_order',
					'name'  		=> 	'order', 
					'type'  		=> 	'select',
					'label' 		=> 	__( 'Order', 'learndash' ),
					'help_text'		=>	__( 'Order', 'learndash' ),
					'value' 		=> 	'ID',
					'options'		=>	array(
											''			=>	__('DESC - highest to lowest values (default)', 'learndash'),
											'ASC'			=>	__('ASC - lowest to highest values', 'learndash'),
										)
				),
				
				'course_points_user' => array(
					'id'			=>	$this->shortcodes_section_key . 'course_points_user',
					'name'  		=> 	'course_points_user', 
					'type'  		=> 	'select',
					'label' 		=> 	sprintf( _x('Show Earned %s Points', 'placeholder: Course', 'learndash'), LearnDash_Custom_Label::get_label( 'course' ) ),
					'help_text'		=>	sprintf( _x('Show Earned %s Points', 'placeholder: Course', 'learndash'), LearnDash_Custom_Label::get_label( 'course' ) ),
					'value' 		=> 	'',
					'options'		=>	array(
											'yes'	=>	__('Yes', 'learndash'),
											'no'	=>	__('No', 'learndash'),
										)
				),
				
			);
		
			$this->shortcodes_option_fields = apply_filters( 'learndash_settings_fields', $this->shortcodes_option_fields, $this->shortcodes_section_key );
			
			parent::init_shortcodes_section_fields();
		}
	}
}
