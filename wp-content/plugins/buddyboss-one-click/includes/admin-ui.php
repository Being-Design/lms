<?php
// Exit if accessed directly
if (!defined('ABSPATH'))
  exit;

class buddyboss_oneclick_installer {

    var $license_manager = false;
    var $system_status_helper = false;
    var $admin_screen = '';
    var $package_api = 'https://www.buddyboss.com/oneclick/packages_new.php';

    function __construct(){
        $this->hooks();
    }



    /**
     * Register all hooks
     * @since BuddyBoss One Click (1.0.0)
     * */
    function hooks() {

        add_action('init', array($this,"init"));
        //add_action('admin_init', array($this,"init"));admin_init is fired after init, so no need to call this twice

        add_action('init', array($this,"load_package_installer"));
        add_action('admin_menu', array($this,"admin_menu"));
        add_action( 'wp_ajax_bb_oneclick_installer_ajax', array($this,"the_ajax_action_new") );

        add_filter( 'plugin_action_links_' . BUDDYBOSS_ONECLICK_PLUGIN_BASENAME, array( $this, 'plugin_action_links' ) );

        $this->setup_textdomain();
    }

    function init() {
        $this->package_api = apply_filters("buddyboss_oneclick_installer_package_api",$this->package_api);
        
        require_once BUDDYBOSS_ONECLICK_INSTALLER_PLUGIN_DIR . 'includes/license-manager.php';
        $this->license_manager = new buddyboss_onclick_license_manager();
        
        require_once BUDDYBOSS_ONECLICK_INSTALLER_PLUGIN_DIR . 'includes/system-status-helper.php';
        $this->system_status_helper = new buddyboss_onclick_system_status_helper();
    }

    /**
     * Load plugin text domain
     *
     * @since BuddyBoss One Click (1.0.0)
     *
     * @uses sprintf() Format .mo file
     * @uses get_locale() Get language
     * @uses file_exists() Check for language file
     * @uses load_textdomain() Load language file
     */
    public function setup_textdomain(){
        $domain = 'buddyboss-one-click';

        $locale = apply_filters('plugin_locale', get_locale(), $domain);

        //first try to load from wp-contents/languages/plugins/ directory
        load_textdomain($domain, WP_LANG_DIR.'/plugins/'.$domain.'-'.$locale.'.mo');

        //if not found, then load from buddyboss-one-click/languages/ directory
        load_plugin_textdomain( $domain, false, 'buddyboss-one-click/languages/' );
    }

    /**
     * Register admin menus.
     * @since BuddyBoss One Click (1.0.0)
     *
     * */
    function admin_menu() {

        $this->admin_screen = add_submenu_page(
            'buddyboss-settings', __('One Click Installer', 'buddyboss-one-click'), __('One Click Installer', 'buddyboss-one-click'), 'manage_options', 'buddyboss-oneclick-installer', array( $this, 'page_ui' )
        );

    }

    function load_package_installer() {


         @session_start();


         if(isset($_SESSION["bb_oneclick_installing_package"]) AND !empty($_SESSION["bb_oneclick_installing_package"])) {

            $upload_dir = wp_upload_dir();
            $destination_path = dirname($upload_dir["basedir"])."/bb-oneclick-demos/".$_SESSION["bb_oneclick_installing_package"];

            if(file_exists($destination_path."/package-include.php")) {

                require_once($destination_path."/package-include.php");

                //variables which one click will provide.
                $GLOBALS["bb_oneclick_workingdir"] = $destination_path; //this is dir to data exists.
                $GLOBALS["bb_oneclick_workingurl"] = dirname($upload_dir["baseurl"])."/bb-oneclick-demos/".$_SESSION["bb_oneclick_installing_package"]; //this is url to data exists.

                /* Run The Package */
                new buddyboss_importer_package_installer();

                // Force to the installation page.
                if($_GET["page"] == "buddyboss-oneclick-installer") {

                    wp_redirect( admin_url("admin.php?page=buddyboss-one-click-importer") );
                    exit;

                }

            }


         }

    }

