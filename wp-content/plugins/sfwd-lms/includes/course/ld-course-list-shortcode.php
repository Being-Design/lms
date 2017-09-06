<?php
/**
 * Shortcodes and helper functions for listing
 * courses, lessons, quizzes, and topics
 * 
 * @since 2.1.0
 * 
 * @package LearnDash\Shortcodes
 */




/**
 * Shortcode to list courses
 * 
 * @since 2.1.0
 * 
 * @param  array 	$attr 	shortcode attributes
 * @return string   		shortcode output
 */
function ld_course_list( $attr ) {
	global $learndash_shortcode_used;
	
	$attr_defaults = array(
		'num' => '-1', 
		'post_type' => 'sfwd-courses', 
		'post_status' => 'publish', 
		'order' => 'DESC', 
		'orderby' => 'ID', 
		'mycourses' => false, 
		'post__in'	=> null,

		'meta_key' => '', 
		'meta_value' => '', 
		'meta_compare' => '',
		
		'tag' => '', 
		'tag_id' => 0, 
		'tag__and' => '', 
		'tag__in' => '', 
		'tag__not_in' => '', 
		'tag_slug__and' => '', 
		'tag_slug__in' => '', 

		'cat' => '', 
		'category_name' => 0, 
		'category__and' => '', 
		'category__in' => '', 
		'category__not_in' => '', 

		'tax_compare' => 'AND',
		'categoryselector' => '', 
		
		'author__in' => '', 
		'col' => '',
		'progress_bar' => false,
		'array' => false,
	);

	$post_type_slug = 'course';
	$post_type_Class = 'LearnDash_Settings_Courses_Taxonomies';
	
	if ( ( isset( $attr['post_type'] ) ) && ( !empty( $attr['post_type'] ) ) ) {
	
	
		if ( $attr['post_type'] == 'sfwd-lessons' ) {
			$post_type_slug = 'lesson';
			$post_type_Class = 'LearnDash_Settings_Lessons_Taxonomies';
		} else if ( $attr['post_type'] == 'sfwd-topic' ) {
			$post_type_slug = 'topic';
			$post_type_Class = 'LearnDash_Settings_Topics_Taxonomies';
		}
	}
	
	if ( !empty( $post_type_slug ) ) {
		$attr_defaults = array_merge(
			$attr_defaults,
			array(
				$post_type_slug . '_categoryselector' => '',
				$post_type_slug . '_cat' => '',
				$post_type_slug . '_category_name' => '',
				$post_type_slug . '_category__and' => '',
				$post_type_slug . '_category__in' => '',
				$post_type_slug . '_category__not_in' => '',
	
				$post_type_slug . '_tag' => '',
				$post_type_slug . '_tag_id' => '',
				$post_type_slug . '_tag__and' => '',
				$post_type_slug . '_tag__in' => '',
				$post_type_slug . '_tag__not_in' => '',
				$post_type_slug . '_tag_slug__and' => '',
				$post_type_slug . '_tag_slug__in' => '',
			)
		);
	}
	
	$atts = shortcode_atts( $attr_defaults, $attr );
	extract( $atts );
	
	global $post;
	
	$filter = array(
		'post_type' => $post_type, 
		'post_status' => $post_status, 
		'posts_per_page' => $num, 
		'order' => $order, 
		'orderby' => $orderby
	);
	
	if ( ! empty( $author__in ) ) {
		$filter['author__in'] = $author__in;
	}
	
	if ( ! empty( $meta_key ) ) {
		$filter['meta_key'] = $meta_key;
	}
	
	if ( ! empty( $meta_value ) ) {
		$filter['meta_value'] = $meta_value;
	}
	
	if ( ! empty( $meta_compare ) ) {
		if ( !empty( $filter['meta_key'] ) ) {
			$filter['meta_compare'] = $meta_compare;
		}
	}
	
	if ( ! empty( $post__in ) ) {
		$filter['post__in'] = $post__in;
	}	
	
	if ( LearnDash_Settings_Section::get_section_setting( $post_type_Class, 'wp_post_category' ) == 'yes') {
	
		if ( ! empty( $cat ) ) {
			//$filter['cat'] = $cat;
			
			if ( !isset( $filter['tax_query'] ) ) 
				$filter['tax_query'] = array();
			
			$filter['tax_query'][] = array(
				'taxonomy'	=>	'category',
				'field'		=>	'term_id',
				'terms'		=>	intval( $cat )
			);
		}
		
		if ( ! empty( $category_name ) ) {
			//$filter['category_name'] = $category_name;
			
			if ( !isset( $filter['tax_query'] ) ) 
				$filter['tax_query'] = array();
			
			$filter['tax_query'][] = array(
				'taxonomy'	=>	'category',
				'field'		=>	'slug',
				'terms'		=>	trim( $category_name )
			);
		}
	
		if ( ! empty( $category__and ) ) {
			//$filter['category__and'] = explode( ',', $category__and );
			
			$category__and = array_map('intval', explode( ',', $category__and ) );
			
			if ( !isset( $filter['tax_query'] ) ) 
				$filter['tax_query'] = array();
			
			$filter['tax_query'][] = array(
				'taxonomy'	=>	'category',
				'field'		=>	'term_id',
				'terms'		=>	$category__and,
				'operator'	=>	'AND'
			);
		}
	
		if ( ! empty( $category__in ) ) {
			//$filter['category__in'] = explode( ',', $category__in );

			$category__in = array_map('intval', explode( ',', $category__in ) );
			
			if ( !isset( $filter['tax_query'] ) ) 
				$filter['tax_query'] = array();
			
			$filter['tax_query'][] = array(
				'taxonomy'	=>	'category',
				'field'		=>	'term_id',
				'terms'		=>	$category__in,
				'operator'	=>	'IN'
			);
		}
	
		if ( ! empty( $category__not_in ) ) {
			//$filter['category__not_in'] = explode( ',', $category__not_in );
			
			$category__not_in = array_map('intval', explode( ',', $category__not_in ) );

			if ( !isset( $filter['tax_query'] ) ) 
				$filter['tax_query'] = array();
			
			$filter['tax_query'][] = array(
				'taxonomy'	=>	'category',
				'field'		=>	'term_id',
				'terms'		=>	$category__not_in,
				'operator'	=>	'NOT IN'
			);
		}
	}
		
	if ( LearnDash_Settings_Section::get_section_setting( $post_type_Class, 'wp_post_tag' ) == 'yes') {
	
		if ( ! empty( $tag ) ) {
			//$filter['tag'] = $tag;
			
			if ( !isset( $filter['tax_query'] ) ) 
				$filter['tax_query'] = array();
			
			$filter['tax_query'][] = array(
				'taxonomy'	=>	'post_tag',
				'field'		=>	'slug',
				'terms'		=>	trim( $tag )
			);
			
		}
	
		if ( ! empty( $tag_id ) ) {
			//$filter['tag_id'] = $tag;
			
			if ( !isset( $filter['tax_query'] ) ) 
				$filter['tax_query'] = array();
			
			$filter['tax_query'][] = array(
				'taxonomy'	=>	'post_tag',
				'field'		=>	'term_id',
				'terms'		=>	intval( $tag_id ),
			);
			
		}
	
		if ( ! empty( $tag__and ) ) {
			//$filter['tag__and'] = explode( ',', $tag__and );
			
			$tag__and = array_map('intval', explode( ',', $tag__and ) );
			
			if ( !isset( $filter['tax_query'] ) ) 
				$filter['tax_query'] = array();
			
			$filter['tax_query'][] = array(
				'taxonomy'	=>	'post_tag',
				'field'		=>	'term_id',
				'terms'		=>	$tag__and,
				'operator'	=>	'AND'
			);
		}
	
		if ( ! empty( $tag__in ) ) {
			//$filter['tag__in'] = explode( ',', $tag__in );
			
			$tag__in = array_map('intval', explode( ',', $tag__in ) );
			
			if ( !isset( $filter['tax_query'] ) ) 
				$filter['tax_query'] = array();
			
			$filter['tax_query'][] = array(
				'taxonomy'	=>	'post_tag',
				'field'		=>	'term_id',
				'terms'		=>	$tag__in,
				'operator'	=>	'IN'
			);
			
		}
	
		if ( ! empty( $tag__not_in ) ) {
			//$filter['tag__not_in'] = explode( ',', $tag__not_in );
			
			$tag__not_in = array_map('intval', explode( ',', $tag__not_in ) );
			
			if ( !isset( $filter['tax_query'] ) ) 
				$filter['tax_query'] = array();
			
			$filter['tax_query'][] = array(
				'taxonomy'	=>	'post_tag',
				'field'		=>	'term_id',
				'terms'		=>	$tag__not_in,
				'operator'	=>	'NOT IN'
			);
		}
	
		if ( ! empty( $tag_slug__and ) ) {
			//$filter['tag_slug__and'] = explode( ',', $tag_slug__and );
			
			$tag_slug__and = array_map('trim', explode( ',', $tag_slug__and ) );

			if ( !isset( $filter['tax_query'] ) ) 
				$filter['tax_query'] = array();
			
			$filter['tax_query'][] = array(
				'taxonomy'	=>	'post_tag',
				'field'		=>	'slug',
				'terms'		=>	$tag_slug__and,
				'operator'	=>	'AND'
			);
		}
	
		if ( ! empty( $tag_slug__in ) ) {
			//$filter['tag_slug__in'] = explode( ',', $tag_slug__in );
			
			$tag_slug__in = array_map('trim', explode( ',', $tag_slug__in ) );

			if ( !isset( $filter['tax_query'] ) ) 
				$filter['tax_query'] = array();
			
			$filter['tax_query'][] = array(
				'taxonomy'	=>	'post_tag',
				'field'		=>	'slug',
				'terms'		=>	$tag_slug__in,
				'operator'	=>	'IN'
			);
		}
	}
		
	
	if ( LearnDash_Settings_Section::get_section_setting( $post_type_Class, 'ld_'. $post_type_slug .'_category' ) == 'yes') {

		// course_cat="123" 
		if ( ( isset( $atts[$post_type_slug .'_cat'] ) )  && ( ! empty( $atts[$post_type_slug .'_cat'] ) ) ) {

			if ( !isset( $filter['tax_query'] ) ) 
				$filter['tax_query'] = array();
			
			$filter['tax_query'][] = array(
				'taxonomy'	=>	'ld_'. $post_type_slug .'_category',
				'field'		=>	'term_id',
				'terms'		=>	intval( $atts[$post_type_slug .'_cat'] )
			);
		}
		
		// course_category_name (string) - use category slug.	
		// course_category_name="course-category-one"
		if ( ( isset( $atts[$post_type_slug .'_category_name'] ) )  && ( ! empty( $atts[$post_type_slug .'_category_name'] ) ) ) {

			if ( !isset( $filter['tax_query'] ) ) 
				$filter['tax_query'] = array();
			
			$filter['tax_query'][] = array(
				'taxonomy'	=>	'ld_'. $post_type_slug .'_category',
				'field'		=>	'slug',
				'terms'		=>	trim( $atts[$post_type_slug .'_category_name'] )
			);
		}

		// course_category__and (array) - use category id.
		if ( ( isset( $atts[$post_type_slug .'_category__and'] ) )  && ( ! empty( $atts[$post_type_slug .'_category__and'] ) ) ) {
			
			$cat__and = array_map('intval', explode( ',', $atts[$post_type_slug .'_category__and'] ) );
			
			if ( !isset( $filter['tax_query'] ) ) 
				$filter['tax_query'] = array();
			
			$filter['tax_query'][] = array(
				'taxonomy'	=>	'ld_'. $post_type_slug .'_category',
				'field'		=>	'term_id',
				'terms'		=>	$cat__and,
				'operator'	=>	'AND',
				'include_children'	=>	false,
			);
		}
		
		// course_category__in (array) - use category id.
		if ( ( isset( $atts[$post_type_slug .'_category__in'] ) )  && ( ! empty( $atts[$post_type_slug .'_category__in'] ) ) ) {

			$cat__in = array_map('intval', explode( ',', $atts[$post_type_slug .'_category__in'] ) );

			if ( !isset( $filter['tax_query'] ) ) 
				$filter['tax_query'] = array();
			
			$filter['tax_query'][] = array(
				'taxonomy'	=>	'ld_'. $post_type_slug .'_category',
				'field'		=>	'term_id',
				'terms'		=>	$cat__in,
				'operator'	=>	'IN',
				'include_children'	=>	false,
			);
		}
		
		// course_category___not_in (array) - use category id.
		if ( ( isset( $atts[$post_type_slug .'_category__not_in'] ) )  && ( ! empty( $atts[$post_type_slug .'_category__not_in'] ) ) ) {

			$cat__not_in = array_map('intval', explode( ',', $atts[$post_type_slug .'_category__not_in'] ) );
			
			if ( !isset( $filter['tax_query'] ) ) 
				$filter['tax_query'] = array();
			
			$filter['tax_query'][] = array(
				'taxonomy'	=>	'ld_'. $post_type_slug .'_category',
				'field'		=>	'term_id',
				'terms'		=>	$cat__not_in,
				'operator'	=>	'NOT IN',
				'include_children'	=>	false,
			);
		}
	}
	
	if ( LearnDash_Settings_Section::get_section_setting( $post_type_Class, 'ld_'. $post_type_slug .'_tag' ) == 'yes') {
	
		// course_tag (string) - use tag slug.
		if ( ( isset( $atts[$post_type_slug .'_tag'] ) )  && ( ! empty( $atts[$post_type_slug .'_tag'] ) ) ) {

			if ( !isset( $filter['tax_query'] ) ) 
				$filter['tax_query'] = array();
			
			$filter['tax_query'][] = array(
				'taxonomy'	=>	'ld_'. $post_type_slug .'_tag',
				'field'		=>	'slug',
				'terms'		=>	trim( $atts[$post_type_slug .'_tag'] )
			);
		}
		
		
		// course_tag_id (int) - use tag id.
		if ( ( isset( $atts[$post_type_slug .'_tag_id'] ) )  && ( ! empty( $atts[$post_type_slug .'_tag_id'] ) ) ) {

			if ( !isset( $filter['tax_query'] ) ) 
				$filter['tax_query'] = array();
			
			$filter['tax_query'][] = array(
				'taxonomy'	=>	'ld_'. $post_type_slug .'_tag',
				'field'		=>	'term_id',
				'terms'		=>	intval( $atts[$post_type_slug .'_tag_id'] )
			);
		}
		
		// course_tag__and (array) - use tag ids.
		if ( ( isset( $atts[$post_type_slug .'_tag__and'] ) )  && ( ! empty( $atts[$post_type_slug .'_tag__and'] ) ) ) {

			$tag__and = array_map('intval', explode( ',', $atts[$post_type_slug .'_tag__and'] ) );
			
			if ( !isset( $filter['tax_query'] ) ) 
				$filter['tax_query'] = array();
						
			$filter['tax_query'][] = array(
				'taxonomy'	=>	'ld_'. $post_type_slug .'_tag',
				'field'		=>	'term_id',
				'terms'		=>	$tag__and,
				'operator'	=>	'AND'
			);
		}
		
		// course_tag__in (array) - use tag ids.
		if ( ( isset( $atts[$post_type_slug .'_tag__in'] ) )  && ( ! empty( $atts[$post_type_slug .'_tag__in'] ) ) ) {

			$tag__in = array_map('intval', explode( ',', $atts[$post_type_slug .'_tag__in'] ) );

			if ( !isset( $filter['tax_query'] ) ) 
				$filter['tax_query'] = array();
			
			$filter['tax_query'][] = array(
				'taxonomy'	=>	'ld_'. $post_type_slug .'_tag',
				'field'		=>	'term_id',
				'terms'		=>	$tag__in,
				'operator'	=>	'IN'
			);
		}

		// course_tag__not_in (array) - use tag ids.
		if ( ( isset( $atts[$post_type_slug .'_tag__not_in'] ) )  && ( ! empty( $atts[$post_type_slug .'_tag__not_in'] ) ) ) {

			$tag__not_in = array_map('intval', explode( ',', $atts[$post_type_slug .'_tag__not_in'] ) );
			
			if ( !isset( $filter['tax_query'] ) ) 
				$filter['tax_query'] = array();
			
			$filter['tax_query'][] = array(
				'taxonomy'	=>	'ld_'. $post_type_slug .'_tag',
				'field'		=>	'term_id',
				'terms'		=>	$tag__not_in,
				'operator'	=>	'NOT IN'
			);
		}
		
		// course_tag_slug__and (array) - use tag slugs.
		if ( ( isset( $atts[$post_type_slug .'_tag_slug__and'] ) )  && ( ! empty( $atts[$post_type_slug .'_tag_slug__and'] ) ) ) {

			$tag_slug__and = array_map('trim', explode( ',', $atts[$post_type_slug .'_tag_slug__and'] ) );
			
			if ( !isset( $filter['tax_query'] ) ) 
				$filter['tax_query'] = array();
			
			$filter['tax_query'][] = array(
				'taxonomy'	=>	'ld_'. $post_type_slug .'_tag',
				'field'		=>	'slug',
				'terms'		=>	$tag_slug__and,
				'operator'	=>	'AND'
			);
		}
		
		
		// course_tag_slug__in (array) - use tag slugs.
		if ( ( isset( $atts[$post_type_slug .'_tag_slug__in'] ) )  && ( ! empty( $atts[$post_type_slug .'_tag_slug__in'] ) ) ) {
			
			$tag_slug__in = array_map('trim', explode( ',', $atts[$post_type_slug .'_tag_slug__in'] ) );
			
			if ( !isset( $filter['tax_query'] ) ) 
				$filter['tax_query'] = array();
			
			$filter['tax_query'][] = array(
				'taxonomy'	=>	'ld_'. $post_type_slug.'_tag',
				'field'		=>	'slug',
				'terms'		=>	$tag_slug__in,
				'operator'	=>	'IN'
			);
		}
	}	


	if ( ( isset( $filter['tax_query'] ) ) && ( count( $filter['tax_query'] ) > 1 ) ) {
		// Due to a quick on WP_Query the 'compare' option needs to be in the first position. 
		// So we save off the current tax_query, add the 'relation', then merge in the original tax_query 
		$tax_query = $filter['tax_query'];
		$filter['tax_query'] = array( 'relation' => $tax_compare );
		$filter['tax_query'] = array_merge( $filter['tax_query'], $tax_query );
		
	} else if ( ! empty( $meta_compare ) ) {
		$filter['meta_compare'] = $meta_compare;
	}
	
	$filter = apply_filters('learndash_ld_course_list_query_args', $filter, $atts );
	
	if ( $array ) {
		return get_posts( $filter );
	}
	
	if ( @$post->post_type == $post_type ) {
		$filter['post__not_in'] = array( $post->ID );
	}
	
	//error_log('filter<pre>'. print_r($filter, true) .'</pre>');
	$loop = new WP_Query( $filter );
	//error_log('loop<pre>'. print_r($loop, true) .'</pre>');
	
	$level = ob_get_level();
	ob_start();
	$ld_categorydropdown = '';

	if ( ( trim( $categoryselector ) == 'true' ) && ( LearnDash_Settings_Section::get_section_setting( $post_type_Class, 'wp_post_category' ) == 'yes') ) {
		$cats = array();
		$posts = get_posts( $filter );
		
		
		// We first need to build a listing of the categories used by each of the queried posts. 
		if ( !empty( $posts ) ) {
			foreach( $posts as $post ) {
				$post_categories = wp_get_post_categories( $post->ID );
				if ( !empty( $post_categories ) ) {
					foreach( $post_categories as $c ) {

						if ( empty( $cats[ $c ] ) ) {
							$cat = get_category( $c );
							$cats[ $c ] = array(
								'id' => $cat->cat_ID, 
								'name' => $cat->name, 
								'slug' => $cat->slug, 
								'parent' => $cat->parent, 
								'count' => 0, 
								'posts' => array()
							); 
						}

						$cats[ $c ]['count']++;
						$cats[ $c ]['posts'][] = $post->ID;
					}
				}
			}
			
			// Once we have these categories we need to requery the categories in order to get them into a proper ordering. 
			if ( !empty( $cats ) ) {
		
				// And also let this query be filtered.
				$get_categories_args = apply_filters(
					'learndash_course_list_category_args', 
					array(
						'taxonomy'	=>	'category',
						'type' 		=>	$post_type,
						'include'	=>	array_keys($cats),
						'orderby'	=>	'name',
						'order'		=>	'ASC'
					)
				);
		
				if ( !empty( $get_categories_args ) ) {
					$categories = get_categories( $get_categories_args );
					if ( !empty( $categories ) ) {

						$categorydropdown = '<div id="ld_categorydropdown">';
						$categorydropdown.= '<form method="get">
								<label for="ld_categorydropdown_select">' . __( 'Categories', 'learndash' ) . '</label>
								<select id="ld_categorydropdown_select" name="catid" onChange="jQuery(\'#ld_categorydropdown form\').submit()">';
						$categorydropdown.= '<option value="">' . __( 'Select category', 'learndash' ) . '</option>';

						foreach( $categories as $category ) {
						
							if ( isset( $cats[$category->term_id] ) ) {
								$cat = $cats[$category->term_id];
								$selected =( empty( $_GET['catid'] ) || $_GET['catid'] != $cat['id'] ) ? '' : 'selected="selected"';
								$categorydropdown.= "<option value='" . $cat['id'] . "' " . $selected . '>' . $cat['name'] . ' (' . $cat['count'] . ')</option>';
							}
						}

						$categorydropdown.= "</select><input type='submit' style='display:none'></form></div>";

						/**
						 * Filter HTML output of category dropdown
						 * 
						 * @since 2.1.0
						 * 
						 * @param  string  $categorydropdown
						 */
						echo apply_filters( 'ld_categorydropdown', $categorydropdown, $atts, $filter );
					}
				}
			}
		}
	} else {
		$categoryselector = '';
		$atts['categoryselector'];
	}
	
	
	// We can only support one of the other category OR course_category selectors
	if ( ( trim( $atts[$post_type_slug .'_categoryselector'] ) == 'true' ) && ( empty( $categoryselector ) )
	  && ( LearnDash_Settings_Section::get_section_setting( $post_type_Class, 'ld_'. $post_type_slug .'_category' ) == 'yes') ) {
		$ld_cats = array();
		$posts = get_posts( $filter );
		
		// We first need to build a listing of the categories used by each of the queried posts. 
		if ( !empty( $posts ) ) {
			$args = array('fields' => 'ids');
			foreach( $posts as $post ) {
				$post_categories = wp_get_object_terms($post->ID, 'ld_'. $post_type_slug .'_category', $args);
				if ( !empty( $post_categories ) ) {
					foreach( $post_categories as $c ) {

						if ( empty( $ld_cats[ $c ] ) ) {
							$ld_cat = get_term( $c, 'ld_'. $post_type_slug .'_category' );
							$ld_cats[ $c ] = array(
								'id' => $ld_cat->cat_ID, 
								'name' => $ld_cat->name, 
								'slug' => $ld_cat->slug, 
								'parent' => $ld_cat->parent, 
								'count' => 0, 
								'posts' => array()
							); 
						}

						$ld_cats[ $c ]['count']++;
						$ld_cats[ $c ]['posts'][] = $post->ID;
					}
				}
			}
			
			// Once we have these categories we need to requery the categories in order to get them into a proper ordering. 
			if ( !empty( $ld_cats ) ) {
				
				// And also let this query be filtered.
				$get_ld_categories_args = apply_filters(
					'learndash_course_list_'. $post_type_slug .'_category_args', 
					array(
						'taxonomy'	=>	'ld_'. $post_type_slug .'_category',
						'type' 		=>	$post_type,
						'include'	=>	array_keys( $ld_cats ),
						'orderby'	=>	'name',
						'order'		=>	'ASC'
					)
				);
		
				$post_type_object = get_post_type_object( $atts['post_type'] );
				//error_log('post_type_slug['. $atts['post_type'] .'] post_type_object<pre>'. print_r($post_type_object, true) .'</pre>');
				
				$tax_object = get_taxonomy('ld_'. $post_type_slug .'_category');
				
				if ( !empty( $get_ld_categories_args ) ) {
					$ld_categories = get_terms( $get_ld_categories_args );
					if ( !empty( $ld_categories ) ) {

						$ld_categorydropdown = '<div id="ld_'. $post_type_slug .'_categorydropdown">';
						$ld_categorydropdown.= '<form method="get">
								<label for="ld_'. $post_type_slug .'_categorydropdown_select">' . $tax_object->labels->name . '</label>
								<select id="ld_'. $post_type_slug .'_categorydropdown_select" name="'. $post_type_slug .'_catid" onChange="jQuery(\'#ld_'. $post_type_slug .'_categorydropdown form\').submit()">';
						$ld_categorydropdown.= '<option value="">' . sprintf( _x( 'Select %s', 'placeholder: LD Category label', 'learndash' ), $tax_object->labels->name )  . '</option>';

						foreach( $ld_categories as $ld_category ) {
						
							if ( isset( $ld_cats[$ld_category->term_id] ) ) {
								$ld_cat = $ld_cats[$ld_category->term_id];
								$selected =( empty( $_GET[$post_type_slug . '_catid'] ) || $_GET[$post_type_slug . '_catid'] != $ld_category->term_id ) ? '' : 'selected="selected"';
								$ld_categorydropdown .= "<option value='" . $ld_category->term_id . "' " . $selected . '>' . $ld_cat['name'] . ' (' . $ld_cat['count'] . ')</option>';
							}
						}

						$ld_categorydropdown.= "</select><input type='submit' style='display:none'></form></div>";

						/**
						 * Filter HTML output of category dropdown
						 * 
						 * @since 2.1.0
						 * 
						 * @param  string  $categorydropdown
						 */
						echo apply_filters( 'ld_'. $post_type_slug .'_categorydropdown', $ld_categorydropdown, $atts, $filter );
					}
				}
			}
		}
	} else {
		$atts[$post_type_slug .'_categoryselector'] = '';
	}
	

	$col = intval($col);
	if (!empty($col)) {
		$row_item_count = 0;
	}
	
	while ( $loop->have_posts() ) {
		$loop->the_post();
		if ( trim( $categoryselector ) == 'true' && ! empty( $_GET['catid'] ) && !in_array( get_the_ID(), (array)@$cats[ $_GET['catid']]['posts'] ) ) {
			continue;
		} else if ( trim( $atts[$post_type_slug . '_categoryselector'] ) == 'true' && ! empty( $_GET[$post_type_slug . '_catid'] ) && !in_array( get_the_ID(), (array)@$ld_cats[ $_GET[$post_type_slug . '_catid']]['posts'] ) ) {
			continue;
		}

		
		if ( !$mycourses || sfwd_lms_has_access( get_the_ID() ) ) {
			if ( !empty( $col ) ) {
				$row_item_count += 1;

				if ( $row_item_count == 1 ) {
					?><div class="row"><?php
				}
			}
			
			echo SFWD_LMS::get_template( 'course_list_template', array('shortcode_atts' => $atts) );

			if ( !empty( $col ) ) {
				// make sure to close the div if the current loop iteration count is equal with the 
				// $col value OR it's the last item in the loop
				if ( $row_item_count >= $col || $loop->current_post + 1 == $loop->post_count ) {
					?></div><?php
					$row_item_count = 0;
				}
			}
		}
	}

	$output = learndash_ob_get_clean( $level );
	wp_reset_query();

	$learndash_shortcode_used = true;

	/**
	 * Filter HTML output of category dropdown
	 * 
	 * @since 2.1.0
	 * 
	 * @param  string $output
	 */
	return apply_filters( 'ld_course_list', $output, $atts, $filter );
}

