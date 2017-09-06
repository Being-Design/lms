<?php
if ( ( class_exists( 'LearnDash_Shortcodes_Section' ) ) && ( !class_exists( 'LearnDash_Shortcodes_Section_ld_topic_list' ) ) ) {
	class LearnDash_Shortcodes_Section_ld_topic_list extends LearnDash_Shortcodes_Section {

		function __construct( $fields_args = array() ) {
			$this->fields_args = $fields_args;

			$this->shortcodes_section_key 			= 	'ld_topic_list';
			$this->shortcodes_section_title 		= 	sprintf( _x( '%s List', 'placeholder: Topic', 'learndash' ), LearnDash_Custom_Label::get_label( 'topic' ) );
			$this->shortcodes_section_type			=	1;
			$this->shortcodes_section_description	=	sprintf( _x( "This shortcode shows list of %s. You can use this shortcode on any page if you don't want to use the default <code>/%s/</code> page.", 'placeholders: topics, topics (URL slug)', 'learndash' ), LearnDash_Custom_Label::label_to_lower( 'topics' ), LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Section_Permalinks', 'topics' ) );			
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
					'help_text'		=>	sprintf( _x( 'limits the number of %s displayed.', 'placeholders: topics', 'learndash' ), LearnDash_Custom_Label::label_to_lower( 'topics' ) ),
					'value' 		=> 	'',
					'class'			=>	'small-text'
				),
			);


			if ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Topics_Taxonomies', 'ld_topic_category' ) == 'yes') {
				$this->shortcodes_option_fields['topic_cat'] = array(
					'id'			=>	$this->shortcodes_section_key . '_topic_cat',
					'name'  		=> 	'topic_cat', 
					'type'  		=> 	'number',
					'label' 		=> 	sprintf( _x('%s Category ID', 'placeholder: Topics', 'learndash'), LearnDash_Custom_Label::get_label( 'topic' ) ),
					'help_text'		=>	sprintf( _x( 'shows %s with mentioned category id.', 'placeholders: topics', 'learndash' ), LearnDash_Custom_Label::label_to_lower( 'topics' ) ),
					'value' 		=> 	'',
					'class'			=>	'small-text'
				);
				
				$this->shortcodes_option_fields['topic_category_name'] = array(
					'id'			=>	$this->shortcodes_section_key . '_topic_category_name',
					'name'  		=> 	'topic_category_name', 
					'type'  		=> 	'text',
					'label' 		=> 	sprintf( _x('%s Category Slug', 'placeholder: Topic', 'learndash'), LearnDash_Custom_Label::get_label( 'topic' ) ),
					'help_text'		=>	sprintf( _x( 'shows %s with mentioned category slug.', 'placeholders: topics', 'learndash' ), LearnDash_Custom_Label::label_to_lower( 'topics' ) ),
					'value' 		=> 	'',
				);
				
				$this->shortcodes_option_fields['topic_categoryselector'] = array(
					'id'			=>	$this->shortcodes_section_key . '_topic_categoryselector',
					'name'  		=> 	'topic_categoryselector', 
					'type'  		=> 	'checkbox',
					'label' 		=> 	sprintf( _x('%s Category Selector', 'placeholder: Topic', 'learndash'), LearnDash_Custom_Label::get_label( 'topic' ) ),
					'help_text'		=>	sprintf( _x( 'shows a %s category dropdown.', 'placeholders: topic', 'learndash' ), LearnDash_Custom_Label::label_to_lower( 'topic' ) ),
					'value' 		=> 	'',
					'options'		=>	array(
											'true'	=>	__('Yes', 'learndash'),
										)
				);
			}
		
			if ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Topics_Taxonomies', 'ld_topic_tag' ) == 'yes') {
				$this->shortcodes_option_fields['topic_tag_id'] = array(
					'id'			=>	$this->shortcodes_section_key . '_topic_tag_id',
					'name'  		=> 	'topic_tag_id', 
					'type'  		=> 	'number',
					'label' 		=> 	sprintf( _x('%s Tag ID', 'placeholder: Topic', 'learndash'), LearnDash_Custom_Label::get_label( 'topic' ) ),
					'help_text'		=>	sprintf( _x( 'shows %s with mentioned tag id.', 'placeholders: topics', 'learndash' ), LearnDash_Custom_Label::label_to_lower( 'topics' ) ),
					'value' 		=> 	'',
					'class'			=>	'small-text'
				);

				$this->shortcodes_option_fields['topic_tag'] = array(
					'id'			=>	$this->shortcodes_section_key . '_topic_tag',
					'name'  		=> 	'topic_tag', 
					'type'  		=> 	'text',
					'label' 		=> 	sprintf( _x( '%s Tag Slug', 'placeholder: Topic', 'learndash'), LearnDash_Custom_Label::get_label( 'topic' ) ),
					'help_text'		=>	sprintf( _x( 'shows %s with mentioned tag slug.', 'placeholders: topics', 'learndash' ), LearnDash_Custom_Label::label_to_lower( 'topics' ) ),
					'value' 		=> 	'',
				);

			}
		
			if ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Topics_Taxonomies', 'wp_post_category' ) == 'yes') {
				
				$this->shortcodes_option_fields['cat'] = array(
					'id'			=>	$this->shortcodes_section_key . '_cat',
					'name'  		=> 	'cat', 
					'type'  		=> 	'number',
					'label' 		=> 	__('WP Category ID', 'learndash'),
					'help_text'		=>	sprintf( __( 'shows %s with mentioned WP category id.', 'placeholders: topics', 'learndash' ), LearnDash_Custom_Label::label_to_lower( 'topics' ) ),
					'value' 		=> 	'',
					'class'			=>	'small-text'
				);
				
				$this->shortcodes_option_fields['category_name'] = array(
					'id'			=>	$this->shortcodes_section_key . '_category_name',
					'name'  		=> 	'category_name', 
					'type'  		=> 	'text',
					'label' 		=> 	__('WP Category Slug', 'learndash'),
					'help_text'		=>	sprintf( _x( 'shows %s with mentioned WP category slug.', 'placeholders: topics', 'learndash' ), LearnDash_Custom_Label::label_to_lower( 'topics' ) ),
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
		
			if ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Topics_Taxonomies', 'wp_post_tag' ) == 'yes') {
				$this->shortcodes_option_fields['tag'] = array(
					'id'			=>	$this->shortcodes_section_key . '_tag',
					'name'  		=> 	'tag', 
					'type'  		=> 	'text',
					'label' 		=> 	__( 'WP Tag Slug', 'learndash'),
					'help_text'		=>	sprintf( _x( 'shows %s with mentioned WP tag slug.', 'placeholders: topics', 'learndash' ), LearnDash_Custom_Label::label_to_lower( 'topics' ) ),
					'value' 		=> 	'',
				);

				$this->shortcodes_option_fields['tag_id'] = array(
					'id'			=>	$this->shortcodes_section_key . '_tag_id',
					'name'  		=> 	'tag_id', 
					'type'  		=> 	'number',
					'label' 		=> 	__('WP Tag ID', 'learndash'),
					'help_text'		=>	sprintf( _x( 'shows %s with mentioned WP tag id.', 'placeholders: topics', 'learndash' ), LearnDash_Custom_Label::label_to_lower( 'topics' ) ),
					'value' 		=> 	'',
					'class'			=>	'small-text'
				);
			}
		
			$this->shortcodes_option_fields = apply_filters( 'learndash_settings_fields', $this->shortcodes_option_fields, $this->shortcodes_section_key );
			
			parent::init_shortcodes_section_fields();
		}
	}
}