    /**
     *  ajax_axtion.
     *
     * */
    function the_ajax_action() {

        error_reporting(0);

         if(!current_user_can("manage_options")) {
            wp_send_json_error ( array(
                "text"=>__("You don't have permission to access this page", 'buddyboss-one-click' ),
                ) );
        }

        if(isset($_POST["oneclick_installer_process_package"]) AND wp_verify_nonce( $_POST["oneclick_installer_process_package"], "oneclick_installer_process_package" )) {

            @session_start();

            set_time_limit(0);

            $package_slug = @$_POST["package_slug"];
            $packages = $this->get_all_demo_packages();

            $_SESSION["bb_oneclick_installing_package"] = ""; //empty this as there is nothing to install

            if(isset($package_slug) AND !empty($packages[$package_slug])) {

                if($this->is_demo_downloaded_new($package_slug)) { // lets unzip it.


                    WP_Filesystem();

                    $upload_dir = wp_upload_dir();
                    $bb_demo_download_dir = dirname($upload_dir["basedir"])."/bb-oneclick-demos/".$package_slug.".zip";
                    $destination_path = dirname($upload_dir["basedir"])."/bb-oneclick-demos/".$package_slug;
                    $this->remove_dir($destination_path); //first remove it for making sure we get latest version.
                    @mkdir($destination_path);
                    $unzipfile = unzip_file( $bb_demo_download_dir , $destination_path);

                    if($unzipfile) {

                        $_SESSION["bb_oneclick_installing_package"] = $package_slug;

                        wp_send_json_success ( array(
                            "text"=>__("Your package has been unpacked, starting installation.","buddyboss-one-click"),
                            "noalert" => "1"
                        ) );

                    } else {
                         wp_send_json_error ( array(
                            "text"=>__("We cannot unzip the demo packages, try again later","buddyboss-one-click"),
                        ) );
                    }


                } else { // lets download it.

                    $upload_dir = wp_upload_dir();
                    $bb_demo_download_dir = dirname($upload_dir["basedir"])."/bb-oneclick-demos/".$package_slug.".zip";
                    @mkdir(dirname($bb_demo_download_dir));
                    unlink($bb_demo_download_dir); //delete if already exists

                    $this->download($packages[$package_slug]["download_url"],$bb_demo_download_dir);
                    
                    wp_send_json_success ( array(
                    "text"=>__("Demo Package is downloaded and ready to install.","buddyboss-one-click"),
                    ) );

                }


            }  else {

             wp_send_json_error ( array(
                "text"=>__("Package not found.","buddyboss-one-click"),
                ) );

            }


        } else {

             wp_send_json_error ( array(
                "text"=>__("Security error try again later.","buddyboss-one-click"),
                ) );

        }



    }
    