add_shortcode( 'ld_course_list', 'ld_course_list' );



/**
 * Shortcode to list lessons
 * 
 * @since 2.1.0
 * 
 * @param  array 	$attr 	shortcode attributes
 * @return string   		shortcode output
 */
function ld_lesson_list( $attr ) {
	global $learndash_shortcode_used;
	$learndash_shortcode_used = true;
	
	$attr['post_type'] = 'sfwd-lessons';
	$attr['mycourses'] = false;
	return ld_course_list( $attr );
}

add_shortcode( 'ld_lesson_list', 'ld_lesson_list' );



/**
 * Shortcode to list quizzes
 * 
 * @since 2.1.0
 * 
 * @param  array 	$attr 	shortcode attributes
 * @return string   		shortcode output
 */
function ld_quiz_list( $attr ) {
	global $learndash_shortcode_used;
	$learndash_shortcode_used = true;
	
	$attr['post_type'] = 'sfwd-quiz';
	$attr['mycourses'] = false;
	return ld_course_list( $attr );
}

add_shortcode( 'ld_quiz_list', 'ld_quiz_list' );



/**
 * Shortcode to list topics
 * 
 * @since 2.1.0
 * 
 * @param  array 	$attr 	shortcode attributes
 * @return string   		shortcode output
 */
