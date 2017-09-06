(function($) {
    
    /* Courses */
     var equalHeights = function(options) {
        var maxHeight = 0,
            $this = $('.course-flexible-area'),
            equalHeightsFn = function() {
                var height = $(this).innerHeight();
    
                if ( height > maxHeight ) { maxHeight = height; }
            };
        options = options || {};

        $this.each(equalHeightsFn);

        return $this.css('height', maxHeight);
    };
    
    // get viewport size        
    function viewport() {
        var e = window, a = 'inner';
        if (!('innerWidth' in window )) {
            a = 'client';
            e = document.documentElement || document.body;
        }
        return { width : e[ a+'Width' ] , height : e[ a+'Height' ] };
    }

    /**
     * -------------------------------------------------------------------------------------
     * Javascript-Equal-Height-Responsive-Rows
     * https://github.com/Sam152/Javascript-Equal-Height-Responsive-Rows
     * -------------------------------------------------------------------------------------
     */

    /**
     * Set all elements within the collection to have the same height.
     */
    $.fn.equalHeight = function() {
        var heights = [];
        $.each(this, function(i, element) {
            var $element = $(element);
            var elementHeight;
            // Should we include the elements padding in it's height?
            var includePadding = ($element.css('box-sizing') === 'border-box') || ($element.css('-moz-box-sizing') === 'border-box');
            if (includePadding) {
                elementHeight = $element.innerHeight();
            } else {
                elementHeight = $element.height();
            }
            heights.push(elementHeight);
        });
        this.css('height', Math.max.apply(window, heights) + 'px');
        return this;
    };

    /**
     * Create a grid of equal height elements.
     */
    $.fn.equalHeightGrid = function(columns) {
        var $tiles = this.filter(':visible');
        $tiles.css('height', 'auto');
        for (var i = 0; i < $tiles.length; i++) {
            if (i % columns === 0) {
                var row = $($tiles[i]);
                for (var n = 1; n < columns; n++) {
                    row = row.add($tiles[i + n]);
                }
                row.equalHeight();
            }
        }
        return this;
    };

    /**
     * Detect how many columns there are in a given layout.
     */
    $.fn.detectGridColumns = function() {
        var offset = 0,
            cols = 0,
            $tiles = this.filter(':visible');
        $tiles.each(function(i, elem) {
            var elemOffset = $(elem).offset().top;
            if (offset === 0 || elemOffset === offset) {
                cols++;
                offset = elemOffset;
            } else {
                return false;
            }
        });
        return cols;
    };

    /**
     * Ensure equal heights now, on ready, load and resize.
     */
    var grids_event_uid = 0;
    $.fn.responsiveEqualHeightGrid = function() {
        var _this = this;
        var event_namespace = '.grids_' + grids_event_uid;
        _this.data('grids-event-namespace', event_namespace);
        function syncHeights() {
            var cols = _this.detectGridColumns();
            _this.equalHeightGrid(cols);
        }
        $(window).bind('resize' + event_namespace + ' load' + event_namespace, syncHeights);
        syncHeights();
        grids_event_uid++;
        return this;
    };

    /**
     * Unbind created events for a set of elements.
     */
    $.fn.responsiveEqualHeightGridDestroy = function() {
        var _this = this;
        _this.css('height', 'auto');
        $(window).unbind(_this.data('grids-event-namespace'));
        return this;
    };

    function equalProjects() {

        if ($('.course-inner section.entry .caption').length) {
            var column_width = $('.course.sfwd-courses').width() / $('.course.sfwd-courses').parent().width() * 100;

            if ( column_width <= 50 && column_width > 33 ) {
                $('.course-inner section.entry .caption').equalHeightGrid(2);
            } else if ( column_width <= 33 && column_width > 25 ) {
                $('.course-inner section.entry .caption').equalHeightGrid(3);
            } else if ( column_width <= 25 && column_width > 20 ) {
                $('.course-inner section.entry .caption').equalHeightGrid(4);
            } else if ( column_width <= 20 && column_width > 0 ) {
                $('.course-inner section.entry .caption').equalHeightGrid(5);
            }
        }
    }
    
    $(document).ready(function(){
        
        //    imagesLoaded( '.course-flexible-area', function( instance ) {
        equalProjects();
        //    });

        /* throttle */
        $(window).resize(function(){
            clearTimeout($.data(this, 'resizeTimer'));
            $.data(this, 'resizeTimer', setTimeout(function() {
                equalProjects();
            }, 1000));
        });

        $('#left-menu-toggle').click(function(){
            setTimeout(function() {
                equalProjects();
            }, 550);
        });

        $(window).trigger('resize');
        
        
        var video_frame = $("#course-video").find('iframe'),
        video_src = video_frame.attr('src');

        $('#show-video').click(function(e){
            e.preventDefault();
            $('.course-header').fadeOut(200, 
            function(){
                $("#course-video").fadeIn(200);
            });
            $(this).addClass('hide');
        });

        $('#hide-video').click(function(e){
            e.preventDefault();
            $('#course-video').fadeOut(200, 
            function(){
                video_frame.attr('src','');
                $(".course-header").fadeIn(200, function() {
                    video_frame.attr('src',video_src);
                });
            });
            $('#show-video').removeClass('hide');
        });

        //Ajax for contact teacher widget
        $( '.boss-edu-send-message-widget' ).on( 'click', function ( e ) {

            e.preventDefault();

            $.post( ajaxurl, {
                    action: 'boss_edu_contact_teacher_ajax',
                    content: $('.boss-edu-teacher-message').val(),
                    sender_id: $('.boss-edu-msg-sender-id').val(),
                    reciever_id: $('.boss-edu-msg-receiver-id').val(),
                    course_id: $('.boss-edu-msg-course-id').val()
                },
                function(response) {

                    if ( response.length > 0 && response != 'Failed' ) {
                        $('.widget_course_teacher h3').append('<div class="learndash-message tick">Your private message has been sent.</div>');
                    }
                });


        } );
    });
    
    /* Course Progress */
//    $.fn.removeComplete = function(){
//        var text = $(this).text(),
//            lastIndex = text.lastIndexOf(" ");
//        $(this).text(text.substring(0, lastIndex));
//    }
//    $(document).ready(function(){
//        $('.course_progress_blue').each(function(){
//            var $this = $(this),
//                style = $this.attr('style');
//            $this.parents('.course_progress').next('.right').attr('style', style).removeComplete();
//        });
//    });
//    
//    
//    $('#learndash_profile').find('.expand_collapse').insertBefore('#course_list');
    
    /* Quiz */
    $('.wpProQuiz_questionInput[type=radio], .wpProQuiz_questionInput[type=checkbox]').each(function(){
        var $this = $(this);
        if($this.attr('checked') == true) {
            $this.parents('label').addClass('selected');
        } else {
            $this.parents('label').removeClass('selected');
        }
    }); 
    
    $('.wpProQuiz_questionInput').change(function(){
        if($(this).attr('type') == 'radio') {
            $(this).parents('.wpProQuiz_questionList').find('.wpProQuiz_questionListItem').each(function(){
                $(this).find('label').removeClass('selected');
            });
            $(this).parent('label').addClass('selected');
        } else if($(this).attr('type') == 'checkbox') {
            $(this).parent('label').toggleClass('selected');
        }
    });
    
    $('.drop-list').click(function(){
        var $parent = $(this).parents('.has-topics');
        $parent.find('.learndash_topic_dots').slideToggle();
        $parent.toggleClass('expanded');
    });

    /* Course Participants Widget View All */
    $('.learndash-view-all-participants a').click( function( event ) {
        event.preventDefault();
        var el = $(this);
        var hiddenLearners = el.closest('.widget_learndash_course_participants').find('.learndash-course-participant.hide');
        var txt = hiddenLearners.is(':visible') ? 'View All' : 'Close';
        $(this).text(txt);
        hiddenLearners.slideToggle( 300 );
    });

})(jQuery)