    function the_ajax_action_new() {

        error_reporting(0);

         if(!current_user_can("manage_options")) {
            wp_send_json_error ( array(
                "text"=>__("You don't have permission to access this page", 'buddyboss-one-click' ),
                ) );
        }

        if(isset($_POST["oneclick_installer_process_package"]) AND wp_verify_nonce( $_POST["oneclick_installer_process_package"], "oneclick_installer_process_package" )) {

            @session_start();

            set_time_limit(0);

            $package_slug = @$_POST["package_slug"];
            $packages = $this->get_all_demo_packages();

            $_SESSION["bb_oneclick_installing_package"] = ""; //empty this as there is nothing to install

            if(isset($package_slug) AND !empty($packages[$package_slug])) {

                if( $this->is_demo_downloaded_new( $package_slug ) ) { // lets unzip it.

                        $_SESSION["bb_oneclick_installing_package"] = $package_slug;

                        wp_send_json_success ( array(
                            "text"=>__("Your package has been unpacked, starting installation.","buddyboss-one-click"),
                            "noalert" => "1"
                        ) );


                } else { // lets download it.
                    WP_Filesystem();
                    
                    $upload_dir = wp_upload_dir();
                    $destination_path = dirname($upload_dir["basedir"])."/bb-oneclick-demos/".$package_slug;
                    
                    
                    $package_url_base = $packages[$package_slug]["download_url"];
                    $package_url_base = trailingslashit( $package_url_base );
                    
                    /**
                     * We are now downloading multiple files.
                     * To avoid running out of php memory or execution time, we'll download just one file in each request.
                     * Those files can be zip files, which are extracted right after download.
                     * 
                     * We pass '_download_file_num' parameter to indicate which file number we are going to download.
                     * If this is not passed, it is assumed to be a fresh request and we download the config file.
                     */
                    if( isset( $_POST['_download_file_num'] ) ){
                        $download_file_num = absint( $_POST['_download_file_num'] );//0 based index
                        $info_file = $destination_path . '/_config.txt';
                        
                        $files_to_download = file( $info_file, FILE_IGNORE_NEW_LINES );
                        if( !empty( $files_to_download ) ){
                            if( count( $files_to_download ) < ( $download_file_num + 1 ) ){
                                //all files downloaded already, abort
                                wp_send_json_success ( array(
                                    "text"=>__("Demo Package is downloaded and ready to install.","buddyboss-one-click"),
                                ) );
                            }
                            
                            $file_to_download = $files_to_download[ $download_file_num ];
                            $parts = explode( ' ', $file_to_download );
                            $filename = $parts[0];
                            $relative_path = $parts[1];

                            $downloaded_file = $this->download_new( $package_url_base . $filename, $destination_path . $relative_path );

                            //if this is a zip file, extract it and delete the zip file
                            $extension = end( explode( '.', $filename ) );
                            if( 'zip' == $extension ){
                                //xxxx.zip file, if contains xxxx folders and then files inside it,
                                //unzip_file function, for some reason, doesn't create the folder xxxx and instead extracts all file in root folder
                                //we need to workaround that issue
                                if( 'main.zip' != $filename ){
                                    $folder_name = reset( explode( '.', $filename ) );
                                    $unzipfile = unzip_file( $downloaded_file , $destination_path . $relative_path . $folder_name .'/' );
                                } else {
                                    $unzipfile = unzip_file( $downloaded_file , $destination_path . $relative_path );
                                }
                                
                                unlink( $downloaded_file );
                            }

                            $next_download_num = $download_file_num + 1;
                            
                            //check if any more file remaining
                            if( count( $files_to_download ) > $next_download_num ){
                                wp_send_json_success ( array(
                                    "text" => sprintf( __( "Downloaded %s", "buddyboss-one-click" ), $downloaded_file ),
                                    "noalert"   => true,
                                    "continue"  => true,
                                    "nextfile_num"  => $next_download_num,
                                    "totalfile_num" => count( $files_to_download ),
                                ) );
                            } else {
                                //all files downloaded already
                                
                                //1. create _download_complete.txt file to indicate that the package download is complete
                                $fp = fopen( $destination_path . "/_download_complete.txt","wb" );
                                fwrite( $fp, 'done' );
                                fclose( $fp );
                                
                                //2. send response
                                wp_send_json_success ( array(
                                    "text"=>__("Demo Package is downloaded and ready to install.","buddyboss-one-click"),
                                ) );
                            }
                            
                        } else {
                            wp_send_json_error ( array(
                                "text" => __( "Please refresh the page and try again.", "buddyboss-one-click" ),
                            ) );
                        }
                        
                    } else {
                        $this->remove_dir($destination_path); //first remove it for making sure we get latest version.
                        @mkdir($destination_path);
                        
                        //Download the text file which contains info about what all files to download
                        $info_file = $this->download_new( $package_url_base . '_config.txt', $destination_path );
                        
                        $files_to_download = file( $info_file, FILE_IGNORE_NEW_LINES );
                        if( !empty( $files_to_download ) ){
                            wp_send_json_success ( array(
                                "text" => __( "Downloading demo data, this may take some time. Please do not refresh or close this screen.", "buddyboss-one-click" ),
                                "continue"  => true,
                                "nextfile_num" => 0,//first file
                                "totalfile_num" => count( $files_to_download ),
                            ) );
                        } else {
                            wp_send_json_error ( array(
                                "text" => __( "Main package file could not be downloaded. Please contact support.", "buddyboss-one-click" ),
                            ) );
                        }
                    }
                }


            }  else {

             wp_send_json_error ( array(
                "text"=>__("Package not found.","buddyboss-one-click"),
                ) );

            }


        } else {

             wp_send_json_error ( array(
                "text"=>__("Security error try again later.","buddyboss-one-click"),
                ) );

        }



    }

