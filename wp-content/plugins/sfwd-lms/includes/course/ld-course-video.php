<?php

if (!class_exists('Learndash_Course_Video' ) ) {
	class Learndash_Course_Video {
	
		private $learndash_video_data = array(
			'videos_found_provider' => false, 
			'videos_found_type' => false,
			'videos_auto_start'	=> false,
			'videos_auto_complete' => true,
			'videos_shown' => false
		);
		
		function __construct() {
			add_action( 'wp_footer', array( $this, 'action_wp_footer' ), 1 );
		}
				
		function add_video_to_content( $content = '', $post, $settings = array() ) {
			
			if ( is_user_logged_in() ) {
				$user_id = get_current_user_id();
			} else {
				$user_id = 0;
			}
			
			// Do we show the video. In some cases we do. But in others like when the setting is to show AFTER completing other steps then we set to false.
			$show_video = false;
			
			// In the initial flow we do apply the video restiction logic. But then in other if the user is an admin or the student has completed the lesson
			// we don't apply the video logic. 
			$logic_video = false;
			
			if ( (isset( $settings['lesson_video_enabled'] ) ) && ( $settings['lesson_video_enabled'] == 'on' ) ) {
				if ( ( isset( $settings['lesson_video_url'] ) ) && ( !empty( $settings['lesson_video_url'] ) ) ) {
					
					$settings['lesson_video_url'] = html_entity_decode( $settings['lesson_video_url'] );
					
					$bypass_course_limits_admin_users = false;
					if ( !empty( $user_id ) ) {
						if ( learndash_is_admin_user( $user_id ) ) {
							$bypass_course_limits_admin_users = LearnDash_Settings_Section::get_section_setting('LearnDash_Settings _Section_General_Admin_User', 'bypass_course_limits_admin_users' );
							if ( $bypass_course_limits_admin_users == 'yes' ) $bypass_course_limits_admin_users = true;
							else $bypass_course_limits_admin_users = false;
							
						} else {
							$bypass_course_limits_admin_users = false;
						}
	
						// For logged in users to allow an override filter. 
						$bypass_course_limits_admin_users = apply_filters( 'learndash_prerequities_bypass', $bypass_course_limits_admin_users, $user_id, $post->ID, $post );
					}


					if ( !$bypass_course_limits_admin_users ) {

						if ( $post->post_type == 'sfwd-lessons' ) {
							$progress = learndash_get_course_progress( null, $post->ID );

							if ( $progress['this']->completed == true ) {
								// The student has completes this step so we show the video but don't apply the logic
								$show_video = true;
								$logic_video = false;
							} else {
								if ( $settings['lesson_video_shown'] == 'BEFORE' ) {
									$show_video = true;
									$logic_video = true;
									

									$topics = learndash_get_topic_list( $post->ID );
									if ( !empty( $topics ) ) {
										$progress = learndash_get_course_progress( null, $topics[0]->ID );
										if ( !empty( $progress ) ) {
											$topics_completed = 0;
											foreach ( $progress['posts'] as $topic ) {
												if ( $topic->completed == true ) {
													$topics_completed += 1;
													break;
												}
											}
											
											if ( !empty( $topics_completed ) ) {
												$logic_video = false;
											}
										} 
									}
								} else if ( $settings['lesson_video_shown'] == 'AFTER' ) {

									if ( learndash_lesson_topics_completed( $post->ID ) ) {
										$show_video = true;
										$logic_video = true;
									} else {
										$show_video =  false;
										$logic_video = false;
									}
								}
							}
						} else if ( $post->post_type == 'sfwd-topic' ) {
							// Lessons are always 'BEFORE'
							$settings['lesson_video_shown'] = 'AFTER';
							
							$progress = learndash_get_course_progress( null, $post->ID );

							if ( ! empty( $progress['this']->completed ) ) {
								$show_video = true;
								$logic_video = false;
							} else {
								// are we the first item in the list. No prev
								if ( ( empty( $progress['prev'] ) ) && ( $progress['this']->ID == $progress['posts'][0]->ID ) ) {
									$show_video = true;
									$logic_video = true;
									
									// Should not be here.
								} else if ( ( ! empty( $progress['prev'] ) ) && ( $progress['prev']->completed == true ) ) {
									$show_video = true;
									$logic_video = true;
								}
							}
						}
					} else {
						$progress = learndash_get_course_progress( null, $post->ID );

						if ( ! empty( $progress['this']->completed ) ) {
							//return str_replace( '[ld_video]', '', $content );
							$show_video = true;
							$logic_video = false;
						}
					}
					
					
					if ( $show_video == true ) {
					
						$this->learndash_video_data['videos_shown'] = $settings['lesson_video_shown'];

						if (( strpos( $settings['lesson_video_url'], 'youtu.be' ) !== false ) || ( strpos( $settings['lesson_video_url'], 'youtube.com' ) !== false )) {
							$this->learndash_video_data['videos_found_provider'] = 'youtube';
						
						} else if ( strpos( $settings['lesson_video_url'], 'vimeo.com' ) !== false ) {
							$this->learndash_video_data['videos_found_provider'] = 'vimeo';
						} else if ( strpos( $settings['lesson_video_url'], trailingslashit( get_home_url() ) ) !== false ) {
							$this->learndash_video_data['videos_found_provider'] = 'local';
						}

						if ( ( substr( $settings['lesson_video_url'], 0, strlen('http://') ) == 'http://'  ) || ( substr( $settings['lesson_video_url'], 0, strlen('https://') ) == 'https://' ) )  {
							if ( $this->learndash_video_data['videos_found_provider'] == 'local' ) {
								$this->learndash_video_data['videos_found_type'] = 'video_shortcode';
								$settings['lesson_video_url'] = '[video src="'. $settings['lesson_video_url'] .'"][/video]';
								
							} else if ( ( $this->learndash_video_data['videos_found_provider'] == 'youtube' ) || ( $this->learndash_video_data['videos_found_provider'] == 'vimeo' ) ) {
								$this->learndash_video_data['videos_found_type'] = 'embed_shortcode';
								$settings['lesson_video_url'] = '[embed]'. $settings['lesson_video_url'] .'[/embed]';
							}
							
						} else if ( substr( $settings['lesson_video_url'], 0, strlen('[embed')  ) == '[embed' ) {
							$this->learndash_video_data['videos_found_type'] = 'embed_shortcode';
						} else if ( substr( $settings['lesson_video_url'], 0, strlen('[video')  ) == '[video' ) {
							$this->learndash_video_data['videos_found_type'] = 'video_shortcode';
						} else if ( substr( $settings['lesson_video_url'], 0, strlen('<iframe')  ) == '<iframe' ) {
							$this->learndash_video_data['videos_found_type'] = 'iframe';
						}
					
						if ( ( $this->learndash_video_data['videos_found_provider'] !== false ) && ( $this->learndash_video_data['videos_found_type'] !== false ) ) {
							if ( $this->learndash_video_data['videos_found_provider'] == 'local' ) {
								if ( $this->learndash_video_data['videos_found_type'] == 'video_url' ) {
									//$video_content = wp_video_shortcode(
									//	apply_filters(
									//		'ld_video_shortcode_args', 
									//		array(
									//			'src' => $settings['lesson_video_url'],
									//		),
									//		$post->ID, $settings
									//	)
									//);
								} else if ( $this->learndash_video_data['videos_found_type'] == 'video_shortcode' ) {
									$video_content = do_shortcode( $settings['lesson_video_url'] );
								} else if ( $this->learndash_video_data['videos_found_type'] == 'iframe' ) {
									$video_content = $settings['lesson_video_url'];
								}
							} else if ( ( $this->learndash_video_data['videos_found_provider'] == 'youtube' ) || ( $this->learndash_video_data['videos_found_provider'] == 'vimeo' ) ) {
								//$video_content =  wp_oembed_get( $settings['lesson_video_url'], apply_filters( 'learndash_video_oembed_args', array(), $settings['lesson_video_url'],  $post->ID, $settings ) );
							
								if ( $this->learndash_video_data['videos_found_type'] == 'embed_shortcode' ) {
									global $wp_embed;
									$video_content = $wp_embed->run_shortcode( $settings['lesson_video_url'] );
								} else if ( $this->learndash_video_data['videos_found_type'] == 'video_shortcode' ) {
									$video_content = do_shortcode( $settings['lesson_video_url'] );
								} else if ( $this->learndash_video_data['videos_found_type'] == 'iframe' ) {
									$video_content = $settings['lesson_video_url'];
								}
							}
															
							if ( !empty( $video_content ) ) {
								//error_log('video_content['. $video_content .']');
								if ( $logic_video ) {
									
									preg_match('/<iframe.*src=\"(.*)\".*><\/iframe>/isU', $video_content, $matches );
									if ( ( is_array( $matches ) ) && ( isset( $matches[1] ) ) && ( !empty( $matches[1] ) ) ) {
				
										// Next we need to check if the video is YouTube, Vimeo, etc. so we check the matches[1]
										//if ( substr( $matches[1], 0, strlen( 'https://www.youtube.com/' ) ) == 'https://www.youtube.com/' ) {
										if ( $this->learndash_video_data['videos_found_provider'] == 'youtube' ) {
											$ld_video_params = apply_filters( 
												'ld_video_params', 
												array( 
													'controls' => '0',
													'modestbranding' => 1,
													'showinfo' => 0,
													'rel' => 0
												), 
												'youtube', $video_content, $post, $settings 
											);
					
											// Regardless of the filter we set this param because we need it!	
											$ld_video_params['enablejsapi'] = '1';
					
											$matches_1_new = add_query_arg( $ld_video_params, $matches[1] );
											$video_content = str_replace( $matches[1], $matches_1_new, $video_content );
				
											//$video_content = str_replace('<iframe ', '<iframe id="ld-video-player" ', $video_content );

										} else if ( $this->learndash_video_data['videos_found_provider'] == 'vimeo' ) {
											
											//$matches_1_new = add_query_arg('api', '1', $matches[1] );
											//$return = str_replace( $matches[1], $matches_1_new, $return );
				
											//$return = str_replace('<iframe ', '<iframe id="ld-video-player" ', $return );
											//$video_content = str_replace('<iframe ', '<iframe id="ld-video-player" ', $video_content );
										} else if ( $this->learndash_video_data['videos_found_provider'] == 'local' ) {
											
										}
									}
									
									$video_content = '<div class="ld-video" data-video="'. $this->learndash_video_data['videos_found_provider'] .'">'. $video_content .'</div>';
									
									if ( $this->learndash_video_data['videos_found_provider'] == 'local' ) {
										$video_content .="<style>.mejs-controls { display: none !important; visibility: hidden !important;}</style>";
									}
									
									
									if (( isset( $settings['lesson_video_auto_start'] ) ) && ( $settings['lesson_video_auto_start'] == 'on' ))
										$this->learndash_video_data['videos_auto_start'] = true;

									$this->learndash_video_data['videos_auto_complete'] = false;
									if (( isset( $settings['lesson_video_shown'] ) ) && ( $settings['lesson_video_shown'] == 'AFTER' )) {
										if (( isset( $settings['lesson_video_auto_complete'] ) ) && ( $settings['lesson_video_auto_complete'] == 'on' )) {
											$this->learndash_video_data['videos_auto_complete'] = true;
										}
									}
									
								} else {
									$this->learndash_video_data['videos_found_provider'] = false;
								}
							
								error_log('video_content['. $video_content .']');
								if ( strpos( $content, '[ld_video]' ) !== false ) {
									$content = str_replace( '[ld_video]', $video_content, $content );
								} else {
									$content .= $video_content;
								}
							}
						}
					} else {
						if ( strpos( $content, '[ld_video]' ) !== false ) {
							$content = str_replace( '[ld_video]', '', $content );
						}
					}
				}
			}
			
			return $content;
		}

		function action_wp_footer() {
			if ( $this->learndash_video_data['videos_found_provider'] !== false ) {

				wp_enqueue_script( 
					'learndash_video_script_js', 
					LEARNDASH_LMS_PLUGIN_URL . '/assets/js/learndash_video_script'. ( ( defined( 'LEARNDASH_SCRIPT_DEBUG' ) && ( LEARNDASH_SCRIPT_DEBUG === true ) ) ? '' : '.min') .'.js', 
					array( 'jquery' ), 
					LEARNDASH_VERSION,
					true 
				);
				$learndash_assets_loaded['scripts']['learndash_video_script_js'] = __FUNCTION__;	

				wp_localize_script( 'learndash_video_script_js', 'learndash_video_data', $this->learndash_video_data );

				if ( $this->learndash_video_data['videos_found_provider'] == 'youtube' ) {
					wp_enqueue_script( 'youtube_iframe_api', 'https://www.youtube.com/iframe_api', array( 'learndash_video_script_js' ), '1.0', true );
				} else if ( $this->learndash_video_data['videos_found_provider'] == 'vimeo' ) {
					wp_enqueue_script( 'vimeo_iframe_api', 'https://player.vimeo.com/api/player.js', array( 'learndash_video_script_js' ), null, true );
				}
			}
		}


	}
}