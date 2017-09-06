<?php
if ( ( class_exists( 'LearnDash_Shortcodes_Section' ) ) && ( !class_exists( 'LearnDash_Shortcodes_Section_courseinfo' ) ) ) {
	class LearnDash_Shortcodes_Section_courseinfo extends LearnDash_Shortcodes_Section {

		function __construct( $fields_args = array() ) {
			$this->fields_args = $fields_args;

			$this->shortcodes_section_key 			= 	'courseinfo';
			$this->shortcodes_section_title 		= 	sprintf( _x( '%s Info', 'placeholder: Course', 'learndash' ), LearnDash_Custom_Label::get_label( 'course' ) );
			$this->shortcodes_section_type			=	1;
			$this->shortcodes_section_description	=	sprintf( _x( "This shortcode displays %s related information on the certificate..", 'placeholder: course', 'learndash' ), LearnDash_Custom_Label::label_to_lower( 'course' ) );
			
			parent::__construct(); 
		}
		
		function init_shortcodes_section_fields() {
			$this->shortcodes_option_fields = array(
				'show' => array(
					'id'			=>	$this->shortcodes_section_key . '_show',
					'name'  		=> 	'show', 
					'type'  		=> 	'select',
					'label' 		=> 	__( 'Show', 'learndash' ),
					'help_text'		=>	sprintf( _x( 'This parameter determines the information to be shown by the shortcode.<br />cumulative - average for all %s of the %s.<br />aggregate - sum for all %s of the %s.', 'placeholders: quizzes, course, quizzes, course', 'learndash' ),
											LearnDash_Custom_Label::label_to_lower( 'quizzes' ), LearnDash_Custom_Label::label_to_lower( 'course' ),
											LearnDash_Custom_Label::label_to_lower( 'quizzes' ), LearnDash_Custom_Label::label_to_lower( 'course' ) ),
					'value' 		=> 	'ID',
					'options'		=>	array(
											'course_title'				=>	__('Title', 'learndash' ),
											'completed_on'				=>	__('Completed On (date)', 'learndash'),
											'cumulative_score'			=>	__('Cumulative Score', 'learndash'),
											'cumulative_points'			=>	__('Cumulative Points', 'learndash'),
											'cumulative_total_points'	=>	__('Cumulative Total Points', 'learndash'),
											'cumulative_percentage'		=>	__('Cumulative Percentage', 'learndash'),
											'cumulative_timespent'		=>	__('Cumulative Time Spent', 'learndash'),
											'aggregate_percentage'		=>	__('Aggregate Percentage', 'learndash'),
											'aggregate_score'			=>	__('Aggregate Score', 'learndash'),
											'aggregate_points'			=>	__('Aggregate Points', 'learndash'),
											'aggregate_total_points'	=>	__('Aggregate Total Points', 'learndash'),
											'aggregate_timespent'		=>	__('Aggregate Time Spent', 'learndash'),
										)
				),
				'format' => array(
					'id'			=>	$this->shortcodes_section_key . '_format',
					'name'  		=> 	'format', 
					'type'  		=> 	'text',
					'label' 		=> 	__( 'Format', 'learndash'),
					'help_text'		=>	__( 'This can be used to change the date format. Default: "F j, Y, g:i a" shows as <i>March 10, 2001, 5:16 pm</i>. See <a target="_blank" href="http://php.net/manual/en/function.date.php">the full list of available date formating strings  here.</a>', 'learndash' ),
					'value' 		=> 	'',
					'placeholder'	=>	'F j, Y, g:i a'
				),
			);

			if ( ( !isset( $this->fields_args['post_type'] ) ) || ( ( $this->fields_args['post_type'] != 'sfwd-courses' ) && ( $this->fields_args['post_type'] != 'sfwd-lessons' ) && ( $this->fields_args['post_type'] != 'sfwd-topic' ) && ( $this->fields_args['post_type'] != 'sfwd-certificates' ) ) ) {	

				$this->shortcodes_option_fields['course_id'] = array(
					'id'			=>	$this->shortcodes_section_key . '_course_id',
					'name'  		=> 	'course_id', 
					'type'  		=> 	'number',
					'label' 		=> 	sprintf( _x( '%s ID', 'placeholder: Course', 'learndash' ), LearnDash_Custom_Label::get_label( 'course' ) ),
					'help_text'		=>	sprintf( _x( 'Enter single %s ID.', 'placeholders: Course', 'learndash' ), LearnDash_Custom_Label::get_label( 'course' ) ),
					'value' 		=> 	'',
					'class'			=>	'small-text',
					'required'		=>	'required'
				);

				$this->shortcodes_option_fields['user_id'] = array(
					'id'			=>	$this->shortcodes_section_key . '_user_id',
					'name'  		=> 	'user_id', 
					'type'  		=> 	'number',
					'label' 		=> 	__( 'User ID', 'learndash' ),
					'help_text'		=>	__('Enter specific User ID. Leave blank for current User.', 'learndash' ),
					'value' 		=> 	'',
					'class'			=>	'small-text'
				);
			}
		
			$this->shortcodes_option_fields = apply_filters( 'learndash_settings_fields', $this->shortcodes_option_fields, $this->shortcodes_section_key );
			
			parent::init_shortcodes_section_fields();
		}
		
		function show_shortcodes_section_footer_extra() {
			?>
			<script>
				jQuery(document).ready(function() {
					if ( jQuery( 'form#learndash_shortcodes_form_courseinfo select#courseinfo_show' ).length) {
						jQuery( 'form#learndash_shortcodes_form_courseinfo select#courseinfo_show').change( function() {
							var selected = jQuery(this).val();
							if ( selected == 'completed_on' ) {
								jQuery( 'form#learndash_shortcodes_form_courseinfo #courseinfo_format_field').slideDown();
							} else {
								jQuery( 'form#learndash_shortcodes_form_courseinfo #courseinfo_format_field').hide();
								jQuery( 'form#learndash_shortcodes_form_courseinfo #courseinfo_format_field input').val('');
							}
						});		
						jQuery( 'form#learndash_shortcodes_form_courseinfo select#courseinfo_show').change();
					} 
				});
			</script>
			<?php
		}
	}
}