    /**
     * Show action links on the plugin screen.
     *
     * @param	mixed $links Plugin Action links
     * @return	array
     */
    public function plugin_action_links( $links ) {
        $action_links = array(
            'settings' => '<a href="' . admin_url( 'admin.php?page=buddyboss-oneclick-installer' ) . '" title="' . esc_attr( __( 'View BuddyBoss One Click Installer Settings', 'buddyboss-one-click' ) ) . '">' . __( 'Settings', 'buddyboss-one-click' ) . '</a>',
        );

        return array_merge( $links, $action_links );
    }

    /**
     * Load Output the UI Screen of admin panel
     *
     * @since BuddyBoss Click Installer (1.0.0)
     *
     * @uses sprintf() Format .mo file
     * @uses get_locale() Get language
     * @uses file_exists() Check for language file
     * @uses load_textdomain() Load language file
     */
    function page_ui() {

        if(!current_user_can("manage_options")) {
            wp_die( __("You don't have permission to access this page.", 'buddyboss-one-click') );
        }
        
        $nav_tabs = array( 
            'installer'     => __( 'Installer', 'buddyboss-one-click' ), 
            'system-info'   => __( 'System Status', 'buddyboss-one-click' ),
            'support'       => __( 'Support', 'buddyboss-one-click' ),
        );
        $current_nav_tab = isset( $_GET['_tab'] ) && !empty( $_GET['_tab'] ) ? $_GET['_tab'] : 'installer';
        if( !isset( $nav_tabs[$current_nav_tab] ) ){
            $current_nav_tab = 'installer';//default
        }
        ?>

        <div class="wrap">
            <h2><?php _e("BuddyBoss One Click Installer","buddyboss-one-click"); ?></h2>
            
            <h2 class="nav-tab-wrapper">
                <?php 
                $url = admin_url( 'admin.php?page=buddyboss-oneclick-installer' );
                foreach( $nav_tabs as $slug => $name ){
                    $class = $slug == $current_nav_tab ? 'nav-tab nav-tab-active' : 'nav-tab';
                    $iurl = add_query_arg( array( '_tab' => $slug ), $url );
                    echo "<a class='{$class}' href='{$iurl}'>{$name}</a>";
                }
                ?>
            </h2>
            
            <?php 
            switch( $current_nav_tab ){
                case 'system-info':
                    $this->system_status_helper->display_details();
                    break;
                case 'support':
                    $this->license_manager->support_ui();
                    break;
                default:
                    if( $this->license_manager->is_connected() ){
                        $this->license_manager->reconnect_ui();
                        $this->page_ui_installer();
                    } else {
                        $this->license_manager->connect_ui();
                    }
                    break;
            }
            ?>

        </div>
        <?php 
    }


