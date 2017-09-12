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
  wp_enqueue_style( 'boss-child-custom', get_stylesheet_directory_uri().'/css/custom.css' );
  wp_enqueue_style( 'boss-child-custom', get_stylesheet_directory_uri().'/css/main.css' );
}
add_action( 'wp_enqueue_scripts', 'boss_child_theme_scripts_styles', 9999 );


/****************************** CUSTOM FUNCTIONS ******************************/

// Add your own custom functions here



function bd_settings_menu() {
  add_theme_page('Being Design Theme Options', 'Being Design Theme Options', 'edit_theme_options', 'bd-theme-options', 'bd_theme_settings_page');
  add_action( 'admin_init', 'register_bdsettings' );
}
add_action('admin_menu', 'bd_settings_menu');

//this function creates a simple page with title Custom Theme Options Page.
function bd_theme_settings_page() { 
  ?>
  <div class="wrap">
    <h1>Being Design Theme Options</h1>
      <form method="post" action="options.php">
        <?php
        settings_fields( 'bd-options-group' );
        do_settings_sections( 'bd-options-group' );
        ?>

        <table class="form-table">
              <tr valign="top">
              <th scope="row">Hide Left Sidebar</th>
              <td><input type="checkbox" name="hide_left_bar" <?php if ( get_option('hide_left_bar') ) echo 'checked'; ?> value="true" /></td>
              </tr>
          </table>

        <?php submit_button(); ?>

      </form>
  </div>
  <?php
}

function register_bdsettings() { 
  // whitelist options
  register_setting( 'bd-options-group', 'hide_left_bar' );

}



