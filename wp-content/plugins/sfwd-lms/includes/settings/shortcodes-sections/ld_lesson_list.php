<?php
if ( ( class_exists( 'LearnDash_Shortcodes_Section' ) ) && ( !class_exists( 'LearnDash_Shortcodes_Section_ld_lesson_list' ) ) ) {
	class LearnDash_Shortcodes_Section_ld_lesson_list extends LearnDash_Shortcodes_Section {

		function __construct( $fields_args = array() ) {
			$this->fields_args = $fields_args;

			$this->shortcodes_section_key 			= 	'ld_lesson_list';
			$this->shortcodes_section_title 		= 	sprintf( _x( '%s List', 'placeholder: Lesson', 'learndash' ), LearnDash_Custom_Label::get_label( 'lesson' ) );
			$this->shortcodes_section_type			=	1;
			$this->shortcodes_section_description	=	sprintf( _x( "This shortcode shows list of %s. You can use this shortcode on any page if you don't want to use the default <code>/%s/</code> page.", 'placeholders: lessons, lessons (URL slug)', 'learndash' ), LearnDash_Custom_Label::label_to_lower( 'lessons' ), LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Section_Permalinks', 'lessons' ) );
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
					'help_text'		=>	sprintf( _x( 'limits the number of %s displayed.', 'placeholders: lessons', 'learndash' ), LearnDash_Custom_Label::label_to_lower( 'lessons' ) ),
					'value' 		=> 	'',
					'class'			=>	'small-text'
				),
			);

			if ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Lessons_Taxonomies', 'ld_lesson_category' ) == 'yes') {
				$this->shortcodes_option_fields['lesson_cat'] = array(
					'id'			=>	$this->shortcodes_section_key . '_lesson_cat',
					'name'  		=> 	'lesson_cat', 
					'type'  		=> 	'number',
					'label' 		=> 	sprintf( _x('%s Category ID', 'placeholder: Lesson', 'learndash'), LearnDash_Custom_Label::get_label( 'lesson' ) ),
					'help_text'		=>	sprintf( _x( 'shows %s with mentioned category id.', 'placeholders: lessons', 'learndash' ), LearnDash_Custom_Label::label_to_lower( 'lessons' ) ),
					'value' 		=> 	'',
					'class'			=>	'small-text'
				);
				
				$this->shortcodes_option_fields['lesson_category_name'] = array(
					'id'			=>	$this->shortcodes_section_key . '_lesson_category_name',
					'name'  		=> 	'lesson_category_name', 
					'type'  		=> 	'text',
					'label' 		=> 	sprintf( _x('%s Category Slug', 'placeholder: Lesson', 'learndash'), LearnDash_Custom_Label::get_label( 'lesson' ) ),
					'help_text'		=>	sprintf( _x( 'shows %s with mentioned category slug.', 'placeholders: lessons', 'learndash' ), LearnDash_Custom_Label::label_to_lower( 'lessons' ) ),
					'value' 		=> 	'',
				);
				
				$this->shortcodes_option_fields['lesson_categoryselector'] = array(
					'id'			=>	$this->shortcodes_section_key . '_lesson_categoryselector',
					'name'  		=> 	'lesson_categoryselector', 
					'type'  		=> 	'checkbox',
					'label' 		=> 	sprintf( _x('%s Category Selector', 'placeholder: Lesson', 'learndash'), LearnDash_Custom_Label::get_label( 'lesson' ) ),
					'help_text'		=>	sprintf( _x( 'shows a %s category dropdown.', 'placeholders: lesson', 'learndash' ), LearnDash_Custom_Label::label_to_lower( 'lesson' ) ),
					'value' 		=> 	'',
					'options'		=>	array(
											'true'	=>	__('Yes', 'learndash'),
										)
				);
			}
		
			if ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Lessons_Taxonomies', 'ld_lesson_tag' ) == 'yes') {
				$this->shortcodes_option_fields['lesson_tag_id'] = array(
					'id'			=>	$this->shortcodes_section_key . '_lesson_tag_id',
					'name'  		=> 	'lesson_tag_id', 
					'type'  		=> 	'number',
					'label' 		=> 	sprintf( _x('%s Tag ID', 'placeholder: Lesson', 'learndash'), LearnDash_Custom_Label::get_label( 'Lesson' ) ),
					'help_text'		=>	sprintf( _x( 'shows %s with mentioned tag id.', 'placeholders: lessons', 'learndash' ), LearnDash_Custom_Label::label_to_lower( 'lessons' ) ),
					'value' 		=> 	'',
					'class'			=>	'small-text'
				);

				$this->shortcodes_option_fields['lesson_tag'] = array(
					'id'			=>	$this->shortcodes_section_key . '_lesson_tag',
					'name'  		=> 	'lesson_tag', 
					'type'  		=> 	'text',
					'label' 		=> 	sprintf( _x( '%s Tag Slug', 'placeholder: Lesson', 'learndash'), LearnDash_Custom_Label::get_label( 'lesson' ) ),
					'help_text'		=>	sprintf( _x( 'shows %s with mentioned tag slug.', 'placeholders: lessons', 'learndash' ), LearnDash_Custom_Label::label_to_lower( 'lessons' ) ),
					'value' 		=> 	'',
				);
			}
		
			if ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Lessons_Taxonomies', 'wp_post_category' ) == 'yes') {
				
				$this->shortcodes_option_fields['cat'] = array(
					'id'			=>	$this->shortcodes_section_key . '_cat',
					'name'  		=> 	'cat', 
					'type'  		=> 	'number',
					'label' 		=> 	__('WP Category ID', 'learndash'),
					'help_text'		=>	sprintf( __( 'shows %s with mentioned WP category id.', 'placeholders: lessons', 'learndash' ), LearnDash_Custom_Label::label_to_lower( 'lessons' ) ),
					'value' 		=> 	'',
					'class'			=>	'small-text'
				);
				
				$this->shortcodes_option_fields['category_name'] = array(
					'id'			=>	$this->shortcodes_section_key . '_category_name',
					'name'  		=> 	'category_name', 
					'type'  		=> 	'text',
					'label' 		=> 	__('WP Category Slug', 'learndash'),
					'help_text'		=>	sprintf( _x( 'shows %s with mentioned WP category slug.', 'placeholders: lessons', 'learndash' ), LearnDash_Custom_Label::label_to_lower( 'lessons' ) ),
					'value' 		=> 	'',
				);
				
				$this->shortcodes_option_fields['categoryselector'] = array(
					'id'			=>	$this->shortcodes_section_key . '_categoryselector',
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
		
			if ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Lessonss_Taxonomies', 'wp_post_tag' ) == 'yes') {
				$this->shortcodes_option_fields['tag'] = array(
					'id'			=>	$this->shortcodes_section_key . '_tag',
					'name'  		=> 	'tag', 
					'type'  		=> 	'text',
					'label' 		=> 	__( 'WP Tag Slug', 'learndash'),
					'help_text'		=>	sprintf( _x( 'shows %s with mentioned WP tag slug.', 'placeholders: lessons', 'learndash' ), LearnDash_Custom_Label::label_to_lower( 'lessons' ) ),
					'value' 		=> 	'',
				);

				$this->shortcodes_option_fields['tag_id'] = array(
					'id'			=>	$this->shortcodes_section_key . '_tag_id',
					'name'  		=> 	'tag_id', 
					'type'  		=> 	'number',
					'label' 		=> 	__('WP Tag ID', 'learndash'),
					'help_text'		=>	sprintf( _x( 'shows %s with mentioned WP tag id.', 'placeholders: lessons', 'learndash' ), LearnDash_Custom_Label::label_to_lower( 'lessons' ) ),
					'value' 		=> 	'',
					'class'			=>	'small-text'
				);
			}
		
			$this->shortcodes_option_fields = apply_filters( 'learndash_settings_fields', $this->shortcodes_option_fields, $this->shortcodes_section_key );
			
			parent::init_shortcodes_section_fields();
		}
	}
}
