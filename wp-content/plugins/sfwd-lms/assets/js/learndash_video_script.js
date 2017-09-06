/** LearnDash Lesson/Topic Video handler
 * Used when a Lesson or Topic contains an embed video ans allows 
 */
if ( typeof learndash_video_data !== 'undefined' ) {
	//console.log('learndash_video_data[%o]', learndash_video_data);

	var ld_video_count = 0;
	var ld_video_players = {};

	if ( learndash_video_data.videos_found_provider == 'youtube' ) {
		function onYouTubeIframeAPIReady() {
			jQuery('.ld-video iframe').each( function(index, element) {
				ld_video_count += 1;
					
				var element_id = jQuery(element).prop('id');
				if ( ( typeof element_id === 'undefined' ) || ( element_id == '' ) ) {
					jQuery(element).prop('id', 'ld-video-player-'+ld_video_count);
					element_id = 'ld-video-player-'+ld_video_count
				}
		
				if ( typeof element_id !== 'undefined' ) {
			
					LearnDash_disable_assets(true);
			
					ld_video_players[element_id] = new YT.Player( element_id, {
						events: {
							'onReady': LearnDash_YT_onPlayerReady,
							'onStateChange': LearnDash_YT_onPlayerStateChange
						}
					});
				}
			});
		}
		
		function LearnDash_YT_onPlayerReady(event) {
			if (learndash_video_data.videos_auto_start == true) {
				event.target.playVideo();
			}
		}

		function LearnDash_YT_onPlayerStateChange(event) {

			var player_state = event.target.getPlayerState();
			//console.log('player_state[%o]', player_state);

			if (player_state == YT.PlayerState.UNSTARTED ) { 
				//console.log('Video has not started'); 
				//jQuery('#player-status').html('Video has not started');
			} else if (player_state == YT.PlayerState.ENDED) { 
				//console.log('Video stopped'); 
				//jQuery('#player-status').html('Video has ended');

				// When the video ends we re-enable the form button
				LearnDash_disable_assets(false);
			} 
			//else if (player_state == YT.PlayerState.PLAYING) { 
				//console.log('Video is playing'); 
				//jQuery('#player-status').html('Video is playing');
			//} else if (player_state == YT.PlayerState.PAUSED) { 
				//console.log('Video is paused'); 
				//jQuery('#player-status').html('Video is paused');
			//} else if (player_state == YT.PlayerState.BUFFERING) { 
				//console.log('Video is buffering'); 
				//jQuery('#player-status').html('Video is buffering');
			//} else if (player_state == YT.PlayerState.CUED) { 
				//console.log('Video is queued'); 
				//jQuery('#player-status').html('Video is queued');
			//}
		}
	} else if ( learndash_video_data.videos_found_provider == 'vimeo' ) {
		jQuery( document ).ready(function() {
			//console.log('learndash_video_data[%o]', learndash_video_data.videos_found_provider);
			jQuery('.ld-video iframe').each( function(index, element) {
				ld_video_count += 1;
					
				var element_id = jQuery(element).prop('id');
				if ( ( typeof element_id === 'undefined' ) || ( element_id == '' ) ) {
					jQuery(element).prop('id', 'ld-video-player-'+ld_video_count);
					element_id = 'ld-video-player-'+ld_video_count;
				}
				
				if ( typeof element_id !== 'undefined' ) {
					//console.log('element[%o]', element);
		
					ld_video_players[element_id] = new Vimeo.Player(element);
					if ( typeof ld_video_players[element_id] !== 'undefined' ) {
						//console.log('player[%o]', ld_video_players[element_id]);

						ld_video_players[element_id].ready().then(function() {
						    console.log('ready  video!');
						
							LearnDash_disable_assets(true);
						
							if (learndash_video_data.videos_auto_start == true) {
								ld_video_players[element_id].play();
							}
						});

					    //ld_video_players[element_id].on('play', function(something) {
							//console.log('something[%o]', something);
					        //console.log('playing the video!');
							//jQuery('#player-status').html('Video is playing');
						//});

					    //ld_video_players[element_id].on('pause', function(something) {
						//	console.log('something[%o]', something);
					    //    console.log('paused the video!');
						//	//jQuery('#player-status').html('Video is paused');
					    //});

					    ld_video_players[element_id].on('ended', function(something) {
							//console.log('something[%o]', something);
					        //console.log('ended the video!');
							//jQuery('#player-status').html('Video has ended');
							LearnDash_disable_assets(false);
					    });

					    //ld_video_players[element_id].on('seeked', function( something ) {
						//	console.log('something[%o]', something);
					    //    console.log('seeked the video!');
							//jQuery('#player-status').html('Video has seeked');
						//});

	
					    //player.getVideoTitle().then(function(title) {
					    //    console.log('title:', title);
					    //});

					} //else {
						//console.log('player undefined');
						//}
				}
			});
		});
	} else if ( learndash_video_data.videos_found_provider == 'local' ) {
		jQuery( document ).ready(function() {
			//console.log('learndash_video_data[%o]', learndash_video_data.videos_found_provider);
			jQuery('.ld-video video').each( function(index, element) {
				ld_video_count += 1;
					
				var element_id = jQuery(element).prop('id');
				if ( ( typeof element_id === 'undefined' ) || ( element_id == '' ) ) {
					jQuery(element).prop('id', 'ld-video-player-'+ld_video_count);
					element_id = 'ld-video-player-'+ld_video_count;
				}
				
				if ( typeof element_id !== 'undefined' ) {
					//console.log('element[%o]', element);
				
					ld_video_players[element_id] = element;
					
					LearnDash_disable_assets(true);
					
					if (learndash_video_data.videos_auto_start == true) {
						ld_video_players[element_id].play();
					}
					
					ld_video_players[element_id].onended = function(e) {
						/*Do things here!*/
						//console.log('video ended');
						LearnDash_disable_assets( false );
					};
					
				}
			});
		});
	}
}

function LearnDash_disable_assets( status ) {
	if ( jQuery('form#sfwd-mark-complete input#learndash_mark_complete_button').length ) {
		jQuery('form#sfwd-mark-complete input#learndash_mark_complete_button').attr('disabled', status );

		// If we enabled the button 'status' is false and auto-complete is true then submit the form.
		if ( ( learndash_video_data.videos_auto_complete == true ) && ( status == false ) ) {
			
			setTimeout(function(){ 
				jQuery('form#sfwd-mark-complete').submit(); 
			}, 3000);
		} 
	}

	if (learndash_video_data.videos_shown == 'BEFORE' ) {
		if ( jQuery('#learndash_lesson_topics_list').length ) {
			if ( status == true ) {
				jQuery('#learndash_lesson_topics_list').hide();
				jQuery('#learndash_quizzes').hide();
				
			} else {
				jQuery('#learndash_lesson_topics_list').slideDown();
				jQuery('#learndash_quizzes').slideDown();
			}
		}
	}
}