function ld_topic_list( $attr ) {
	global $learndash_shortcode_used;
	$learndash_shortcode_used = true;
	
	$attr['post_type'] = 'sfwd-topic';
	$attr['mycourses'] = false;
	return ld_course_list( $attr );
}

add_shortcode( 'ld_topic_list', 'ld_topic_list' );



/**
 * Check if user has access
 *
 * @todo  duplicate function, exists in other places
 *        check it's use and consolidate
 * 
 * @since 2.1.0
 * 
 * @param  int $course_id
 * @param  int $user_id
 * @return bool
 */
function ld_course_check_user_access( $course_id, $user_id = null ) {
	return sfwd_lms_has_access( $course_id, $user_id );
}



/**
 * Shortcode to display content to users that have access to current course id
 *
 * @todo  function is duplicate of learndash_student_check_shortcode()
 * 
 * @since 2.1.0
 * 
 * @param  array 	$attr 		shortcode attributes
 * @param  string 	$content 	content of shortcode
 * @return string   			shortcode output
 */
function learndash_visitor_check_shortcode( $atts, $content = '' ) {
	global $learndash_shortcode_used;

	if ( !empty( $content ) ) {
	
		$defaults = array(
			'course_id' => learndash_get_course_id(),
			'content'	=>	$content
		);
		$atts = wp_parse_args( $atts, $defaults );
		$atts = apply_filters('learndash_visitor_shortcode_atts', $atts );
		
		if ( ( !is_user_logged_in() ) || ( ( ! empty( $atts['course_id'] ) ) && ( ! sfwd_lms_has_access( $atts['course_id'] ) ) ) ) {
			$learndash_shortcode_used = true;
			$content = do_shortcode( $atts['content'] );
		} else {
			$content = '';
		}
	}	

	return $content;
}

