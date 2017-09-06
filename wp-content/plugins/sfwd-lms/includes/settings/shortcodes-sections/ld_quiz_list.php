<?php
if ( ( class_exists( 'LearnDash_Shortcodes_Section' ) ) && ( !class_exists( 'LearnDash_Shortcodes_Section_ld_quiz_list' ) ) ) {
	class LearnDash_Shortcodes_Section_ld_quiz_list extends LearnDash_Shortcodes_Section {

		function __construct( $fields_args = array() ) {
			$this->fields_args = $fields_args;

			$this->shortcodes_section_key 			= 	'ld_quiz_list';
			$this->shortcodes_section_title 		= 	sprintf( _x( '%s List', 'placeholder: Quiz', 'learndash' ), LearnDash_Custom_Label::get_label( 'quiz' ) );
			$this->shortcodes_section_type			=	1;
			$this->shortcodes_section_description	=	sprintf( _x( "This shortcode shows list of %s. You can use this shortcode on any page if you don't want to use the default <code>/%s/</code> page.", 'placeholders: quizzes, quizzes (URL slug)', 'learndash' ), LearnDash_Custom_Label::label_to_lower( 'quizzes' ), LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Section_Permalinks', 'quizzes' ) );
			
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
											''				=>	__('ID - Order by post id. (default)', 'learndash'),
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
											''				=>	__('DESC - highest to lowest values (default)', 'learndash'),
											'ASC'			=>	__('ASC - lowest to highest values', 'learndash'),
										)
				),
				'num' => array(
					'id'			=>	$this->shortcodes_section_key . '_num',
					'name'  		=> 	'num', 
					'type'  		=> 	'number',
					'label' 		=> 	__('Number','learndash'),
					'help_text'		=>	sprintf( _x( 'limits the number of %s displayed.', 'placeholders: courses', 'learndash' ), LearnDash_Custom_Label::label_to_lower( 'courses' ) ),
					'value' 		=> 	'',
					'class'			=>	'small-text'
				),
			);

			$this->shortcodes_option_fields = apply_filters( 'learndash_settings_fields', $this->shortcodes_option_fields, $this->shortcodes_section_key );
			
			parent::init_shortcodes_section_fields();
		}
	}
}
