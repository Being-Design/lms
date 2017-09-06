(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-specific JavaScript source
	 * should reside in this file.
	 *
	 * Note that this assume you're going to use jQuery, so it prepares
	 * the $ function reference to be used within the scope of this
	 * function.
	 *
	 * From here, you're able to define handlers for when the DOM is
	 * ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * Or when the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and so on.
	 *
	 * Remember that ideally, we should not attach any more than a single DOM-ready or window-load handler
	 * for any particular page. Though other scripts in WordPress core, other plugins, and other themes may
	 * be doing this, we should try to minimize doing that in our own work.
	 */
	$( document ).ready( function(){

		var new_course_id = 0;
		var og_course_id = 0;


		var og_group_id = 0;
		var new_group_id = 0;

		var curriculum_data = "";

		var curr_lesson_ind = 0;
		var next_lesson = 0;

		var curr_quiz_ind = 0;
		var next_quiz = 0;

		$( '.ldcc-clone-course' ).click( function( e ){
			e.preventDefault();
			$( '#ldcc-dialog' ).dialog({
  				modal: true,
  				closeOnEscape: false,
  				draggable: false,
  				resizable: false,
  				minWidth: 500,
  				minHeight: 400,
  				open: function(event, ui) { $(".ui-dialog-titlebar-close", ui.dialog | ui).hide(); }
			});
			var course_id = og_course_id = $( this ).data( 'course-id' );
			var course = $( this ).data( 'course' );
			
			var course_title = $(this).parents( 'td.title.column-title' ).find('strong a.row-title').text();

			$( '#ldcc-dialog .ldcc-success' ).before( "<div class='ldcc-course-progress'> <span>" + course_title + "</span> <img src='"+ldcc_js_data.image_base_url+"loader.gif' /></div>" );
			$.ajax({
				method: "POST",
				url: ldcc_js_data.adm_ajax_url,
				data: {
					course: course,
					course_id: course_id,
					action: "duplicate_course",
				},
			}).
			success( function( result ){
				var res = JSON.parse( result );
				if( res.success ){
					new_course_id = res.success.new_course_id;
					curriculum_data = res.success.c_data;
					$( '#ldcc-dialog .ldcc-course-progress img' ).attr( "src", ldcc_js_data.image_base_url + "tick.png" );
					$( '#ldcc-dialog' ).trigger( "course_post_created" );
				} else{
					$( '#ldcc-dialog .ldcc-success' ).before( "<div class='ldcc-error''>" + res.error + "</div>");
				}
			});
		});


		$( '.ldcc-clone-group' ).click( function( e ){
			e.preventDefault();
			$( '#ldcc-group-dialog' ).dialog({
  				modal: true,
  				closeOnEscape: false,
  				draggable: false,
  				resizable: false,
  				minWidth: 500,
  				minHeight: 400,
  				open: function(event, ui) { $(".ui-dialog-titlebar-close", ui.dialog | ui).hide(); }
			});
			var group_id = og_group_id = $( this ).data( 'group-id' );
			var group = $( this ).data( 'group' );

			var group_title = $(this).parents( 'td.title.column-title' ).find('strong a.row-title').text();

			$( '#ldcc-group-dialog .ldcc-success' ).before( "<div class='ldcc-course-progress'> <span>" + group_title + "</span> <img src='"+ldcc_js_data.image_base_url+"loader.gif' /></div>" );
			$.ajax({
				method: "POST",
				url: ldcc_js_data.adm_ajax_url,
				data: {
					group: group,
					group_id: group_id,
					action: "duplicate_group",
				},
			}).
			success( function( result ){
				var res = JSON.parse( result );
				if( res.success ){
					new_group_id = res.success.new_group_id;
					curriculum_data = res.success.c_data;
					$( '#ldcc-group-dialog .ldcc-course-progress img' ).attr( "src", ldcc_js_data.image_base_url + "tick.png" );
					$( '#ldcc-group-dialog' ).trigger( "group_clone_completed" );
				} else{
					$( '#ldcc-group-dialog .ldcc-success' ).before( "<div class='ldcc-error''>" + res.error + "</div>");
				}
			});
		});

		/*$( '.ldcc-clone-lesson' ).click( function( e ){ 
			e.preventDefault();
			$( '#ldcc-dialog' ).dialog({
  				modal: true,
  				closeOnEscape: false,
  				draggable: false,
  				resizable: false,
  				minWidth: 600,
  				minHeight: 400,
  				//open: function(event, ui) { $(".ui-dialog-titlebar-close", ui.dialog | ui).hide(); }
			});
			//return;
			var lesson_id = $( this ).data( 'lesson-id' );
			$.ajax({
				method: "POST",
				url: ldcc_js_data.adm_ajax_url,
				data: {
					lesson_id: lesson_id,
					action: "duplicate_lesson",
				},
			}).
			success( function( result ){
			});
		});*/

		/*$( '.ldcc-clone-quiz' ).click( function( e ){ 
			e.preventDefault();
			var quiz_id = $( this ).data( 'quiz-id' );
			var course_id = $( this ).data( 'course-id' );
			$.ajax({
				method: "POST",
				url: ldcc_js_data.adm_ajax_url,
				data: {
					quiz_id: quiz_id,
					course_id: course_id,
					action: "duplicate_quiz",
				},
			}).
			success( function( result ){
			});
		});*/

		$( '#ldcc-dialog' ).on( "course_post_created", function(){

			if( ! $.isEmptyObject( curriculum_data ) ) {
				if( curriculum_data.lesson.length || curriculum_data.quiz.length){
					curr_lesson_ind = 0;
					next_lesson = 0;
					if( curriculum_data.lesson.length ){
						next_lesson = curriculum_data.lesson[0][0];
					}
					curr_quiz_ind = 0;
					next_quiz = 0;
					if( curriculum_data.quiz.length ){
						next_quiz = curriculum_data.quiz[0][0];
					}
					if( next_lesson !== 0 ){
						$( '#ldcc-dialog' ).trigger( "create_lesson" );
					}
					if( next_quiz !== 0 ){
						$( '#ldcc-dialog' ).trigger( "create_quiz" );
					}
				} else {
					$( '#ldcc-dialog .ldcc-success' ).before( "<div> No content in Course. Course duplication complete. </div>" );
					$( '#ldcc-dialog' ).trigger( "course_clone_completed" );
				}
			} else {
				$( '#ldcc-dialog .ldcc-success' ).before( "<div> No content in Course. Course duplication complete. </div>" );
				$( '#ldcc-dialog' ).trigger( "course_clone_completed" );
			}

		});

		$( '#ldcc-dialog' ).on( "create_lesson", function(){
			if( curr_lesson_ind <= ( curriculum_data.lesson.length - 1 ) ) {
				$( '#ldcc-dialog .ldcc-success' ).before( "<div class='ldcc-lesson-"+curriculum_data.lesson[curr_lesson_ind][0]+"'> <span>" + curriculum_data.lesson[curr_lesson_ind][1] + "</span> <img src='"+ldcc_js_data.image_base_url+"loader.gif' /> </div>" );
				$.ajax({
					method: "POST",
					url: ldcc_js_data.adm_ajax_url,
					data: {
						lesson_id: curriculum_data.lesson[curr_lesson_ind][0],
						course_id: new_course_id,
						action: "duplicate_lesson",
					},
				}).
				success( function( result ){
					var res = JSON.parse( result );
					if( res.success ){
						$( '#ldcc-dialog .ldcc-lesson-'+curriculum_data.lesson[curr_lesson_ind][0]+' img' ).attr( "src", ldcc_js_data.image_base_url + "tick.png" );
						curr_lesson_ind += 1;
						$( '#ldcc-dialog' ).trigger( "create_lesson" );
					} else{
						$( '#ldcc-dialog .ldcc-success' ).before( "<div class='ldcc-error''>" + res.error + "</div>");
					}
				});
			} else {
				$( '#ldcc-dialog' ).trigger( "create_quiz" );
			}
		});
		
		$( '#ldcc-dialog' ).on( "create_quiz", function(){
			if( curr_quiz_ind <= ( curriculum_data.quiz.length - 1 ) ) {
				$( '#ldcc-dialog .ldcc-success' ).before( "<div class='ldcc-quiz-"+curriculum_data.quiz[curr_quiz_ind][0]+"'> <span>" + curriculum_data.quiz[curr_quiz_ind][1] + " </span> <img src='"+ldcc_js_data.image_base_url+"loader.gif' /> </div>" );
				$.ajax({
					method: "POST",
					url: ldcc_js_data.adm_ajax_url,
					data: {
						course_id: new_course_id,
						quiz_id: curriculum_data.quiz[curr_quiz_ind][0],
						action: "duplicate_quiz",
					},
				}).
				success( function( result ){
					var res = JSON.parse( result );
					if( res.success ){
						$( '#ldcc-dialog .ldcc-quiz-' + curriculum_data.quiz[curr_quiz_ind][0] + ' img' ).attr( "src", ldcc_js_data.image_base_url + "tick.png" );
						curr_quiz_ind += 1;
						$( '#ldcc-dialog' ).trigger( "create_quiz" );
					} else{
						$( '#ldcc-dialog .ldcc-success' ).before( "<div class='ldcc-error''>" + res.error + "</div>");
					}
				});
			} else {
				$( '#ldcc-dialog' ).trigger( "course_clone_completed" );
			}
		});

		$( '#ldcc-dialog' ).on( "course_clone_completed", function(){
			$( '#ldcc-dialog .ldcc-success .ldcc-course-link' ).attr( "href", ldcc_js_data.adm_post_url + "?action=edit&post=" + new_course_id );
			console.log( ldcc_js_data.adm_post_url + "?ldbr-select-course=" + new_course_id );
			console.log( ldcc_js_data.adm_ldbr_url + "&ldbr-select-course=" + new_course_id );
			$( '#ldcc-dialog .ldcc-success .ldcc-course-rename-link' ).attr( "href", ldcc_js_data.adm_ldbr_url + "&ldbr-select-course=" + new_course_id );
			$( '#ldcc-dialog .ldcc-success').show();
			$( '#ldcc-dialog .ldcc-notice' ).show();
		});

		$( '#ldcc-group-dialog' ).on( "group_clone_completed", function(){
			$( '#ldcc-group-dialog .ldcc-success .ldcc-group-link' ).attr( "href", ldcc_js_data.adm_post_url + "?action=edit&post=" + new_group_id );
			$( '#ldcc-group-dialog .ldcc-success').show();
			$( '#ldcc-group-dialog .ldcc-notice' ).show();
		});

	});

})( jQuery );