add_shortcode( 'visitor', 'learndash_visitor_check_shortcode' );



/**
 * Shortcode to display content to users that have access to current course id
 *
 * @todo  function is duplicate of learndash_visitor_check_shortcode()
 * 
 * @since 2.1.0
 * 
 * @param  array 	$attr 		shortcode attributes
 * @param  string 	$content 	content of shortcode
 * @return string   			shortcode output
 */
function learndash_student_check_shortcode( $atts, $content = null ) {
	global $learndash_shortcode_used;

	if ( ( !is_null( $content ) ) && ( is_user_logged_in() ) ) {
	
		$defaults = array(
			'course_id' => 	learndash_get_course_id(),
			'user_id'	=>	get_current_user_id(),
			'content'	=>	$content
		);
		$atts = wp_parse_args( $atts, $defaults );
		
		$atts = apply_filters('learndash_student_shortcode_atts', $atts );

		if ( ( !empty( $atts['content'] ) ) && ( !empty( $atts['user_id'] ) ) && ( !empty( $atts['course_id'] ) ) ) {
			// The reason we are doing this check is because 'sfwd_lms_has_access' will return true if the course does not exist. 
			// This needs to be changed to return some other value because true signals the calling function that all is well. 
			$course_id = learndash_get_course_id( $atts['course_id'] );
			if ( $course_id == $atts['course_id'] ) {
				if ( sfwd_lms_has_access( $atts['course_id'], $atts['user_id'] ) ) {
					$learndash_shortcode_used = true;
					return do_shortcode( $atts['content'] );
				}
			}
		}
	}
	
	return '';
}

