<?php
/**
 * @package WordPress
 * @subpackage Boss for LearnDash
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'Boss_Learndash_Plugin' ) ):
/**
 *
 * Boss for LearnDash Plugin Main Controller
 * **************************************
 *
 *
 */
class Boss_Learndash_Plugin
{
    /* Version
     * ===================================================================
     */

    /**
     * Plugin codebase version
     * @var string
     */
    public $version = '1.0.0';

    /* Paths
     * ===================================================================
     */

    public $plugin_dir = '';
    public $plugin_url = '';
    public $lang_dir = '';
    public $templates_dir = '';
    public $templates_url = '';
    public $assets_dir = '';
    public $assets_url = '';
    private $data;

	/* Singleton
	 * ===================================================================
	 */

	/**
	 * Main Boss for LearnDash Instance.
	 *
	 * Boss for LearnDash is great
	 * Please load it only one time
	 * For this, we thank you
	 *
	 * Insures that only one instance of Boss for LearnDash exists in memory at any
	 * one time. Also prevents needing to define globals all over the place.
	 *
	 * @since Boss for LearnDash (1.0.0)
	 *
	 * @static object $instance
	 * @uses Boss_Learndash_Plugin::setup_globals() Setup the globals needed.
	 * @uses Boss_Learndash_Plugin::setup_actions() Setup the hooks and actions.
	 * @uses Boss_Learndash_Plugin::setup_textdomain() Setup the plugin's language file.
	 * @see buddyboss_education()
	 *
	 * @return Boss for LearnDash The one true BuddyBoss.
	 */
	public static function instance()
	{
		// Store the instance locally to avoid private static replication
		static $instance = null;

		// Only run these methods if they haven't been run previously
		if ( null === $instance )
		{
			$instance = new Boss_Learndash_Plugin;
			$instance->setup_globals();
			$instance->setup_actions();
			$instance->setup_textdomain();
		}

		// Always return the instance
		return $instance;
	}

	/* Magic Methods
	 * ===================================================================
	 */

	/**
	 * A dummy constructor to prevent Boss for LearnDash from being loaded more than once.
	 *
	 * @since Boss for LearnDash (1.0.0)
	 * @see Boss_Learndash_Plugin::instance()
	 * @see buddypress()
	 */
	private function __construct() { /* Do nothing here */ }

