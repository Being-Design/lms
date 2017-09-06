<?php
/*
Plugin Name:Easy Coming Soon
Description: Easy coming Soon plugin allows you to quickly create a launch / under construction page for your website. Collect E-mails and connect with users on Social Networks. 
Version: 1.9.6.1
Author:Ankit Agarwal, Priyanshu Mittal
Author URI: http://easycomingsoon.com/
Plugin URI: http://easycomingsoon.com/
Text Domain: easy-coming-soon
Domain Path: /lang
*/

// Plugin Root File.
if ( ! defined( 'COMINGSOON_PLUGIN_FILE' ) ) {
	define( 'COMINGSOON_PLUGIN_FILE', __FILE__ );
}

add_action('admin_menu','cmgs_menu');


/*
* Coming Soon menu
*
* Create the Plugin Menu
*
*/
function cmgs_menu()
{
	$comingsoon_lang_dir  = dirname( plugin_basename( COMINGSOON_PLUGIN_FILE ) ) . '/lang/';
	load_plugin_textdomain( 'easy-coming-soon', false, $comingsoon_lang_dir );
	
	$menu = add_menu_page(__('Easy coming soon','easy-coming-soon'), __('Easy coming soon','easy-coming-soon'),'administrator', 'coming_soon',	'construction');

	add_action( 'admin_print_styles-' . $menu, 'cmgs_plugin_js_css' );
}

/*
* Coming Soon plugin js and css
*
* attach file css and js from plugin directory
*
*/
		

function cmgs_plugin_js_css()
{
	// enqueue script for coming soon plugin
	
	wp_enqueue_media();
	wp_enqueue_script( 'theme-preview' );
	wp_enqueue_script('tab',plugins_url('/js/spa-tab.js',__FILE__));
   // wp_enqueue_script('tab-content',plugins_url('/js/jquery.option.tree.js',__FILE__));
	wp_enqueue_script('media-uploads',plugins_url('/js/media-upload-script.js',__FILE__),array('media-upload','thickbox','jquery'));
	wp_enqueue_script('my-color-picker-script', plugins_url('js/my-color-picker-script.js',__FILE__), array( 'wp-color-picker' ), false, true );

   
   

	wp_enqueue_style('dashboard');
	
	wp_enqueue_style('wp-color-picker');
	//enqueue css for coming soon plugin
	wp_enqueue_style('spa_flexslider', plugins_url('/css/theme-admin.css',__FILE__));
	wp_enqueue_style('classic',plugins_url('/css/style-classic.css',__FILE__));
	wp_enqueue_style('option',plugins_url('/css/style-option.css',__FILE__));
	 wp_enqueue_style('bootsrap',plugins_url('/css/cs-bootstrap.css',__FILE__));
	wp_enqueue_style('thickbox');
	wp_enqueue_style('farbtasticss',plugins_url('/css/farbtasticss.css',__FILE__));
	
	
}
		
		
function cmgs_wp_admin_style() {
        wp_register_style( 'custom_wp_admin_css', plugins_url('/css/ecs-admin-style.css',__FILE__), false, '1.0.0' );
        wp_enqueue_style( 'custom_wp_admin_css' );
}
add_action( 'admin_enqueue_scripts', 'cmgs_wp_admin_style' );
		
		
add_action('admin_bar_menu', 'cmgs_my_custom_menu', 1000);
function cmgs_my_custom_menu()
{
    global $wp_admin_bar;
	$value=get_option('soon_page_settings');
    if($value['status']=='0') return;
    // Add Parent Menu
    $argsParent=array(
        'id' => 'myCustomMenu',
        'title' => 'Coming Soon Mode Enable',
		 //'parent' => 'top-secondary',
        'href' => '?page=coming_soon',
		'meta'   => array( 'class' => 'ecs-mode-enable' ),
    );
    $wp_admin_bar->add_menu($argsParent);

   
}