add_shortcode( 'student', 'learndash_student_check_shortcode' );


/**
 * Shortcode to display content to users that have access to current group id
 *
 * @todo  function is duplicate of learndash_visitor_check_shortcode()
 * 
 * @since 2.3
 * 
 * @param  array 	$attr 		shortcode attributes
 * @param  string 	$content 	content of shortcode
 * @return string   			shortcode output
 */
function learndash_ld_group_check_shortcode( $atts, $content = null ) { 
	global $learndash_shortcode_used; 

	if ( ( is_singular() ) && ( !is_null( $content ) ) && ( is_user_logged_in() ) ) {

		$defaults = array(
			'group_id' 	=> 	0,
			'user_id'	=>	get_current_user_id(),
			'content'	=>	$content
		);
		$atts = wp_parse_args( $atts, $defaults );

		$atts = apply_filters('learndash_ld_group_shortcode_atts', $atts, $content);

		if ( ( !empty( $atts['content'] ) ) && ( !empty( $atts['user_id'] ) ) && ( !empty( $atts['group_id'] ) ) ) {
			if ( learndash_is_user_in_group( $atts['user_id'], $atts['group_id'] ) ) { 
				$learndash_shortcode_used = true;
				return do_shortcode( $atts['content'] ); 
			}
		}
	}
	
	return ''; 
}