	/**
	 * A dummy magic method to prevent Boss for LearnDash from being cloned.
	 *
	 * @since Boss for LearnDash (1.0.0)
	 */
	public function __clone() { _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'boss-learndash' ), '1.0.0' ); }

	/**
	 * A dummy magic method to prevent Boss for LearnDash from being unserialized.
	 *
	 * @since Boss for LearnDash (1.0.0)
	 */
	public function __wakeup() { _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'boss-learndash' ), '1.0.0' ); }

	/**
	 * Magic method for checking the existence of a certain custom field.
	 *
	 * @since Boss for LearnDash (1.0.0)
	 */
	public function __isset( $key ) { return isset( $this->data[$key] ); }

	/**
	 * Magic method for getting Boss for LearnDash varibles.
	 *
	 * @since Boss for LearnDash (1.0.0)
	 */
	public function __get( $key ) { return isset( $this->data[$key] ) ? $this->data[$key] : null; }

	/**
	 * Magic method for setting Boss for LearnDash varibles.
	 *
	 * @since Boss for LearnDash (1.0.0)
	 */
	public function __set( $key, $value ) { $this->data[$key] = $value; }

	/**
	 * Magic method for unsetting Boss for LearnDash variables.
	 *
	 * @since Boss for LearnDash (1.0.0)
	 */
	public function __unset( $key ) { if ( isset( $this->data[$key] ) ) unset( $this->data[$key] ); }

	/**
	 * Magic method to prevent notices and errors from invalid method calls.
	 *
	 * @since Boss for LearnDash (1.0.0)
	 */
	public function __call( $name = '', $args = array() ) { unset( $name, $args ); return null; }


	/* Plugin Specific, Setup Globals, Actions, Includes
	 * ===================================================================
	 */
    
    /**
     * Setup Boss for LearnDash plugin global variables.
     *
     * @since 1.0.0
     * @access private
     *
     * @uses plugin_dir_path() To generate Boss for LearnDash plugin path.
     * @uses plugin_dir_url() To generate Boss for LearnDash plugin url.
     * @uses apply_filters() Calls various filters.
     */
    private function setup_globals() {
        
        /** Versions ************************************************* */
        $this->version = BOSS_LEARNDASH_PLUGIN_VERSION;

        /** Paths***************************************************** */
        // Boss for LearnDash root directory
        $this->file          = BOSS_LEARNDASH_PLUGIN_FILE;
        $this->basename      = plugin_basename( $this->file );
        $this->plugin_dir    = BOSS_LEARNDASH_PLUGIN_DIR;
        $this->plugin_url    = BOSS_LEARNDASH_PLUGIN_URL;

        // Languages
        $this->lang_dir      = dirname( $this->basename ) . '/languages/';

        // Includes
        $this->includes_dir = $this->plugin_dir . 'includes';
        $this->includes_url = $this->plugin_url . 'includes';

        // Templates
		$this->templates_dir = $this->plugin_dir . 'templates';
		$this->templates_url = $this->plugin_url . 'templates';
        
        // Assets
        $this->assets_dir = $this->plugin_dir . 'assets';
        $this->assets_url = $this->plugin_url . 'assets';
    }

    /**
	 * Set up the default hooks and actions.
	 *
	 * @since Boss for LearnDash (1.0.0)
	 * @access private
	 *
	 * @uses add_action() To add various actions.
	 * @uses add_fileter() To add various filters.
	 */
    private function setup_actions() {

        if ( class_exists( 'SFWD_LMS' ) ) {

            if(!function_exists('learndash_course_grid_admin')) {
                add_action( 'admin_enqueue_scripts', array($this, 'boss_edu_learndash_course_grid_admin'), 0);
            }

            if ( ! is_admin() && ! is_network_admin() )
            {
                // Css and Js
                add_action( 'wp_enqueue_scripts', array( $this, 'boss_edu_enqueue_scripts' ), 11 );
                add_action( 'wp_footer', array( $this, 'boss_edu_deregister_theme_css_learndash' ), 11 );
                add_filter( 'the_content', array( $this, 'boss_edu_deregister_theme_css_learndash') );
                add_filter( 'widget_text', array( $this, 'boss_edu_deregister_theme_css_learndash') );
                add_action( 'wp_enqueue_scripts', array( $this, 'boss_edu_deregister_theme_css_learndash'), 100 );
            }

            // Widgets
            add_action( 'widgets_init', array( $this, 'boss_edu_register_widgets' ) );

            // Image size for course archive
            add_image_size( 'course-archive-thumb', 360, 250, true );
            add_image_size( 'course-single-thumb', 472, 355, true );

            // Filter templates path
            add_filter('learndash_template', array( $this, 'boss_edu_learndash_templates'), 10, 5);

            // Register Sidebar
            add_action( 'widgets_init', array( $this, 'boss_edu_sidebar'), 11 );

            //Learndash & Wpcourseware
            add_filter('single_template', array( $this, 'boss_edu_load_singles_templates'));
            //add_filter('template_include', array( $this, 'boss_edu_load_archive_templates'));
            add_filter('boss_edu_course_author_meta', array( $this, 'boss_edu_post_author_meta'));

            // Wrap course list
            add_filter("ld_course_list", array( $this, 'boss_edu_ld_course_list'), 10, 3);

            // Excerpt Course support
            add_post_type_support( 'sfwd-courses', 'excerpt' );

            // Add Course video box
            add_action( 'add_meta_boxes', array( $this, 'boss_edu_course_add_meta_box') );
            add_action( 'save_post', array( $this, 'boss_edu_course_save_meta_box_data') );

            //Register new group extension
            add_action( 'bp_init', array( $this, 'boss_edu_overide_courses_html'),5 );

            // Profile courses title
            add_action( 'bp_learndash_before_courses_page_content', array($this, 'boss_edu_courses_page_title') );

            // Filter steps text
            add_filter( 'badgeos_steps_heading', array($this, 'boss_edu_change_steps_text'), 10, 2 );

            add_filter( 'learndash_post_args', array ( $this, 'boss_learndash_post_args' ), 10, 1 );

            global $bp;
            if($bp) {
                add_action( 'bp_init', array($this, 'boss_edu_buddypress_learndash') );
                
                // Disable group template of Boss theme
                add_action( 'bp_init', array($this, 'boss_edu_disable_theme_template') );
            }
            
            // BadgeOs
            if( class_exists('BadgeOS') && $GLOBALS['badgeos']) {
                add_filter( 'badgeos_render_achievement', array( $this, 'boss_edu_badgeos_render_achievement'), 10, 2 );
                remove_filter( 'the_content', 'badgeos_reformat_entries', 9 );
                add_filter( 'the_content', array( $this, 'boss_edu_badgeos_reformat_entries'), 9 );
            }
            
            // activity
		    add_filter( 'bp_get_activity_action',array($this, 'boss_edu_filter_course_activity') );
			
			add_filter( 'learndash_flush_rewrite_rules', '__return_false' );

            add_action( 'wp_ajax_boss_edu_contact_teacher_ajax', array( $this, 'boss_edu_contact_teacher_ajax' ) );
            add_action( 'wp_ajax_nopriv_boss_edu_contact_teacher_ajax', array( $this, 'boss_edu_contact_teacher_ajax' ) );
        }
        
        add_action( 'wp_head', array( $this, 'boss_edu_course_group_canonical_link' ), 10 );

        // Fix BuddyPress for Learndash > My Course list header
        remove_shortcode("ld_topic_list");
        add_shortcode("ld_topic_list", array($this,'boss_edu_topics_shortcode') );
        
        //for free courses, there is no way to track course-participants.
        //lets do it! yes!!
        add_action( 'learndash_topic_completed',    array( $this, 'add_user_to_participants_list' ), 10 );
        add_action( 'learndash_lesson_completed',   array( $this, 'add_user_to_participants_list' ), 10 );
        add_action( 'learndash_course_completed',   array( $this, 'add_user_to_participants_list' ), 10 );
    }
    
    /**
     * Topics scpecific shortcode
     *
     */     
    public function boss_edu_learndash_course_grid_admin() {
        global $pagenow, $post;

        if($pagenow == "post.php" && $post->post_type == "sfwd-courses" || $pagenow == "post-new.php" && @$_GET["post_type"] == "sfwd-courses")
        wp_enqueue_script( 'learndash_admin_js', $this->assets_url . '/js/learndash-admin.js', array('jquery') );
    } 
    
    /**
     * Topics scpecific shortcode
     *
     */ 
    public function boss_edu_topics_shortcode($attr) {
        $attr['post_type'] = 'sfwd-topic';
        $attr['mycourses'] = false;
        return $this->boss_edu_ld_topic_list($attr);
    }
    
    public function boss_edu_ld_topic_list($attr) {

         $shortcode_atts = shortcode_atts ( array(
                'num' => '-1',
                'post_type' => 'sfwd-courses',
                'post_status' => 'publish',
                'order' => 'DESC',
                'orderby' => 'ID',
                'mycourses' => false,
                'meta_key' => '',
                'meta_value'	=> '',
                'meta_compare'	=> '',
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
                'categoryselector' => '',
                'author__in' => '',
                'col'	=> '',
                'array'	=> false,
                ), $attr);
        extract($shortcode_atts);
        global $post;

        $filter = array( 'post_type' => $post_type, 'post_status' => $post_status,  'posts_per_page' => $num, 'order' => $order , 'orderby' => $orderby );

        if(!empty($author__in))
        $filter['author__in'] = $author__in;

        if(!empty($tag))
        $filter['tag'] = $tag;

        if(!empty($tag_id))
        $filter['tag_id'] = $tag;

        if(!empty($meta_key))
        $filter['meta_key'] = $meta_key;

        if(!empty($meta_value))
        $filter['meta_value'] = $meta_value;

        if(!empty($meta_compare))
        $filter['meta_compare'] = $meta_compare;

        if(!empty($tag__and))
        $filter['tag__and'] = explode(",", $tag__and);

        if(!empty($tag__in))
        $filter['tag__in'] = explode(",", $tag__in);

        if(!empty($tag__not_in))
        $filter['tag__not_in'] = explode(",", $tag__not_in);

        if(!empty($tag_slug__and))
        $filter['tag_slug__and'] = explode(",", $tag_slug__and);

        if(!empty($tag_slug__in))
        $filter['tag_slug__in'] = explode(",", $tag_slug__in);

        if(!empty($cat))
        $filter['cat'] = $cat;

        if(!empty($cat))
        $filter['cat'] = $cat;

        if(!empty($category_name))
        $filter['category_name'] =  $category_name;

        if(!empty($category__and))
        $filter['category__and'] = explode(",", $category__and);

        if(!empty($category__in))
        $filter['category__in'] = explode(",", $category__in);

        if(!empty($category__not_in))
        $filter['category__not_in'] = explode(",", $category__not_in);

        if($array)
            return get_posts($filter);

        if(@$post->post_type == $post_type)
            $filter["post__not_in"] = array($post->ID);

        $loop = new WP_Query( $filter );

        $topics = array(); 
        while ( $loop->have_posts() ) : $loop->the_post();
            if(!$mycourses  || sfwd_lms_has_access(get_the_ID()))
            $topics[] = $post;
        endwhile; 

        if($mycourses){
            $topics_progress = learndash_get_course_progress(null, $topics[0]->ID);

            if(!empty($topics_progress['posts'][0]))
                $topics = $topics_progress['posts'];    
        }    

        $level = ob_get_level();    

        ob_start();	

        if(!empty($topics)) {
            ?>
            <div class="learndash">
                <div id="learndash_topic_dots-<?php echo $lesson["post"]->ID; ?>" class="learndash_topic_dots type-list">
                    <ul>
                        <?php
                        $odd_class = "";
                        foreach ($topics as $key => $topic) { 
                            $odd_class = empty($odd_class)? "nth-of-type-odd":"";
                            $completed_class = empty($topic->completed)? "topic-notcompleted":"topic-completed";
                            ?>
                            <li class="<?php echo $odd_class; ?>">
                                <span class="topic_item">
                                    <a class="<?php echo $completed_class; ?>" href="<?php echo get_permalink($topic->ID); ?>" title="<?php echo $topic->post_title; ?>">
                                        <?php echo $topic->post_title; ?>
                                    </a>
                                </span>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
            <?php }
        $output = learndash_ob_get_clean($level);
        wp_reset_query(); 

        return apply_filters("boss_edu_ld_topic_list", $output, $shortcode_atts, $filter);
    }
    
     /**
     * Adds canonical link tag at header, SEO friendly
     *
     */       
    public function boss_edu_course_group_canonical_link(){

        if( function_exists( 'bp_is_active' ) && bp_is_active( 'groups' ) && bp_is_group()) {
            $group = groups_get_current_group();
            $group_status = groups_get_groupmeta( $group->id , 'bp_course_attached', true );
            if($group_status) {
                remove_action( 'bp_head', 'bp_rel_canonical');
                echo '<link rel="canonical" href="'.get_permalink($group_status).'">';
            }
        }
    }
    
    /**
     * Filter steps text
     *
     */
    public function boss_edu_change_steps_text($text, $steps){
        $count = count($steps);
        $post_type_object = get_post_type_object( $steps[0]->post_type );
        
        return sprintf( _n( '%1$d Required '.$post_type_object->labels->singular_name, '%1$d Required '.$post_type_object->labels->name, $count, 'boss-learndash' ), $count );
    }


    /**
     * Add hide price tag field in course setting metabox
     * @param $post_args
     * @return mixed
     */
    public function boss_learndash_post_args( $post_args ) {

        foreach ( $post_args as  $key => $arg ) {

            //Add Hide Price Tag in course edit metabox
            if ( 'sfwd-courses' === $arg['post_type'] ) {

                $post_args[$key]['fields']['boss_hide_price_tag'] =  array(
                    'name' => __( 'Hide Price Tag', 'boss-learndash' ),
                    'type' => 'checkbox',
                    'help_text' => __( 'Hide course price tag from all courses page.', 'boss-learndash' ),
                    'default' => 0,
                );
            }
        }

        return $post_args;
    }
    
    /**
     * Wrap course list
     *
     */
    public function boss_edu_courses_page_title() {

        echo '<div class="learndash_profile_heading"><span class="title">'. sprintf( __( 'My %s', 'boss-learndash' ), LearnDash_Custom_Label::get_label( 'courses' ) ) .'</span><span class="ld_profile_status">'.__('Status', 'boss-learndash').'</span></div>';
    }
    
    /**
     * Wrap course list
     *
     */
    public function boss_edu_ld_course_list($output, $shortcode_atts, $filter) {

        //Course Grid Columns Param
        if ( ! empty( $shortcode_atts['col'] ) ) {
            ob_start(); ?>
            <style type="text/css">
                @media screen and (min-width: 901px) {
                    .course.sfwd-courses {
                        width: <?php echo 100/(int)$shortcode_atts['col'] ?>% !important;
                        max-width: initial !important;
                    }
                }
            </style>
            <?php
            $output .= ob_get_contents();
            ob_end_clean();
        }

        return '<div id="course-list-wrap">'.$output.'</div>';
    }

    /**
     * Filter badge content to add our removed content back
     *
     * @since  1.0.0
     * @param  string $content The page content
     * @return string          The page content after reformat
     */
    public function boss_edu_badgeos_reformat_entries( $content ) {

        wp_enqueue_style( 'badgeos-front' );

        $badge_id = get_the_ID();

        // filter, but only on the main loop!
        if ( !badgeos_is_main_loop( $badge_id ) )
            return wpautop( $content );

        // now that we're where we want to be, tell the filters to stop removing
        $GLOBALS['badgeos_reformat_content'] = true;

        // do badge title markup
        // $title = '<h1 class="badge-title">'. get_the_title() .'</h1>';

        // check if user has earned this Achievement, and add an 'earned' class
        $class = badgeos_get_user_achievements( array( 'achievement_id' => absint( $badge_id ) ) ) ? ' earned' : '';

        // wrap our content, add the thumbnail and title and add wpautop back
        $newcontent = '<div class="achievement-wrap'. $class .'">';

        // Check if current user has earned this achievement
        $newcontent .= badgeos_render_earned_achievement_text( $badge_id, get_current_user_id() );

        $newcontent .= wpautop( $content );

        // Include output for our steps
        $newcontent .= badgeos_get_required_achievements_for_achievement_list( $badge_id );

        // Include achievement earners, if this achievement supports it
        if ( $show_earners = get_post_meta( $badge_id, '_badgeos_show_earners', true ) )
            $newcontent .= $this->boss_edu_badgeos_get_achievement_earners_list( $badge_id );

        $newcontent .= '</div><!-- .achievement-wrap -->';

        // Ok, we're done reformating
        $GLOBALS['badgeos_reformat_content'] = false;

        return $newcontent;
    }
    
    public function boss_edu_badgeos_get_achievement_earners_list( $achievement_id = 0 ) {

        // Grab our users
        $earners = badgeos_get_achievement_earners( $achievement_id );
        $output = '';

        // Only generate output if we have earners
        if ( ! empty( $earners ) )  {
            // Loop through each user and build our output
            $output .= '<h4>' . apply_filters( 'badgeos_earners_heading', __( 'People who have earned this:', 'boss-learndash'  ) ) . '</h4>';
            $output .= '<ul class="badgeos-achievement-earners-list achievement-' . $achievement_id . '-earners-list">';
            foreach ( $earners as $user ) {
                $user_url = apply_filters( 'boss_edu_badgeos_achievement_earner_link', get_author_posts_url( $user->ID ), $user->ID, $user );
                $output .= '<li><a href="' . esc_url( $user_url ) . '">' . get_avatar( $user->ID, 75 ) . '</a></li>';
            }
            $output .= '</ul>';
        }

        // Return our concatenated output
        return $output;
    }
    
    /**
    * Change 
    */
    public function boss_edu_badgeos_render_achievement($output, $achievement) {
        global $user_ID;
        
        // If we were given an ID, get the post
        if ( is_numeric( $achievement ) )
            $achievement = get_post( $achievement );

        // make sure our JS and CSS is enqueued
        wp_enqueue_script( 'badgeos-achievements' );
        wp_enqueue_style( 'badgeos-widget' );

        // check if user has earned this Achievement, and add an 'earned' class
        $earned_status = badgeos_get_user_achievements( array( 'user_id' => $user_ID, 'achievement_id' => absint( $achievement->ID ) ) ) ? 'user-has-earned' : 'user-has-not-earned';

        // Setup our credly classes
        $credly_class = '';
        $credly_ID = '';

        // If the achievement is earned and givable, override our credly classes
        if ( 'user-has-earned' == $earned_status && $giveable = credly_is_achievement_giveable( $achievement->ID, $user_ID ) ) {
            $credly_class = ' share-credly addCredly';
            $credly_ID = 'data-credlyid="'. absint( $achievement->ID ) .'"';
        }

        // Each Achievement
        $output = '';
        
        $output .= '<div id="badgeos-achievements-list-item-' . $achievement->ID . '" class="badgeos-achievements-list-item '. $earned_status . $credly_class .'"'. $credly_ID .'>';

            // Achievement Image
            $output .= '<div class="badgeos-item-image">';
            $output .= '<a href="' . get_permalink( $achievement->ID ) . '">' . badgeos_get_achievement_post_thumbnail( $achievement->ID ) . '</a>';
            $output .= '</div><!-- .badgeos-item-image -->';

            // Achievement Content
            $output .= '<div class="badgeos-item-description">';

                $output .= badgeos_achievement_points_markup( $achievement->ID );
        
                // Achievement Title
                $output .= '<h2 class="badgeos-item-title"><a href="' . get_permalink( $achievement->ID ) . '">' . get_the_title( $achievement->ID ) .'</a></h2>';

                // Achievement Short Description
                $output .= '<div class="badgeos-item-excerpt">';
                $excerpt = !empty( $achievement->post_excerpt ) ? $achievement->post_excerpt : $achievement->post_content;
                $output .= wpautop( apply_filters( 'get_the_excerpt', $excerpt ) );
                $output .= '</div><!-- .badgeos-item-excerpt -->';

                // Render our Steps
                if ( $steps = badgeos_get_required_achievements_for_achievement( $achievement->ID ) ) {
                    $output.='<div class="badgeos-item-attached">';
                        $output.='<div id="show-more-'.$achievement->ID.'" class="badgeos-open-close-switch"><a class="show-hide-open" data-badgeid="'. $achievement->ID .'" data-action="open" href="#">' . __( 'Show Details', 'boss-learndash'  ) . '</a></div>';
                        $output.='<div id="badgeos_toggle_more_window_'.$achievement->ID.'" class="badgeos-extras-window">'. badgeos_get_required_achievements_for_achievement_list_markup( $steps, $achievement->ID ) .'</div><!-- .badgeos-extras-window -->';
                    $output.= '</div><!-- .badgeos-item-attached -->';
                }

            $output .= '</div><!-- .badgeos-item-description -->';

        $output .= '</div><!-- .badgeos-achievements-list-item -->';
        return $output;
    }
    
        
    /**
    * Activity markup
    */
	public function boss_edu_filter_course_activity( $action ) {

        global $activities_template;

        $curr_id = isset( $activities_template->current_activity ) ? $activities_template->current_activity : '';
        $act_id = isset( $activities_template->activities[ $curr_id ]->id ) ? ( int ) $activities_template->activities[ $curr_id ]->id : '';
        $user_id = isset( $activities_template->activities[ $curr_id ]->user_id ) ? ( int ) $activities_template->activities[ $curr_id ]->user_id : '';

        // Check for activity ID in $_POST if this is a single
        // activity request from a [read more] action
        if ( $act_id === 0 && ! empty( $_POST[ 'activity_id' ] ) ) {
            $activity_array = bp_activity_get_specific( array(
                'activity_ids' => $_POST[ 'activity_id' ],
                'display_comments' => 'stream'
            ) );

            $activity = ! empty( $activity_array[ 'activities' ][ 0 ] ) ? $activity_array[ 'activities' ][ 0 ] : false;
            $act_id = ( int ) $activity->id;
        }

        // This should never happen, but if it does, bail.
        if ( $act_id === 0 ) {
            return $action;
        }

        $is_course_act = bp_activity_get_meta( $act_id, 'bp_learndash_group_activity_markup', true );

        //Check for action 
        if ( $is_course_act ) {
            $course_attached = bp_activity_get_meta($act_id,'bp_learndash_group_activity_markup_courseid',true );
            $post = get_post($course_attached);
            if ( strpos( $action, 'started taking the course' ) != false ) {
                $html = '<div class="bp-learndash-activity table course-activity">';
                    $html .= '<div class="table-cell edu-activity-image">';
                        $html .= '<p class="edu-activity-type">' .LearnDash_Custom_Label::get_label( 'course' ) . '</p>';
                        if ( has_post_thumbnail( $post->ID ) ) {
                            // Get Featured Image
                            $html .= get_the_post_thumbnail( $post->ID, 'course-archive-thumb', array( 'class' => 'woo-image thumbnail alignleft') );
                        } else {
                            $html .= '<img src="http://placehold.it/360x250&text='. LearnDash_Custom_Label::get_label( 'course' ) .'">';
                        }
                    $html .= '</div>';
                    $html .= '<div class="table-cell edu-activity-content">';
                
                        $status_class = 'fa-spinner';
                
                        $html .= '<h4><span>' . $post->post_title . '<i  class="fa '.$status_class.'"></i></span></h4>';

                        $author_url = bp_core_get_user_domain( $post->post_author );
                        $author = '<a href="' . $author_url . '">' . bp_core_get_user_displayname( $post->post_author ) . '</a>';  
                        $category_output = get_the_category_list( ', ', '', $post->ID );
                        $html .= '<div class="edu-activity-meta">';
                            $html .= '<span>' . __('by ', 'boss-learndash') . $author . '</span>';
                            if ( 'Uncategorized' != $category_output ) {
                                $html .= '<span class="course-category">' . sprintf( __( 'in %s', 'boss-learndash' ), $category_output ) . '</span>';
                            }
                        $html .= '</div>';
                        if($post->post_excerpt) {
                            $html .= '<p class="edu-activity-excerpt">' . $post->post_excerpt . '</p>';
                        }
                    $html .= '</div>';
                $html .= '</div>';

                $action .= $html;

            } elseif( strpos( $action, 'has passed the' ) != false ) {
                $html = '<div class="bp-learndash-activity table quiz-activity">';
                    $html .= '<div class="table-cell edu-activity-image">';
                        $html .= '<img src="' . $this->assets_url . '/images/quiz.png">';
                    $html .= '</div>';
                    $status_class = 'fa-check-circle';
                    $html .= '<div class="table-cell edu-activity-content">';
                        $html .= '<h4><span>' . $post->post_title . '<i  class="fa '.$status_class.'"></i></span></h4>';

                        $author_url = bp_core_get_user_domain( $post->post_author );
                        $author = '<a href="' . $author_url . '">' . bp_core_get_user_displayname( $post->post_author ) . '</a>';  
                        $html .= '<div class="edu-activity-meta">';
                            $html .= '<span>' . __('by ', 'boss-learndash') . $author . '</span>';
//                            if ( 'Uncategorized' != $category_output ) {
//                                $html .= '<span class="course-category">' . sprintf( __( 'in %s', 'boss-learndash' ), $category_output ) . '</span>';
//                            }
                        $html .= '</div>';
                        if($post->post_excerpt) {
                            $html .= '<p class="edu-activity-excerpt">' . $post->post_excerpt . '</p>';
                        }
                    $html .= '</div>';
                $html .= '</div>';
                $action .= $html;
            } elseif( strpos( $action, 'completed the lesson' ) != false ) {
                $html = '<div class="bp-learndash-activity table course-activity">';
                    $html .= '<div class="table-cell edu-activity-image">';
                        $html .= '<p class="edu-activity-type">' . LearnDash_Custom_Label::get_label( 'lesson' ) . '</p>';
                        if ( has_post_thumbnail( $post->ID ) ) {
                            // Get Featured Image
                            $html .= get_the_post_thumbnail( $post->ID, 'course-archive-thumb', array( 'class' => 'woo-image thumbnail alignleft') );
                        } else {
                            $html .= '<img src="http://placehold.it/360x250&text=Lesson">';
                        }
                    $html .= '</div>';
                    $html .= '<div class="table-cell edu-activity-content">';
                        $status_class = 'fa-check-circle';
                        $html .= '<h4><span>' . $post->post_title . '<i  class="fa '.$status_class.'"></i></span></h4>';

                        $author_url = bp_core_get_user_domain( $post->post_author );
                        $author = '<a href="' . $author_url . '">' . bp_core_get_user_displayname( $post->post_author ) . '</a>';  
                        $lesson_course_id = learndash_get_course_id($post->ID);
                        $lesson_course = get_post($lesson_course_id);
                        $html .= '<div class="edu-activity-meta">';
                            $html .= '<span>' . __('by ', 'boss-learndash') . $author . '</span>';
                            if ( '' != $lesson_course->post_title ) {
                                $html .= '<span class="course-category">' . sprintf( __( 'in <a href="%1s">%2s</a>', 'boss-learndash' ), get_permalink($lesson_course->ID), $lesson_course->post_title ) . '</span>';
                            }
                        $html .= '</div>';
                        if($post->post_excerpt) {
                            $html .= '<p class="edu-activity-excerpt">' . $post->post_excerpt . '</p>';
                        }
                    $html .= '</div>';
                $html .= '</div>';
                $action .= $html;
            }
        }
        return $action;
	}
    
    /**
    * Register new group extension
    */
    function boss_edu_overide_courses_html() {
        if ( function_exists( 'buddypress_learndash' ) && bp_is_active('groups')) {
            remove_action( 'bp_init', array( buddypress_learndash(), 'bp_learndash_add_group_course_extension'), 10 );
            add_action( 'bp_init', array( $this, 'boss_edu_bp_learndash_add_group_course_extension'), 10 );
        }
	}
    
    /**
    * Load Group Course extension 
    */
    public function boss_edu_bp_learndash_add_group_course_extension() {
        if ( class_exists( 'BP_Group_Extension' ) ){
            include_once trailingslashit( plugin_dir_path( __FILE__ ) ) . 'group-extension.php';
        }

        bp_register_group_extension( 'GType_Course_Group' );

    }
    
    /*
    * Remove group template from Boss and add another one from this plugin
    */
    public function boss_edu_disable_theme_template() {
        remove_action('boss_get_group_template', 'boss_get_group_template');
        add_action('boss_get_group_template', array( $this, 'boss_edu_get_group_template' ));
        add_filter( 'bp_learndash_templates_dir_filter', array( $this, 'boss_edu_get_courses_template' ), 10, 2 );
    }
    
    public function boss_edu_get_group_template() {
        load_template( apply_filters( 'boss_edu_course_group_template_path' , $this->templates_dir ) . '/learndash-buddypress/buddypress-group-single.php' );
    }

    /**
     * Override courses.php template file of the buddypress-learndash
     * @param $template_dir
     * @param $template
     * @return string
     */
    public function boss_edu_get_courses_template( $template_dir, $template ) {

        if ( 'courses.php' == $template ) {
            $template_dir =  apply_filters( 'boss_edu_get_courses_template_path' , $this->templates_dir ) . '/learndash-buddypress/';
        }

        return $template_dir;
    }

    
   	/**
	 * Modify buddypress-learndash
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */ 
    
    public function boss_edu_buddypress_learndash(){
        global $BUDDYPRESS_LEARNDASH;
        if($BUDDYPRESS_LEARNDASH) {
            //Move group discussion button
            remove_filter('the_content', array( $BUDDYPRESS_LEARNDASH->bp_learndash_groups, 'bp_learndash_group_discussion_button' ), 110 );
        }        
    }
    
	/**
	 * Register the widgets.
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function boss_edu_register_widgets () {
		// Widget List (key => value is filename => widget class).
		$widget_list =  array( 	'course-progress' 	=> 'Course_Progress', 'course-teacher' 	=> 'Course_Teacher', 'course-participants' => 'Course_Participants' );
        
		foreach ( $widget_list as $key => $value ) {
            
			if ( file_exists( $this->plugin_dir . '/widgets/widget-boss-edu-' . esc_attr( $key ) . '.php' ) ) {
                
				require_once( $this->plugin_dir . '/widgets/widget-boss-edu-' . esc_attr( $key ) . '.php' );
				register_widget( 'Boss_LearnDash_' . $value . '_Widget' );
			}
		} // End For Loop
	} // End register_widgets()
    
    /**
	 * Filter templates path.
	 *
	 * @since Boss for LearnDash (1.0.0)
	 * @access public
	 *
	 */  
    
    public function boss_edu_learndash_templates($filepath, $name, $args, $echo, $return_file_path) {

        if($name == 'course') {
            return $this->boss_learndash_locate_template('course');
        }
        if($name == 'lesson') {
            return $this->boss_learndash_locate_template('lesson');
        }
        if($name == 'course_progress_widget') {
            return $this->boss_learndash_locate_template('course_progress_widget');
        }
        if($name == 'course_list_template') {
            if($args["shortcode_atts"]["post_type"] == 'sfwd-lessons') {
                return $this->boss_learndash_locate_template('lesson_list_template');
            } elseif ($args["shortcode_atts"]["post_type"] == 'sfwd-quiz'){
                return $this->boss_learndash_locate_template('quiz_list_template');
            }
            return $this->boss_learndash_locate_template('course_list_template');
        }
        if($name == 'course_content_shortcode') {
            return $this->boss_learndash_locate_template('course_content_shortcode');
        }
        if($name == 'topic') {
            return $this->boss_learndash_locate_template('topic');
        }
        if($name == 'quiz') {
            return $this->boss_learndash_locate_template('quiz');
        }
        if($name == 'profile') {
            return $this->boss_learndash_locate_template('profile');
        }

        return apply_filters( 'boss_edu_learndash_templates', $filepath );
    }


    /**
     * Adds a box to the main column on the Post and Page edit screens.
     */
    public function boss_edu_course_add_meta_box() {

        $screens = array( 'sfwd-courses', 'sfwd-lessons', 'sfwd-topic' );

        foreach ( $screens as $screen ) {
            add_meta_box(
                'post_video',
                __( 'Video', 'boss-learndash' ),
                array( $this, 'boss_edu_course_meta_box_callback'),
                $screen,
                'advanced',
			    'high'
            );
        }
    }
    

    /**
     * Prints the box content.
     * 
     * @param WP_Post $post The object for the current post/page.
     */
    public function boss_edu_course_meta_box_callback( $post ) {

        // Add a nonce field so we can check for it later.
        wp_nonce_field( 'boss_edu_course_meta_box', 'boss_edu_course_meta_box_nonce' );

        /*
         * Use get_post_meta() to retrieve an existing value
         * from the database and use the value for the form.
         */
        $value = get_post_meta( $post->ID, '_boss_edu_post_video', true );
        echo '<textarea id="boss_edu_post_video" name="boss_edu_post_video" rows="3" style="width:100%;">' . esc_attr( $value ) . '</textarea>';
    }

    /**
     * When the post is saved, saves our custom data.
     *
     * @param int $post_id The ID of the post being saved.
     */
    function boss_edu_course_save_meta_box_data( $post_id ) {

        /*
         * We need to verify this came from our screen and with proper authorization,
         * because the save_post action can be triggered at other times.
         */

        // Check if our nonce is set.
        if ( ! isset( $_POST['boss_edu_course_meta_box_nonce'] ) ) {
            return;
        }

        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $_POST['boss_edu_course_meta_box_nonce'], 'boss_edu_course_meta_box' ) ) {
            return;
        }

        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // Check the user's permissions.
        if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

            if ( ! current_user_can( 'edit_page', $post_id ) ) {
                return;
            }

        } else {

            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return;
            }
        }

        /* OK, it's safe for us to save the data now. */

        // Make sure that it is set.
        if ( ! isset( $_POST['boss_edu_post_video'] ) ) {
            return;
        }

        // Sanitize user input.
        $data = sanitize_text_field( $_POST['boss_edu_post_video'] );

        // Update the meta field in the database.
        update_post_meta( $post_id, '_boss_edu_post_video', $data );
    }    
    
    /**
	 * Register Learndash sidebars.
	 *
	 * @since Boss for LearnDash (1.0.0)
	 * @access public
	 *
	 * @uses wp_deregister_style() To deregister style.
	 */
    public function boss_edu_sidebar() {
        register_sidebar( array(
            'name' 			=> 'LearnDash &rarr; Default',
            'id'	 		=> 'learndash-default',
            'description' 	=> sprintf( __( 'Only display on LearnDash %s pages', 'boss-learndash' ), LearnDash_Custom_Label::get_label( 'quiz' ) ),
            'before_widget' => '<aside id="%1$s" class="widget %2$s">',
            'after_widget' 	=> '</aside>',
            'before_title' 	=> '<h4 class="widgettitle">',
            'after_title' 	=> '</h4>',
        ) ); 
        register_sidebar( array(
            'name' 			=> 'LearnDash &rarr; Courses',
            'id'	 		=> 'learndash-courses',
            'description' 	=> 'Only display on LearnDash Course pages',
            'before_widget' => '<aside id="%1$s" class="widget %2$s">',
            'after_widget' 	=> '</aside>',
            'before_title' 	=> '<h4 class="widgettitle">',
            'after_title' 	=> '</h4>',
        ) );
        register_sidebar( array(
            'name' 			=> 'LearnDash &rarr; Course',
            'id'	 		=> 'learndash-course',
            'description' 	=> 'Only display on LearnDash Course pages',
            'before_widget' => '<aside id="%1$s" class="widget %2$s">',
            'after_widget' 	=> '</aside>',
            'before_title' 	=> '<h4 class="widgettitle">',
            'after_title' 	=> '</h4>',
        ) ); 
        register_sidebar( array(
            'name' 			=> sprintf( __( 'LearnDash &rarr; %s/%s', 'boss-learndash' ), LearnDash_Custom_Label::get_label( 'lesson' ), LearnDash_Custom_Label::get_label( 'topic' ) ),
            'id'	 		=> 'learndash-lesson',
            'description' 	=> sprintf(  __( 'Display on LearnDash %s and %s pages', 'boss-learndash' ), LearnDash_Custom_Label::get_label( 'lesson' ), LearnDash_Custom_Label::get_label( 'topic' ) ),
            'before_widget' => '<aside id="%1$s" class="widget %2$s">',
            'after_widget' 	=> '</aside>',
            'before_title' 	=> '<h4 class="widgettitle">',
            'after_title' 	=> '</h4>',
        ) ); 
        register_sidebar( array(
            'name' 			=> 'LearnDash &rarr; Quiz',
            'id'	 		=> 'learndash-quiz',
            'description' 	=> sprintf( __( 'Only display on LearnDash %s pages', 'boss-learndash' ), LearnDash_Custom_Label::get_label( 'quiz' ) ),
            'before_widget' => '<aside id="%1$s" class="widget %2$s">',
            'after_widget' 	=> '</aside>',
            'before_title' 	=> '<h4 class="widgettitle">',
            'after_title' 	=> '</h4>',
        ) ); 
    }
    
    /**
	 * Check for sidebar templates
	 *
	 * @since Boss for LearnDash (1.0.0)
	 * @access public
	 *
	 */
    public function boss_edu_load_template( $template ) {
        $template .= '.php';
        if( file_exists( STYLESHEETPATH.'/boss-learndash/'.$template ) )
            include_once( STYLESHEETPATH.'/boss-learndash/'.$template );
        else if( file_exists( TEMPLATEPATH.'boss-learndash/'.$template ) )
            include_once( TEMPLATEPATH.'/boss-learndash/'.$template );
        else{
            $template_dir = apply_filters( 'boss_edu_templates_dir_filter', $this->templates_dir );
            include_once trailingslashit( $template_dir ) . $template;
        }
    }

    /**
	 * Locate learnash templates
	 *
	 * @since Boss for LearnDash 
	 * @access public
	 *
	 */
    public function boss_learndash_locate_template( $template ) {

        $template .= '.php';

        if( file_exists( STYLESHEETPATH.'/boss-learndash/learndash/'.$template ) )
            $path =  STYLESHEETPATH.'/boss-learndash/learndash/'.$template;
        else if( file_exists( TEMPLATEPATH.'/boss-learndash/learndash/'.$template ) )
            $path = TEMPLATEPATH.'/boss-learndash/learndash/'.$template;
        else
            $path = $this->templates_dir . '/learndash/'. $template;

        return apply_filters( 'boss_learndash_locate_template', $path );
    }
    
    /**
	 * Deregister Learndash css.
	 *
	 * @since Boss for LearnDash (1.0.0)
	 * @access public
	 *
	 * @uses wp_deregister_style() To deregister style.
	 */
    public function boss_edu_deregister_theme_css_learndash( $content ) 
    {
        // LearnDash css
        wp_deregister_style( 'sfwd_template_css' );
		return  $content;
    }
    
    /**
	 * Enqueue styles and scripts for various plugins.
	 *
	 * @since Boss for LearnDash (1.0.0)
	 * @access public
	 *
	 */
    public function boss_edu_enqueue_scripts() 
    {

        //Check boss theme is active
        if ( ! function_exists('boss') ) {
            return;
        }
        
        //Check for option values
        if ( ! function_exists('boss_get_option') ) {
            $theme_layout = boss_get_option( 'boss_layout_style' );
        }


        // Styles
        wp_deregister_style( 'wpProQuiz_front_style' );        
        
        if(is_rtl()){
            //wp_enqueue_style( 'boss-learndash', $this->assets_url . '/css/learndash-rtl.css', array(), '1.0.6', 'all' );
            wp_enqueue_style( 'boss-learndash', $this->assets_url . '/css/learndash-rtl.min.css', array(), BOSS_LEARNDASH_PLUGIN_VERSION, 'all' );
        } else {
//            wp_enqueue_style( 'boss-learndash', $this->assets_url . '/css/learndash.css', array(), '1.0.6', 'all' );
            wp_enqueue_style( 'boss-learndash', $this->assets_url . '/css/learndash.min.css', array('learndash_style'), BOSS_LEARNDASH_PLUGIN_VERSION, 'all' );
        }
        
        if( ! empty( $theme_layout ) && $theme_layout == 'boxed') {
            if(is_rtl()){
                wp_enqueue_style( 'boss-learndash-boxed', $this->assets_url . '/css/learndash-boxed-rtl.css', array(), BOSS_LEARNDASH_PLUGIN_VERSION, 'all' );
            } else {
                wp_enqueue_style( 'boss-learndash-boxed', $this->assets_url . '/css/learndash-boxed.css', array(), BOSS_LEARNDASH_PLUGIN_VERSION, 'all' );
            }            
        }

        // Javascript
        //wp_enqueue_script( 'boss-learndash', $this->assets_url . '/js/learndash.js', array('jquery'), '1.0.6', true );
        wp_enqueue_script( 'boss-learndash', $this->assets_url . '/js/learndash.min.js', array('jquery'), BOSS_LEARNDASH_PLUGIN_VERSION, true );
    }
    
    /**
	 * Load single templates for custom posts from plugin.
	 *
	 * @since Boss for LearnDash (1.0.0)
	 * @access public
	 *
	 */
    /* Filter the single_template with our custom function */
    public function boss_edu_load_singles_templates($single) {
        global $wp_query, $post;
        /* Checks for single template by post type */
        /* Learndash */
        if ($post->post_type == "sfwd-quiz"){
            return $this->boss_learndash_locate_template('single-sfwd-quiz');
        }
        if ($post->post_type == "sfwd-topic"){
            return $this->boss_learndash_locate_template('single-sfwd-topic');
        }
        if ($post->post_type == "sfwd-lessons"){
            return $this->boss_learndash_locate_template('single-sfwd-lesson');
        }
        if ($post->post_type == "sfwd-courses") {
            return $this->boss_learndash_locate_template('single-sfwd-course');
        }
        return $single;
    }
    
    /**
	 * Load archive templates for custom posts from plugin.
	 *
	 * @since Boss for LearnDash (1.0.0)
	 * @access public
	 *
	 */