    function page_ui_installer(){
        ?>
        
            <p><?php _e("Please select the demo package you would like to install on this site.","buddyboss-one-click"); ?></p>

            <form method="post" action="javascript:;" id="bb_oneclick_installer_form">

            <?php
            $demo_packages = (array) $this->get_all_demo_packages();

            if(empty($demo_packages)) {
                echo '<p>'.__("Sorry currently there is no demo package available by buddyboss","buddyboss-one-click").'</p>';
            }


            wp_nonce_field( "oneclick_installer_process_package",'oneclick_installer_process_package' );

            ?>


            <style>
               form#bb_oneclick_installer_form {
                    margin-top: 40px;
                }
                .package_details {
                    float: left;
                    width: 30%;
                    margin: 0 3% 4% 0;
                    position: relative;
                    border: 1px solid #ddd;
                    background: #fff;
                    background: rgba(255,255,255,.65);
                    overflow: hidden;
                    -webkit-box-shadow: 0 1px 1px -1px rgba(0,0,0,.1);
                    box-shadow: 0 1px 1px -1px rgba(0,0,0,.1);
                    -webkit-box-sizing: border-box;
                    -moz-box-sizing: border-box;
                    box-sizing: border-box;
                }
                .package_details.install_ready {
                    background: #23282d;
                }
                .package_details .packages_screenshots {
                    overflow: hidden;
                    display: block;
                    position: relative;
                    border-bottom: 1px solid #e2e2e2;
                }
                .package_details.install_ready .packages_screenshots {
                    border-color: #23282d;
                }
                .package_details .packages_screenshots:after {
                    content: "";
                    display: block;
                    padding-top: 66.66666%;
                }
                .package_details .packages_screenshots img {
                    -webkit-transition: all 2s; /* Safari */
                    transition: all 2s;
                    width: 100%;
                    height: auto;
                    position: absolute;
                    top:0px;
                    left: 0px;
                }
                .package_details h4 {
                    float: left;
                    height: 48px;
                    line-height: 48px;
                    font-size: 16px;
                    margin: 0 15px;
                    color: #111;
                    font-weight: 600;
                }
                .package_details.install_ready h4 {
                    color: #fff;
                }
                .package_details .action {
                    background: #f5f5f5;
                    border-left: 1px solid #e2e2e2;
                    visibility: hidden;
                    opacity: 0;
                    padding: 0 13px;
                    height: 48px;
                    position: absolute;
                    right: 0;
                    bottom: 0;
                    z-index: 10;
                }
                .package_details.install_ready .action {
                    background: #2c2e2f;
                    border-color: #23282d;
                }
                .package_details .installbtn {
                    float: right;
                    margin: 10px 0 0 0;
                }
                .package_details .solution {
                    float: right;
                    margin: 10px 4px 0 0;
                }
                .package_details a.btn {
                    font-size: 14px;
                    float: left;
                    text-decoration: none;
                    margin-left: 5px;
                    clear: both;
                }
                .package_details button span.spinner {
                    position: absolute;
                    margin-left: -184px;
                    margin-top: 4px;
                }
                .package_details .overlaybg {
                    width: 100%;
                    height: 100%;
                    position: absolute;
                    bottom: 49px;
                    background: white;
                    opacity: 0;
                    visibility: hidden;
                    -webkit-transition: all 0.5s; /* Safari */
                    transition: all 0.5s;
                }
                .package_details a.btn.demobtn {
                    position: absolute;
                    top: 35%;
                    right: 25%;
                    left: 25%;
                    background: #23282d;
                    background: rgba(0,0,0,.7);
                    color: #fff;
                    font-size: 15px;
                    text-shadow: 0 1px 0 rgba(0,0,0,.6);
                    -webkit-font-smoothing: antialiased;
                    font-weight: 600;
                    padding: 15px 12px;
                    text-align: center;
                    -webkit-border-radius: 3px;
                    border-radius: 3px;
                    visibility: hidden;
                    opacity: 0;
                    z-index: 10;
                }
                .package_details:hover a.btn.demobtn,
                .package_details:hover .action {
                    visibility: visible;
                    opacity: 1;
                }
                .package_details:hover .overlaybg {
                    visibility: visible;
                    opacity: 0.7;
                }
                @media screen and (max-width: 1080px) and (min-width: 581px) {
                    .package_details {
                        width: 45%;
                        margin: 0 2.5% 4%;
                    }
                    .package_details h4 {
                        font-size: 14px;
                    }
                    .package_details .installbtn,
                    .package_details .solution {
                        margin-top: 8px;
                    }
                }
                @media screen and (max-width: 580px) {
                    .package_details {
                        width: 100%;
                        margin: 0 0 4%;
                    }
                    .package_details .installbtn,
                    .package_details .solution {
                        margin-top: 7px;
                    }
                }
            </style>

