<?php
if ( ( class_exists( 'LearnDash_Shortcodes_Section' ) ) && ( !class_exists( 'LearnDash_Shortcodes_Section_ld_course_list' ) ) ) {
	class LearnDash_Shortcodes_Section_ld_course_list extends LearnDash_Shortcodes_Section {

		function __construct( $fields_args = array() ) {
			$this->fields_args = $fields_args;

			$this->shortcodes_section_key 			= 	'ld_course_list';
			$this->shortcodes_section_title 		= 	sprintf( _x( '%s List', 'placeholder: Course', 'learndash' ), LearnDash_Custom_Label::get_label( 'course' ) );
			$this->shortcodes_section_type			=	1;
			$this->shortcodes_section_description	=	sprintf( _x( "This shortcode shows list of %s. You can use this shortcode on any page if you don't want to use the default <code>/%s/</code> page.", 'placeholders: courses, courses (URL slug)', 'learndash' ), LearnDash_Custom_Label::label_to_lower( 'courses' ), LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Section_Permalinks', 'courses' ) );
			
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

				'mycourses' => array(
					'id'			=>	$this->shortcodes_section_key . '_mycourses',
					'name'  		=> 	'mycourses', 
					'type'  		=> 	'checkbox',
					'label' 		=> 	sprintf( _x('My %s', 'laceholder: Courses', 'learndash'), LearnDash_Custom_Label::get_label( 'courses' ) ),
					'help_text'		=>	sprintf( _x( 'show current user\'s %s.', 'placeholders: courses', 'learndash' ), LearnDash_Custom_Label::label_to_lower( 'courses' ) ),
					'value' 		=> 	'',
					'options'		=>	array(
											'true'	=>	__('Yes', 'learndash'),
										)
				),

				'col' => array(
					'id'			=>	$this->shortcodes_section_key . '_col',
					'name'  		=> 	'col', 
					'type'  		=> 	'number',
					'label' 		=> 	__('Columns','learndash'),
					'help_text'		=>	sprintf( _x( 'number of columns to show when using %s grid addon', 'placeholders: course', 'learndash' ), LearnDash_Custom_Label::label_to_lower( 'course' ) ),
					'value' 		=> 	'',
					'class'			=>	'small-text'
				),
			);


			if ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Courses_Taxonomies', 'ld_course_category' ) == 'yes') {
				$this->shortcodes_option_fields['course_cat'] = array(
					'id'			=>	$this->shortcodes_section_key . 'course_cat',
					'name'  		=> 	'course_cat', 
					'type'  		=> 	'number',
					'label' 		=> 	sprintf( _x('%s Category ID', 'placeholder: Course', 'learndash'), LearnDash_Custom_Label::get_label( 'course' ) ),
					'help_text'		=>	sprintf( _x( 'shows %s with mentioned category id.', 'placeholders: courses', 'learndash' ), LearnDash_Custom_Label::label_to_lower( 'courses' ) ),
					'value' 		=> 	'',
					'class'			=>	'small-text'
				);
				
				$this->shortcodes_option_fields['course_category_name'] = array(
					'id'			=>	$this->shortcodes_section_key . 'course_category_name',
					'name'  		=> 	'course_category_name', 
					'type'  		=> 	'text',
					'label' 		=> 	sprintf( _x('%s Category Slug', 'placeholder: Course', 'learndash'), LearnDash_Custom_Label::get_label( 'course' ) ),
					'help_text'		=>	sprintf( _x( 'shows %s with mentioned category slug.', 'placeholders: courses', 'learndash' ), LearnDash_Custom_Label::label_to_lower( 'courses' ) ),
					'value' 		=> 	'',
				);
				
				$this->shortcodes_option_fields['course_categoryselector'] = array(
					'id'			=>	$this->shortcodes_section_key . 'course_categoryselector',
					'name'  		=> 	'course_categoryselector', 
					'type'  		=> 	'checkbox',
					'label' 		=> 	sprintf( _x('%s Category Selector', 'placeholder: Course', 'learndash'), LearnDash_Custom_Label::get_label( 'course' ) ),
					'help_text'		=>	sprintf( _x( 'shows a %s category dropdown.', 'placeholders: course', 'learndash' ), LearnDash_Custom_Label::label_to_lower( 'course' ) ),
					'value' 		=> 	'',
					'options'		=>	array(
											'true'	=>	__('Yes', 'learndash'),
										)
				);
			}
		
			if ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Courses_Taxonomies', 'ld_course_tag' ) == 'yes') {
				$this->shortcodes_option_fields['course_tag'] = array(
					'id'			=>	$this->shortcodes_section_key . 'course_tag',
					'name'  		=> 	'course_tag', 
					'type'  		=> 	'text',
					'label' 		=> 	sprintf( _x( '%s Tag Slug', 'placeholder: Course', 'learndash'), LearnDash_Custom_Label::get_label( 'course' ) ),
					'help_text'		=>	sprintf( _x( 'shows %s with mentioned tag slug.', 'placeholders: courses', 'learndash' ), LearnDash_Custom_Label::label_to_lower( 'courses' ) ),
					'value' 		=> 	'',
				);

				$this->shortcodes_option_fields['course_tag_id'] = array(
					'id'			=>	$this->shortcodes_section_key . 'course_tag_id',
					'name'  		=> 	'course_tag_id', 
					'type'  		=> 	'number',
					'label' 		=> 	sprintf( _x('%s Tag ID', 'placeholder: Course', 'learndash'), LearnDash_Custom_Label::get_label( 'course' ) ),
					'help_text'		=>	sprintf( _x( 'shows %s with mentioned tag id.', 'placeholders: courses', 'learndash' ), LearnDash_Custom_Label::label_to_lower( 'courses' ) ),
					'value' 		=> 	'',
					'class'			=>	'small-text'
				);
			}
		
			if ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Courses_Taxonomies', 'wp_post_category' ) == 'yes') {
				
				$this->shortcodes_option_fields['category_name'] = array(
					'id'			=>	$this->shortcodes_section_key . 'category_name',
					'name'  		=> 	'category_name', 
					'type'  		=> 	'text',
					'label' 		=> 	__('WP Category Slug', 'learndash'),
					'help_text'		=>	sprintf( _x( 'shows %s with mentioned WP category slug.', 'placeholders: courses', 'learndash' ), LearnDash_Custom_Label::label_to_lower( 'courses' ) ),
					'value' 		=> 	'',
				);
				
				$this->shortcodes_option_fields['cat'] = array(
					'id'			=>	$this->shortcodes_section_key . 'cat',
					'name'  		=> 	'cat', 
					'type'  		=> 	'number',
					'label' 		=> 	__('WP Category ID', 'learndash'),
					'help_text'		=>	sprintf( __( 'shows %s with mentioned WP category id.', 'placeholders: courses', 'learndash' ), LearnDash_Custom_Label::label_to_lower( 'courses' ) ),
					'value' 		=> 	'',
					'class'			=>	'small-text'
				);
								
				$this->shortcodes_option_fields['categoryselector'] = array(
					'id'			=>	$this->shortcodes_section_key . 'categoryselector',
					'name'  		=> 	'categoryselector', 
					'type'  		=> 	'checkbox',
					'label' 		=> 	__('WP Category Selector', 'learndash'),
					'help_text'		=>	__( 'shows a WP category dropdown.', 'learndash' ),
					'value' 		=> 	'',
					'options'		=>	array(
											'true'	=>	__('Yes', 'learndash'),
										)
				);
			}
		
			if ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Courses_Taxonomies', 'wp_post_tag' ) == 'yes') {
				$this->shortcodes_option_fields['tag'] = array(
					'id'			=>	$this->shortcodes_section_key . 'tag',
					'name'  		=> 	'tag', 
					'type'  		=> 	'text',
					'label' 		=> 	__( 'WP Tag Slug', 'learndash'),
					'help_text'		=>	sprintf( _x( 'shows %s with mentioned WP tag slug.', 'placeholders: courses', 'learndash' ), LearnDash_Custom_Label::label_to_lower( 'courses' ) ),
					'value' 		=> 	'',
				);

				$this->shortcodes_option_fields['tag_id'] = array(
					'id'			=>	$this->shortcodes_section_key . 'tag_id',
					'name'  		=> 	'tag_id', 
					'type'  		=> 	'number',
					'label' 		=> 	__('WP Tag ID', 'learndash'),
					'help_text'		=>	sprintf( _x( 'shows %s with mentioned WP tag id.', 'placeholders: courses', 'learndash' ), LearnDash_Custom_Label::label_to_lower( 'courses' ) ),
					'value' 		=> 	'',
					'class'			=>	'small-text'
				);
			}
		
			$this->shortcodes_option_fields = apply_filters( 'learndash_settings_fields', $this->shortcodes_option_fields, $this->shortcodes_section_key );
			
			parent::init_shortcodes_section_fields();
		}
	}
}
