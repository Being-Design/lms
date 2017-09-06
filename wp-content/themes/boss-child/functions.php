<?php
/**
 * @package Boss Child Theme
 * The parent theme functions are located at /boss/buddyboss-inc/theme-functions.php
 * Add your own functions in this file.
 */

/**
 * Sets up theme defaults
 *
 * @since Boss Child Theme 1.0.0
 */
function boss_child_theme_setup()
{
  /**
   * Makes child theme available for translation.
   * Translations can be added into the /languages/ directory.
   * Read more at: http://www.buddyboss.com/tutorials/language-translations/
   */

  // Translate text from the PARENT theme.
  load_theme_textdomain( 'boss', get_stylesheet_directory() . '/languages' );

  // Translate text from the CHILD theme only.
  // Change 'boss' instances in all child theme files to 'boss_child_theme'.
  // load_theme_textdomain( 'boss_child_theme', get_stylesheet_directory() . '/languages' );

}
add_action( 'after_setup_theme', 'boss_child_theme_setup' );

/**
 * Enqueues scripts and styles for child theme front-end.
 *
 * @since Boss Child Theme  1.0.0
 */
function boss_child_theme_scripts_styles()
{
  /**
   * Scripts and Styles loaded by the parent theme can be unloaded if needed
   * using wp_deregister_script or wp_deregister_style.
   *
   * See the WordPress Codex for more information about those functions:
   * http://codex.wordpress.org/Function_Reference/wp_deregister_script
   * http://codex.wordpress.org/Function_Reference/wp_deregister_style
   **/

  /*
   * Styles
   */
  wp_enqueue_style( 'font-libre-baskerville', 'https://fonts.googleapis.com/css?family=Libre+Baskerville:300,400,700' );
  wp_enqueue_style( 'font-montserrat', 'https://fonts.googleapis.com/css?family=Montserrat:400,600' );
  wp_enqueue_style( 'boss-child-libraries', get_stylesheet_directory_uri() . '/resources/css/compressed/libraries.css' );
  wp_enqueue_style( 'boss-child-custom', get_stylesheet_directory_uri() . '/resources/css/compressed/main.css' );

  /*
   * Scripts
   */
  wp_enqueue_script( 'boss-child-custom', get_stylesheet_directory_uri() . '/resources/js/compressed/libraries.js' );
}
add_action( 'wp_enqueue_scripts', 'boss_child_theme_scripts_styles', 9999 );



/****************************** CUSTOM FUNCTIONS ******************************/

  /*
   * Adds shortcode for current username
   */
  function shortcode_current_user( $attributes ) {

    global $current_user, $user_login;
    get_currentuserinfo();
    add_filter('widget_text', 'do_shortcode');

    if ($user_login) {
      return $current_user->display_name;
    } else {
      return '';
    }
  }
  add_shortcode( 'current_user', 'shortcode_current_user' );


  /*
   * Adds shortcode for user-specific links, in the form of a box
   *
   * title: sets the box title.
   * target: goes to this url previxed with 'members/{user_login}'.
   * href: goes to a custom url; overridden if target is not blank.
   */
  function shortcode_personal_link( $attributes, $content ) {
    add_filter('widget_text', 'do_shortcode');

    $title =  '';
    $target = '';
    $href = '';
    $user_id = '';
    if(isset($attributes['title'])) {
      $title = $attributes['title'];
    }

    if(isset($attributes['href'])) {
      $href = $attributes['href'];
    }

    if(isset($attributes['target'])) {
      global $current_user, $user_login;
      get_currentuserinfo();

      if ($user_login) {
        $user_id = $current_user->user_login;
      }

      $target = $attributes['target'];
      $href = '/members/' . $user_id . '/' . $target;
    }

    if(!empty($href)) {

      $link = '
      <a href="' . $href . '" class="personal-link-card" data-mh="personal-link-card">
        <div class="card-content">
          <h4>' . $title . '</h4>
          <div class="card-content">' . $content . '</div>
        </div>
      </a>
      ';

    } else {
      $link = '
      <div class="personal-link-card">
        <div class="card-content">
          <h4>' . $title . '</h4>
          <div class="card-content">' . $content . '</div>
        </div>
      </div>
      ';
    }

    return $link;
  }
  add_shortcode( 'personal_link', 'shortcode_personal_link' );



  /*
   * WP Login Redirect
   */
  function redirect_login_page(){

      // var for checking if this page equals wp-login.php
      $page_viewed = basename( $_SERVER['REQUEST_URI'] );
      $user_can_access = false;

      if ( strstr($page_viewed, "logout") !== false ) {
        $user_can_access = true;
      }
      elseif ( strstr($page_viewed, "lostpassword") !== false ) {
        $user_can_access = true;
      }
      elseif ( strstr($page_viewed, "action=rp") !== false || strstr($page_viewed, "resetpass") !== false ) {
        $user_can_access = true;
      }

      
      if( strstr($page_viewed, "wp-login") !== false && !$user_can_access ) {
        wp_redirect( '/login/' );
        exit();
      }

  }
  add_action( 'init','redirect_login_page' );

  