            <?php foreach($demo_packages as $package_slug => $package): ?>

                <div class="package_details <?php if($this->is_demo_downloaded_new($package_slug)) { ?>install_ready<?php } ?>">

                    <div class="packages_screenshots">
                        <?php
                        foreach($package["screenshots"] as $screenshot): ?>

                            <img src="<?php echo $screenshot; ?>" title="<?php _e("Screenshot","buddyboss-one-click"); ?>" />

                        <?php
                        endforeach; ?>

                    </div>

                        <h4><?php echo $package["package_name"]; ?></h4>

                        <div class="action">
                            <?php if( $this->license_manager->can_download_package( $package ) ):?>
                            
                                <?php if(!$this->is_demo_downloaded_new($package_slug)) { ?>
                                <button name="package_name" data-package-slug="<?php echo $package_slug; ?>" type="button" class="installbtn button button-primary"> <?php _e("Download","buddyboss-one-click"); ?><span class="spinner"></span></button>
                                <?php } else { ?>
                                <button name="package_name" data-package-slug="<?php echo $package_slug; ?>" type="button" class="installbtn button button-primary"> <?php _e("Install Now","buddyboss-one-click"); ?><span class="spinner"></span></button>
                                <?php } ?>
                            
                            <?php endif; ?>

                            <?php if(!empty($package["solution_url"])):?>

                            <a href="<?php echo $package["solution_url"]; ?>" class="solution button" target="_blank"> <?php _e("Purchase","buddyboss-one-click"); ?></a>
                            <?php endif; ?>
                        </div>

                        <?php if(!empty($package["official_demo"])): ?>
                        <a href="<?php echo $package["official_demo"]; ?>" class="demobtn btn" target="_blank"> <?php _e("View Demo","buddyboss-one-click"); ?></a>
                        <?php endif; ?>

                        <div class="overlaybg"></div>

                </div>

            <?php endforeach; ?>

                <input type="hidden" name="package_slug" id="package_slug" value="">
                <input type="hidden" name="action" value="bb_oneclick_installer_ajax">


            </form>

            <script>
                jQuery(document).ready(function(){
                    
                    jQuery(document).on("click",".installbtn",function(){

                        _this = jQuery(this);

                        _this.prop("disabled",true).find(".spinner").addClass('is-active');

                        jQuery("#package_slug").val(_this.data("package-slug"));


                        post = jQuery.post(ajaxurl,jQuery("#bb_oneclick_installer_form").serialize(),function(){},'json');

                        post.done(function(d){
                            _this.prop("disabled",false).find(".spinner").removeClass('is-active');
                            
                            var _continue = false;
                            if( d.success ){
                                if( typeof d.data.continue != 'undefined' && d.data.continue ){
                                    _continue = true;
                                    var nextfile_num = d.data.nextfile_num,//0 based index
                                        totalfile_num = d.data.totalfile_num;
                                    
                                    var percent = ( nextfile_num * 100 ) / totalfile_num;
                                    percent = Math.round( percent );
                                    if( percent < 0 ){
                                        percent = 0;
                                    }
                                    _this.html( percent + '% <span class="spinner"></span>' );
                                    
                                    jQuery("#bb_oneclick_installer_form").find('[name="_download_file_num"]').remove();
                                    jQuery("#bb_oneclick_installer_form").append( "<input type='hidden' name='_download_file_num' value='"+ nextfile_num +"'>" );
                                }
                                
                                if( typeof d.data.noalert == 'undefined' ){
                                    alert( d.data.text );
                                }
                                
                                if( _continue ){
                                    window.setTimeout( function(){ _this.trigger('click'); }, 500);
                                    return false;
                                } else {
                                    location.reload();
                                    return false;
                                }
                            } else {
                                alert( d.data.text );
                            }
                        });

                        post.fail(function(d){
                            _this.prop("disabled",false).find(".spinner").removeClass('is-active');
                            alert( '<?php _e( "Unknown Server Error.", "buddyboss-one-click" );?>' );
                        });

                    });

                });