add_shortcode( 'ld_group', 'learndash_ld_group_check_shortcode' );

/**
 * Generates output for course status shortcodes
 * 
 * @since 2.1.0
 * 
 * @param  array 	$attr 		shortcode attributes
 * @param  string 	$content 	content of shortcode
 * @param  string 	$status  	status of course
 * @return string 				shortcode output
 */
function learndash_course_status_content_shortcode( $atts, $content, $status ) {

	$user_id = empty( $atts['user_id'] ) ? get_current_user_id() : intval( $atts['user_id'] );
	$course_id = empty( $atts['course_id'] ) ? learndash_get_course_id() : learndash_get_course_id( intval( $atts['course_id'] ) );
	
	if (( !empty( $course_id ) ) && ( !empty( $user_id ) )) {
		if ( sfwd_lms_has_access( $course_id, $user_id ) ) {
			if ( learndash_course_status( $course_id, $user_id ) == $status ) {
				return do_shortcode( $content );
			}
		}
	}
	return '';
}



/**
 * Shortcode that shows the content if the user has completed the course. 
 * 
 * @since 2.1.0
 * 
 * @param  array 	$attr 		shortcode attributes
 * @param  string 	$content 	content of shortcode
 * @return string   			shortcode output
 */
function learndash_course_complete_shortcode( $atts, $content ) {
	global $learndash_shortcode_used;
	$learndash_shortcode_used = true;
	
	return learndash_course_status_content_shortcode( $atts, $content, __( 'Completed', 'learndash' ) );
}