//    public function boss_edu_load_archive_templates( $template ) {
//          if ( is_tax('wpc_course_category') ) {
//            $theme_files = array('archive-wpc_course.php', 'wpcourseware/archive-wpc_course.php');
//            $exists_in_theme = locate_template($theme_files, false);
//
//            if ( $exists_in_theme != '' ) {
//              return $exists_in_theme;
//            } else {
//              return $this->templates_dir . '/courseware/archive-wpc_course.php';
//            }
//          }
//          return $template;
//    }
    
    public function boss_edu_post_author_meta() {
        $date = sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s" pubdate>%4$s</time></a></span>',
            esc_url( get_permalink() ),
            esc_attr( get_the_time() ),
            esc_attr( get_the_date( 'c' ) ),
            esc_html( get_the_date() )
        );

        $author = sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>',
            esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
            esc_attr( sprintf( __( 'View all posts by %s', 'boss-learndash' ), get_the_author() ) ),
            get_the_author()
        );

        if (function_exists('get_avatar')) { 
            $avatar = sprintf( '<a href="%1$s" rel="bookmark">%2$s</a>', 
                                esc_url( get_permalink() ),
                                get_avatar( get_the_author_meta('email'), 55 ) 
                            ); 
        }
        
        echo '<span class="post-author">';
            echo $avatar;
            echo $author;
        echo '</span>';
        echo $date;
    }

    /**
	 * Load plugin text domain
	 *
	 * @since Boss for LearnDash (1.0.0)
	 *
	 * @uses sprintf() Format .mo file
	 * @uses get_locale() Get language
	 * @uses file_exists() Check for language file
	 * @uses load_textdomain() Load language file
	 */
	public function setup_textdomain()
	{
		$domain = 'boss-learndash';
		$locale = apply_filters('plugin_locale', get_locale(), $domain);

		//first try to load from wp-content/languages/plugins/ directory
		load_textdomain($domain, WP_LANG_DIR.'/plugins/'.$domain.'-'.$locale.'.mo');

		//if not found, then load from boss-learndash/languages/ directory
		load_plugin_textdomain( 'boss-learndash', false, $this->lang_dir );
	}

        /**
         * Teacher contact form markup
         *
         * @since Boss for Learndash (1.0.0)
         *
         * @global type $current_user
         * @param type $post
         * @return string
         */
        public function boss_edu_teacher_contact_form( $post ) {

            if( ! is_user_logged_in() ) return;

            global $current_user;
            wp_get_current_user();

            $html = '';

            if( ! isset( $post->ID ) ) return $html;

            $html .= '<h3 id="private_message">' . __( 'Send Private Message', 'boss-learndash' ) . '</h3>';
            $html .= '<p>';
            $html .= '</p>';
            $html .= '<form name="contact-teacher" action="" method="post" class="contact-teacher">';
            $html .= '<p class="form-row form-row-wide">';
            $html .= '<textarea class="boss-edu-teacher-message" name="contact_message" placeholder="' . __( 'Enter your private message.', 'boss-learndash' ) . '"></textarea>';
            $html .= '</p>';
            $html .= '<p class="form-row">';
            $html .= '<input type="hidden" class="boss-edu-msg-course-id" name="post_id" value="' . $post->ID . '" />';
            $html .= '<input type="hidden" class="boss-edu-msg-sender-id" name="sender_id" value="' . $current_user->ID . '" />';
            $html .= '<input type="hidden" class="boss-edu-msg-receiver-id" name="receiver_id" value="' . $post->post_author . '" />';
            $html .= wp_nonce_field( 'message_teacher', 'boss_edu_learndash_message_teacher_nonce', true, false );
            $html .= '<input type="submit" class="boss-edu-send-message-widget" value="' . __( 'Send Message', 'boss-learndash' ) . '" />';
            $html .= '</p>';
            $html .= '<div class="fix"></div>';
            $html .= '</form>';

            return $html;
        }

        /**
         * Ajax handling for contact a teacher button
         */
        public function boss_edu_contact_teacher_ajax() {
            $msg_content = $_POST['content'];
            if ( empty( $msg_content ) ) {
                echo 'Failed';
                die();
            }
            $sender_id = $_POST['sender_id'];
            $reciever_id = $_POST['reciever_id'];
            $course_id = $_POST['course_id'];
            $subject = 'Regarding' .get_the_title($course_id);

            $args = array( 'recipients' => array($reciever_id), 'sender_id' => $sender_id, 'subject' => $subject, 'content' => $msg_content );
            $msg_id = messages_new_message( $args );

            echo $msg_id;

            die();
        }
        
        /**
         * Add the given user to course's participants list.
         * 
         * @param int $user_id
         * @param WP_POST $course
         */
        public function add_user_to_participants_list( $args ){
            $user_id = $args['user']->ID;
            $course_id = $args['course']->ID;
            
            if( !$user_id )
                return;
            
            $learners = array();
            
            $meta = get_post_meta( $course_id, '_sfwd-courses', true );
            if ( ! empty( $meta['sfwd-courses_course_access_list'] ) ) {
                $learners = explode( ',', $meta['sfwd-courses_course_access_list'] );
            }
            
            if( empty( $learners ) || !in_array( $user_id, $learners ) ){
                $learners[] = $user_id;
                
                $meta['sfwd-courses_course_access_list'] = implode( ',', $learners );
                update_post_meta( $course_id, '_sfwd-courses', $meta );
            }
        }
}

endif;

?>