            </script>
        <?php 
    }
    
    function get_all_demo_packages() {

        $oneclick_demo_packages = get_transient( "buddyboss_oneclick_demo_packages" );

        if(!empty($oneclick_demo_packages)) {
            return $oneclick_demo_packages;
        }

        $data = $this->get_remote_content($this->package_api);

        $data = json_decode($data,true);

        set_transient( "buddyboss_oneclick_demo_packages", $data, 1 * HOUR_IN_SECONDS );

        return $data;

    }

    function get_remote_content($url,$post_data=array()) {

       if ( ! function_exists( 'curl_init' ) || ! function_exists( 'curl_exec' ) ) {
           echo '<div class="error"><p><strong>' . __( 'ERROR:', 'buddyboss-one-click') . '</strong> ' . __("PHP's CURL extension is not available. Please contact your hosting provider to enable PHP's CURL extension.", "buddyboss-one-click") . '</p></div>';
           return;
       }

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post_data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        //for kemarise @todo:remove on production
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($curl, CURLOPT_USERPWD, "dev:n9kWcOpv");

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    function remove_dir($dir) {
       if (is_dir($dir)) {
         $objects = scandir($dir);
         foreach ($objects as $object) {
           if ($object != "." && $object != "..") {
             if (is_dir($dir."/".$object))
               $this->remove_dir($dir."/".$object);
             else
               unlink($dir."/".$object);
           }
         }
         rmdir($dir);
       }
     }

     /**
      * @since BuddyBoss One Click (1.0.0)
      * Tells if demo is downloaded or not
      * */
     function is_demo_downloaded($package_slug) {

            $upload_dir = wp_upload_dir();
            $bb_demo_download_dir = dirname($upload_dir["basedir"])."/bb-oneclick-demos/".$package_slug.".zip";
            if(file_exists($bb_demo_download_dir)) {
                return true;
            }

            return false;

     }
     
     function is_demo_downloaded_new($package_slug) {
         $upload_dir = wp_upload_dir();
         
         $flag = dirname( $upload_dir["basedir"] ) . "/bb-oneclick-demos/" . $package_slug . "/_download_complete.txt";
         if( file_exists( $flag ) ) {
             return true;
         }
         
         return false;
     }

     /**
      * @since BuddyBoss One Click (1.0.0)
      * Download big files.
      * */

    function download($file_source, $file_target) {
        
        $download = download_url($file_source);

        if(empty($download)){
            return false;
        } else {

            @rename($download,$file_target);

        }

        return true;
    }

    function download_new($file_source, $file_target) {
        $name_parts = explode( '/', $file_source );
        $filename = end( $name_parts );
        
        $download = download_url( $file_source );

        if(empty($download)){
            return false;
        } else {
            //create target folder, if it not exists
            if( !is_dir( $file_target ) ){
                mkdir( $file_target, 0777, true );
            }
            
            @rename( $download, trailingslashit( $file_target ) . $filename );
            return trailingslashit( $file_target ) . $filename;
        }

        return true;
    }
}

/**
 * Delete packages
 * @return void
 */
function buddyboss_oneclick_installer_delete_packages() {
        @session_start();

        $upload_dir = wp_upload_dir();

        if(!empty($_SESSION["bb_oneclick_installing_package"])){
            $package_dir = dirname($upload_dir["basedir"])."/bb-oneclick-demos/".$_SESSION["bb_oneclick_installing_package"]; //this is url to data exists.

            $package_zip = dirname($upload_dir["basedir"])."/bb-oneclick-demos/".$_SESSION["bb_oneclick_installing_package"].".zip"; //this is url to data exists.
            if(is_dir($package_dir)) {

                foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($package_dir, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $path) {
                    $path->isDir() && !$path->isLink() ? rmdir($path->getPathname()) : unlink($path->getPathname());
                }
                rmdir($package_dir);

            }
            if(is_file($package_zip)) {
                unlink($package_zip);
            }
            $_SESSION["bb_oneclick_installing_package"] = null;
            unset($_SESSION["bb_oneclick_installing_package"]);
        }
    }

?>