add_shortcode( 'course_complete', 'learndash_course_complete_shortcode' );



/**
 * Shortcode that shows the content if the user is in progress on the course.
 * 
 * @since 2.1.0
 * 
 * @param  array 	$attr 		shortcode attributes
 * @param  string 	$content 	content of shortcode
 * @return string   			shortcode output
 */
function learndash_course_inprogress_shortcode( $atts, $content ) {
	global $learndash_shortcode_used;
	$learndash_shortcode_used = true;
	
	return learndash_course_status_content_shortcode( $atts, $content, __( 'In Progress', 'learndash' ) );
}

add_shortcode( 'course_inprogress', 'learndash_course_inprogress_shortcode' );



/**
 * Shortcode that shows the content if the user has mnot started the course
 * 
 * @since 2.1.0
 * 
 * @param  array 	$attr 		shortcode attributes
 * @param  string 	$content 	content of shortcode
 * @return string   			shortcode output
 */
function learndash_course_notstarted_shortcode( $atts, $content ) {
	global $learndash_shortcode_used;
	$learndash_shortcode_used = true;
	
	return learndash_course_status_content_shortcode( $atts, $content, __( 'Not Started', 'learndash' ) );
}

add_shortcode( 'course_notstarted', 'learndash_course_notstarted_shortcode' );


/**
 * Shortcode that shows the Course Expire date for user access. 
 * 
 * @since 2.1.0
 * 
 * @param  array 	$attr 		shortcode attributes
 * @param  string 	$content 	content of shortcode
 * @return string   			shortcode output
 */
