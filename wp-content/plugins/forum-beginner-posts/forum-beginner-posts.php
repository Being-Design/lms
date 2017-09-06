<?php
/**
 * @author            Kona Macphee <kona@fidgetylizard.com>
 * @since             1.0.0
 * @package           Forum_Beginner_Posts
 *
 * @wordpress-plugin
 * Plugin Name:       Forum Beginner Posts
 * Plugin URI:        https://wordpress.org/plugins/forum-beginner-posts/
 * Description:       Enables the Visual Editor for bbPress posts, using paste-as-text mode to prevent unwanted styling and markup appearing in pasted content.
 * Version:           1.1.0
 * Author:            Fidgety Lizard
 * Author URI:        https://fidgetylizard.com
 * Contributors:      fliz, kona
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       forum-beginner-posts
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
  die;
}

if ( ! class_exists( 'Forum_Beginner_Posts' ) )
{
  class Forum_Beginner_Posts
  {
    /**
     * Some useful bbPress-related constants
     */
    const BBP_PLUGIN = 'bbpress/bbpress.php';
    const BBP_CLASS = 'bbPress';
    const BBP_OPTIONS = 'options-general.php?page=bbpress';
    const BBP_EDIT_ON = '_bbp_use_wp_editor';

    /**
     * Constants for our default options settings.
		 * These match plugin behaviour in versions before the options were added.
     */
		const FP_DEFAULT = 'on';	// Default setting for force paste - ON
		const ET_DEFAULT = 'off'; // Default setting for extended editor - OFF
    
    /**
     * Construct the plugin object
     */
    public function __construct()
    {
      // Need to do our initialisation after all other plugins are loaded
      add_action( 'plugins_loaded', array( $this, 'plugin_init' ) );

      // Prepare for i18n translations
      add_action( 'plugins_loaded', array( $this, 'load_my_textdomain' ) );
    } // END public function __construct


    /**
    * Initialise plugin and set up actions.
		* This needs to be run AFTER plugins have been loaded so that 
		* dependencies can be checked by bbpress_is_running().
    */
    public function plugin_init() 
    {
       //Check first that bbPress is active
      if ( TRUE === $this->bbpress_is_running() ) {
        
        // Enqueue admin scripts
        add_action( 'admin_enqueue_scripts',
                array( $this, 'admin_add_scripts' ) );

        // Enable the plugin admin menu
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );

        // Warn if bbPress options need adjusted
        add_action( 'admin_notices', 
                array( $this, 'notice_about_bbpress_options' ) );

        // Enable the bbPress plugin that supports paste-as-text if needed
        add_filter( 'bbp_get_tiny_mce_plugins', 
                array( $this, 'bbp_tinymce_paste_plain_text' ) );

        // Enable the visual editor in required mode
        add_filter( 'bbp_after_get_the_content_parse_args', 
                array( $this, 'bbp_enable_visual_editor' ) );

				if ('on' === get_option( 'flizfbp_extendtiny', self::ET_DEFAULT ) ) {
           // Only need this if full TinyMCE is enabled
          add_filter( 'bbp_kses_allowed_tags', 
                array( $this, 'bbpress_custom_kses_allowed_tags' ) );
        }
      }
    } // END public function __construct

    /**
     * Activate the plugin
     */
    public static function activate()
    {
			// Initialise our admin preferences if not already configured,
			// using our class defaults
      if (FALSE === get_option( 'flizfbp_forcepaste' ) ) {
        update_option( 'flizfbp_forcepaste', self::FP_DEFAULT );
      }
      if ( FALSE === get_option( 'flizfbp_extendtiny' ) ) {
        update_option( 'flizfbp_extendtiny', self::ET_DEFAULT );
      }
    } // END public static function activate

    /**
     * Deactivate the plugin
     */
    public static function deactivate()
    {
      // Nothing to do here
    } // END public static function deactivate


    /**
      * Check if bbPress is installed and active.
      * @return bool
      */
    private function bbpress_is_running() 
    {
      $active = FALSE;
      $single = FALSE;
      $multi = FALSE;
      if ( class_exists( self::BBP_CLASS ) ) {
        $active = TRUE;
      }
      else {
        // Double check in case bbPress is active but not yet available
        if ( is_multisite() ) {
          $plugins = get_site_option( 'active_sitewide_plugins' );
          if ( isset( $plugins[ self::BBP_PLUGIN ] ) ) {
            $multi = TRUE;
          }
        }
        if ( FALSE === $multi ) {
          $single = in_array( 
              self::BBP_PLUGIN, get_option( 'active_plugins' ) );
        }
      }
      return ( $active || $multi || $single ); // True if any is true, otw false
    }

    public function notice_about_bbpress_options()
    {
      global $pagenow;

      // Check whether 'Post Formatting' has been explicitly switched off
      // (If unset in the database, it defaults to switched on).
      $option = get_option( self::BBP_EDIT_ON, TRUE );

      // We only need the notice if 'Post Formatting' is switched off
      if ( FALSE == $option ) {
      
        // Check if we have a suitably qualified user to see the notice
        if ( current_user_can( 'install_plugins' ) || 
             current_user_can( 'manage_options' ) ) {

          // Check if we're on the plugins or forum options page
          if ( ( $pagenow == 'plugins.php' ) ||
               ( ( $pagenow == 'options-general.php' ) && 
                 (isset( $_GET['page'] ) && ( "bbpress" == $_GET[ 'page' ] ) )
               ) ) {
      
            // We have a suitable user and page, so show notification
            $url = get_admin_url() . self::BBP_OPTIONS;

            echo "<div class='error'><p><b>";
            esc_html_e( "Notice from Forum Beginner Posts:", 
                          'forum-beginner-posts' );
            echo "</b></p><p>";
            printf( wp_kses( 
                __( "Please enable 'Post Formatting' (under Forum features on the <a href='%s'>Forum Settings</a> page) if you wish to use Forum Beginner Posts.", 
                    'forum-beginner-posts' ), 
                array(  'a' => array( 'href' => array() ) ) ), 
                esc_url( $url ) 
            );
            echo "</p></div>";
          }
        }
      }
    }

    /**
     * Switch on the TinyMCE editor in "teeny" mode (reduced button set.)
     */
    public function bbp_enable_visual_editor( $args = array() ) 
    {
      // Default to OFF unless explicitly on
      if ( 'off' === get_option( 'flizfbp_extendtiny', self::ET_DEFAULT ) ) {
        $args[ 'teeny' ] = true;  // Enable TinyMCE with reduced button set
      }
      else {
        $args[ 'teeny' ] = false;  // Enable extended TinyMCE button set
      }
      // Default to ON unless explicitly off
      if ( 'on' === get_option( 'flizfbp_forcepaste', self::FP_DEFAULT ) ) {
        $args[ 'tinymce' ] = array(
          'paste_as_text' => true
        );
      } 
      else {
        $args[ 'tinymce' ] = array(
          'paste_as_text' => false
        );
      }
      return $args;
    }

    /**
     * Enable the bbPress "paste" plugin so TinyMCE can implement 
     * paste-as-text, if required.
     */
    public function bbp_tinymce_paste_plain_text( $plugins = array() ) 
    {
      // Default to ON unless explicitly off
      if ( 'on' === get_option( 'flizfbp_forcepaste', self::FP_DEFAULT ) ) {
        $plugins[] = 'paste';
      }
      return $plugins;
    }

    /**
     * Set up the necessary admin-end CSS and JS.
     */
    public function admin_add_scripts()
    {
      global $pagenow;

      // Only add the scripts on our actual admin page
      if ( ( $pagenow == 'options-general.php' ) &&
            ( isset( $_GET['page'] ) &&
                   ( 'forum_beginner_posts' == $_GET[ 'page' ] )
            )
      ) {
        // Add the admin CSS
        wp_enqueue_style(
          'flizfbp-admin-styles',
          plugin_dir_url( __FILE__ ) . 'css/flizfbp-admin-styles.css',
          false
        );
      }
    } // END public function admin_add_scripts

    /**
     * Create a menu entry for our administration page.
     */
    public function admin_menu()
    {
      add_options_page(
          esc_html__('Forum Beginner Posts Settings',
                        'forum-beginner-posts' ),
          esc_html__('Forum Beginner Posts',
                        'forum-beginner-posts' ),
          'manage_options',
          'forum_beginner_posts',
          array( $this, 'admin_settings_page')
      );
    } // END public function admin_menu

    /**
     * Create our administration page and manage its options.
     */
    public function admin_settings_page()
    {
      $showNotice = FALSE;
      // If we've got a form submission, set all options accordingly
      if ( isset( $_POST['flizfbp__s'] ) ) {
        $showNotice = TRUE;
        if ( isset( $_POST['flizfbp_extendtiny'] ) ) {
          update_option( 'flizfbp_extendtiny', 'on' );
        }
        else {
          update_option( 'flizfbp_extendtiny', 'off' );
        }
        if ( isset( $_POST['flizfbp_forcepaste'] ) ) {
          update_option( 'flizfbp_forcepaste', 'on' );
        }
        else {
          update_option( 'flizfbp_forcepaste', 'off' );
        }
      }
      $forceChecked = '';
      if ( 'on' === get_option( 'flizfbp_forcepaste', self::FP_DEFAULT ) ) {
        $forceChecked = ' checked';
      }
      $extendChecked = '';
      if ('on' === get_option( 'flizfbp_extendtiny', self::ET_DEFAULT ) ) {
        $extendChecked = ' checked';
      }
      ?>
      <div class='wrap'>
        <?php if ( TRUE === $showNotice) {
          echo '<div class="updated"><p>';
            esc_html_e( 'Your changes have been saved.',
                          'forum-beginner-posts' );
          echo '</p></div>';
        }
        ?>
        <h1><?php esc_html_e("Forum Beginner Posts preferences",
                      'forum-beginner-posts');
            ?></h1>

        <form method="post" action="<?php admin_url(
                'options-general.php?page=forum-beginner-posts' ); ?>">
          <input type='hidden' name='flizfbp__s' value='flizfbp__s'>
          <table class='form-table'>
            <tbody>

              <tr>
                <th scope='row'><?php
                  esc_html_e( 'Paste as text', 'forum-beginner-posts' );?>
                </th>
                <td>
                  <input type="checkbox" id='flizfbp_forcepaste' name='flizfbp_forcepaste' value='flizfbp_forcepaste'<?php echo $forceChecked;?>><?php
                    esc_html_e(
                      'Force paste-as-text when using TinyMCE',
                      'forum-beginner-posts'
                  );?>
                  <div class='flizfbp-explainer'><?php
                    esc_html_e(
                      'Disabling this option may lead to garbled forum posts in future (but it won\'t change existing posts.)',
                      'forum-beginner-posts'
                  );?></em></div>
                </td>
              </tr>

              <tr>
                <th scope='row'><?php
                  esc_html_e( 'Extended editor', 'forum-beginner-posts' );?>
                </th>
                <td>
                  <input type='checkbox' id='flizfbp_extendtiny' name='flizfbp_extendtiny' value='flizfbp_extendtiny'<?php echo $extendChecked;?>><?php
                   esc_html_e(
                      'Enable extended editor (more buttons in TinyMCE)',
                      'forum-beginner-posts'
                  );?>
                  <div class='flizfbp-explainer'><?php
                    esc_html_e( 'Extended TinyMCE functions may not work nicely with all themes - please test after enabling.',
                      'forum-beginner-posts' ); ?>
                  </div>
                </td>
              </tr>

            </tbody>
           </table>
          <?php submit_button(); ?>
        </form>
        <hr/>
        <h4><?php esc_html_e("Note: Embedded content", 
                 'forum-beginner-posts');?></h5>
        <p><?php esc_html_e( 'Even if paste-as-text is forced, links in
         pasted text may show up as embedded content (videos, images,
         external Wordpress posts etc.) in the displayed forum
         posts.', 
                'forum-beginner-posts'); ?></p>
        <p><?php esc_html_e('To disable this embedded content, please switch 
         "Auto-embed links" OFF in the BBPress Forums settings.', 
                'forum-beginner-posts'); ?></p>
      </div>
    <?php
    } // END public function admin_settings_page


    /**
     * Enable extra tags through input stripping for extended TinyMCE.
     * This function is by Stephen Edgar - http://netweb.com.au
     * See https://gist.github.com/ntwb/7797990 
		 * Some of these tags will be surplus to requirement unless 
		 * advanced TinyMCE options/buttons are enabled elsewhere,
		 * e.g. in the current theme.
     */
    public function bbpress_custom_kses_allowed_tags() 
    {
      return array(
        // Links
        'a'          => array(
          'class'    => true,
          'href'     => true,
          'title'    => true,
          'rel'      => true,
          'class'    => true,
          'target'    => true,
        ),
        // Quotes
        'blockquote' => array(
          'cite'     => true,
        ),
        
        // Div
        'div' => array(
          'class'     => true,
        ),
        
        // Span
        'span'             => array(
          'class'     => true,
        ),
        
        // Code
        'code'       => array(),
        'pre'        => array(
          'class'  => true,
        ),
        // Formatting
        'em'         => array(),
        'strong'     => array(),
        'del'        => array(
          'datetime' => true,
        ),
        // Lists
        'ul'         => array(),
        'ol'         => array(
          'start'    => true,
        ),
        'li'         => array(),
        // Images
        'img'        => array(
          'class'    => true,
          'src'      => true,
          'border'   => true,
          'alt'      => true,
          'height'   => true,
          'width'    => true,
        ),
        // Tables
        'table'      => array(
          'align'    => true,
          'bgcolor'  => true,
          'border'   => true,
        ),
        'tbody'      => array(
          'align'    => true,
          'valign'   => true,
        ),
        'td'         => array(
          'align'    => true,
          'valign'   => true,
        ),
        'tfoot'      => array(
          'align'    => true,
          'valign'   => true,
        ),
        'th'         => array(
          'align'    => true,
          'valign'   => true,
        ),
        'thead'      => array(
          'align'    => true,
          'valign'   => true,
        ),
        'tr'         => array(
          'align'    => true,
          'valign'   => true,
        )
      );
    } // END public function bbpress_custom_kses_allowed_tags

    /**
     * Set things up for i18n
     */
    public function load_my_textdomain() 
    {
      load_plugin_textdomain( 
        'forum-beginner-posts', 
        FALSE, 
        basename( dirname( __FILE__ ) ) . '/languages/' 
      );
    }

  } // END class Forum_Beginner_Posts
} // END if ( ! class_exists( 'Forum_Beginner_Posts' ) )



if ( class_exists( 'Forum_Beginner_Posts' ) )
{
  // Installation and uninstallation hooks
  register_activation_hook(
    __FILE__, 
    array( 'Forum_Beginner_Posts', 'activate' )
  );
  register_deactivation_hook(
    __FILE__, 
    array( 'Forum_Beginner_Posts', 'deactivate' )
  );
  // instantiate the plugin class
  $wp_plugin_template = new Forum_Beginner_Posts();
}
?>