/*
* Construction
*
* This function is render all setting page on coming soon plugin page
*
*/
function construction()
{  
   require "theme_options/webriti_option_pannel.php"; 
}

/*
* Function  my_page_template_redirect
*
* To redirect our site url by coming soon page,after redirecting only our coming soon page can display
*
*/
function my_page_template_redirect()
{
	//get status value from option table for enable or disable coming soon page
	$value=get_option('soon_page_settings');

	//condition matching if status is not disable for coming soon then page is redirect
	if($value['status']!=='0')
	{
		if(!is_feed())
		{
			//if user not login page is redirect on coming soon template page
			if ( !is_user_logged_in()  )
			{
				//get path of our coming soon display page and redirecting
				$file = plugin_dir_path(__FILE__).'template/coming_soon_display.php';
				include($file);
				exit();
			}
		}
		//if user is log-in then we check role.
		if (is_user_logged_in() )
		{
			//get logined in user role
			global $current_user; /* Global variable already have the current user info so need to call get_currentuserinfo or wp_get_current_user */
			// get_currentuserinfo(); /* Function is deprecated in the version 4.5 and above new function wp_get_current_user added */
			$LoggedInUserID = $current_user->ID;
			$UserData = get_userdata( $LoggedInUserID );
			//if user role not 'administrator' redirect him to message page
			if($UserData->roles[0] != "administrator")
			{
				if(!is_feed())
				{
					$file = plugin_dir_path(__FILE__).'template/coming_soon_display.php';
				   include($file);
				   exit();
				}
			}
		}
	}
}

/*
* add action to call function my_page_template_redirect
*/
//   add_action( 'template_redirect', 'my_page_template_redirect' );

		//get page for live preview when my_preview is true,that mean when this page is call on live preview
		if (  (isset($_GET['my_preview']) && $_GET['my_preview'] == 'true') )
		{
			$file = plugin_dir_path(__FILE__).'template/coming_soon_display.php';
			include($file);
			exit();
		}
 
 
 
/*
* We will wrap the template redirect action in this function. We will check if the page is wp-login.php.
  The template redirect action will not be called on login pages. 
*/

add_action('init','ecs_skip_redirect_on_login');

function ecs_skip_redirect_on_login ()

{
	global $pagenow;
	if ('wp-login.php' == $pagenow)
		{
		return;
						
		} else 
			{
			 add_action( 'template_redirect', 'my_page_template_redirect' );
												
			
			}
	

}
		
		
		
/*
* Includes all option panel scripts
*
*/		 
		 
add_action('admin_enqueue_scripts', 'cmgs_admin_enqueue_script');
function cmgs_admin_enqueue_script($hook)
{ 
  if ($hook == 'toplevel_page_coming_soon')
    {

      wp_enqueue_script('innovation-tab',plugin_dir_url( __FILE__ ).'theme_options/js/option-panel-js.js',array('media-upload','jquery-ui-sortable'));    
      wp_enqueue_style('innovation-option',plugin_dir_url( __FILE__ ).'theme_options/css/style-option.css');

      wp_enqueue_style('innovation-optionpanal-dragdrop',plugin_dir_url( __FILE__ ).'theme_options/css/optionpanal-dragdrop.css');
      
      //Custom plugin
      wp_enqueue_script('innovation_admin_js',plugin_dir_url( __FILE__ ).'theme_options/js/my-custom.js');
      wp_enqueue_script ('wff_custom_wp_admin_js');
      wp_enqueue_script('eif_custom_wp_admin_js',plugin_dir_url( __FILE__ ).'theme_options/js/my-custom.js',array('jquery','jquery-ui-tabs'));
      
      //css
      wp_register_style ('wff_custom_wp_admin_css',plugin_dir_url( __FILE__ ). 'theme_options/css/wff-admin.css');
      wp_enqueue_style( 'wff_custom_wp_admin_css' );
  
  }
}	 
		 
		 
?>