function learndash_course_expire_status_shortcode( $atts, $content ) {
	global $learndash_shortcode_used;
	$learndash_shortcode_used = true;
	
	$content_shortcode = '';
	
	$atts = shortcode_atts( 
		array(
			'course_id' 	=> 	learndash_get_course_id(), 
			'user_id' 		=> 	get_current_user_id(), 
			'label_before'	=>	sprintf( _x('%s access will expire on:', 'Course access will expire on:', 'learndash'), LearnDash_Custom_Label::get_label( 'course' ) ),
			'label_after'	=>	sprintf( _x('%s access expired on:', 'Course access expired on:', 'learndash'), LearnDash_Custom_Label::get_label( 'course' ) ),
			'format'		=>	get_option('date_format') .' '. get_option('time_format')
		), 
		$atts
	);

	if ( ( !empty( $atts['course_id'] ) ) && ( !empty( $atts['user_id'] ) ) ) {
		if ( sfwd_lms_has_access( $atts['course_id'], $atts['user_id'] ) ) {
			$course_meta = get_post_meta( $atts['course_id'], '_sfwd-courses', true );
			
			$courses_access_from = ld_course_access_from( $atts['course_id'], $atts['user_id'] );			
			if ( empty( $courses_access_from ) ) {
				$courses_access_from = learndash_user_group_enrolled_to_course_from( $atts['user_id'], $atts['course_id'] );
			}
			
			if ( !empty( $courses_access_from ) ) {
						
				$expire_on = ld_course_access_expires_on( $atts['course_id'], $atts['user_id'] );
				if (!empty($expire_on)) {
					if ($expire_on > time()) {
						$content_shortcode .= $atts['label_before'];
					} else {
						$content_shortcode .= $atts['label_after'];
					}
					$content_shortcode .= ' '. date($atts['format'], $expire_on + (get_option('gmt_offset') * 3600));
				}
			}
		}
	} 
	
	if (!empty( $content_shortcode ) ) {
		$content .= $content_shortcode;
	}
	return $content;
}

add_shortcode( 'ld_course_expire_status', 'learndash_course_expire_status_shortcode' );


function learndash_quiz_shortcode( $atts, $content = '' ) {

	global $learndash_shortcode_used;
	
	$atts = shortcode_atts( 
		array(
			'quiz_id'	=>	0
		), 
		$atts
	);
	
	if ( !empty( $atts['quiz_id'] ) ) {
		$learndash_shortcode_used = true;
		
		$lesson_progression_enabled = false;
		$has_access = '';
		
		$quiz_post = get_post( $atts['quiz_id'] );
		if ( $quiz_post instanceof WP_Post ) {
			$quiz_settings = learndash_get_setting( $atts['quiz_id'] );
			$meta = SFWD_CPT_Instance::$instances[ 'sfwd-quiz' ]->get_settings_values( 'sfwd-quiz' );
		
			$show_content = ! ( ! empty( $lesson_progression_enabled) && ! is_quiz_accessable( null, $quiz_post ) );
			$attempts_count = 0;
			$repeats = trim( @$quiz_settings['repeats'] );

			if ( $repeats != '' ) {
				$user_id = get_current_user_id();

				if ( $user_id ) {
					$usermeta = get_user_meta( $user_id, '_sfwd-quizzes', true );
					$usermeta = maybe_unserialize( $usermeta );

					if ( ! is_array( $usermeta ) ) { 
						$usermeta = array();
					}

					if ( ! empty( $usermeta ) )	{
						foreach ( $usermeta as $k => $v ) {
							if ( $v['quiz'] == $atts['quiz_id'] ) { 
								$attempts_count++;
							}
						}
					}
				}
			}

			$attempts_left = ( $repeats == '' || $repeats >= $attempts_count );

			if ( ! empty( $lesson_progression_enabled) && ! is_quiz_accessable( null, $quiz_post ) ) {
				add_filter( 'comments_array', 'learndash_remove_comments', 1, 2 );
			}

			 /**
			 * Filter for content access
			 *
			 * If not null, will display instead of quiz content
			 * 
			 * @since 2.1.0
			 * 
			 * @param  string
			 */
			$access_message = apply_filters( 'learndash_content_access', null, $quiz_post );

			if ( ! is_null( $access_message ) ) {
				$quiz_content = $access_message;
			} else {							
				if ( ! empty( $quiz_settings['quiz_pro'] ) ) {
					$quiz_content = wptexturize( do_shortcode( '[LDAdvQuiz '. $quiz_settings['quiz_pro'] .']' ) );
				}

				 /**
				 * Filter quiz content
				 * 
				 * @since 2.1.0
				 * 
				 * @param  string  $quiz_content
				 */
				$quiz_content = apply_filters( 'learndash_quiz_content', $quiz_content, $quiz_post );
			}

			$level = ob_get_level();
			ob_start();
			include( SFWD_LMS::get_template( 'quiz', null, null, true ) );
			$content .= learndash_ob_get_clean( $level );
		
			// Added this defined wrap in v2.1.8 as it was effecting <pre></pre>, <code></code> and other formatting of the content. 
			// See wrike https://www.wrike.com/open.htm?id=77352698 as to why this define exists
			if ( ( defined( 'LEARNDASH_NEW_LINE_AND_CR_TO_SPACE' ) ) && ( LEARNDASH_NEW_LINE_AND_CR_TO_SPACE == true ) ) {

				// Why is this here? 
				$content = str_replace( array( "\n", "\r" ), ' ', $content );
			}
		
			$user_has_access = $has_access ? 'user_has_access':'user_has_no_access';

			 /**
			 * Filter content to be return inside div
			 * 
			 * @since 2.1.0
			 * 
			 * @param  string  $content 
			 */
			$content = '<div class="learndash '. $user_has_access .'"  id="learndash_post_'. $quiz_post->ID.'">'.apply_filters( 'learndash_content', $content, $quiz_post ).'</div>';
		}
	}
	
	return $content;
}
add_shortcode( 'ld_quiz', 'learndash_quiz_shortcode' );

