<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Profile_Magic
 * @subpackage Profile_Magic/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Profile_Magic
 * @subpackage Profile_Magic/public
 * @author     Your Name <email@example.com>
 */
class Profile_Magic_Public {

        /**
         * The ID of this plugin.
         *
         * @since    1.0.0
         * @access   private
         * @var      string    $profile_magic    The ID of this plugin.
         */
        private $profile_magic;

        /**
         * The version of this plugin.
         *
         * @since    1.0.0
         * @access   private
         * @var      string    $version    The current version of this plugin.
         */
        private $version;

        /**
         * Initialize the class and set its properties.
         *
         * @since    1.0.0
         * @param      string    $profile_magic       The name of the plugin.
         * @param      string    $version    The version of this plugin.
         */
        public function __construct( $profile_magic, $version ) {
                $dbhandler = new PM_DBhandler;
                $this->profile_magic = $profile_magic;
                $this->version = $version;
                $this->pm_theme  = $dbhandler->get_global_option_value('pm_style','default');

        }

        /**
         * Register the stylesheets for the public-facing side of the site.
         *
         * @since    1.0.0
         */
        public function enqueue_styles() 
        {
             $dbhandler = new PM_DBhandler;
            /**
             * This function is provided for demonstration purposes only.
             *
             * An instance of this class should be passed to the run() function
             * defined in Profile_Magic_Loader as all of the hooks are defined
             * in that particular class.
             *
             * The Profile_Magic_Loader will then create the relationship
             * between the defined hooks and the functions defined in this
             * class.
             */
            global $wp_scripts;
            // tell WordPress to load jQuery UI tabs
            wp_enqueue_script('jquery-ui-tabs');
            wp_enqueue_style( 'jquery-ui-styles' );
            wp_enqueue_style( $this->profile_magic, plugin_dir_url( __FILE__ ) . 'css/profile-magic-public.css', array(), $this->version, 'all' );
            wp_enqueue_style('jquery.Jcrop.css', plugin_dir_url( __FILE__ ) . 'css/jquery.Jcrop.css', array(), $this->version, 'all' );
            wp_enqueue_style( 'pm-emoji-picker', plugin_dir_url( __FILE__ ) . 'css/emoji.css', array(), $this->version, 'all' );
            wp_enqueue_style( 'pm-emoji-picker-nanoscroller', plugin_dir_url( __FILE__ ) . 'css/nanoscroller.css', array(), $this->version, 'all' );
            if($dbhandler->get_global_option_value('pm_theme_type','light')=='dark')
            {
                wp_enqueue_style( 'pg-dark-theme', plugin_dir_url( __FILE__ ) . 'css/pg-dark-theme.css', array(), $this->version, 'all' );
            }
        }

        /**
         * Register the JavaScript for the public-facing side of the site.
         *
         * @since    1.0.0
         */
        public function enqueue_scripts() {

                /**
                 * This function is provided for demonstration purposes only.
                 *
                 * An instance of this class should be passed to the run() function
                 * defined in Profile_Magic_Loader as all of the hooks are defined
                 * in that particular class.
                 *
                 * The Profile_Magic_Loader will then create the relationship
                 * between the defined hooks and the functions defined in this
                 * class.
                 */
                $dbhandler = new PM_DBhandler;
		$lang = $dbhandler->get_global_option_value('pm_recaptcha_lang','en');
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_script('jquery-ui-accordion');
		wp_enqueue_script('jquery-ui-dialog');
                wp_enqueue_script('jquery-ui-autocomplete');
                
                wp_enqueue_script( 'profile-magic-nanoscroller.js', plugin_dir_url( __FILE__ ) . 'js/nanoscroller.min.js', array( 'jquery' ), $this->version, true );
                wp_enqueue_script( 'profile-magic-tether.js', plugin_dir_url( __FILE__ ) . 'js/tether.min.js', array( 'jquery' ), $this->version, true );
                wp_enqueue_script( 'profile-magic-emoji-config.js', plugin_dir_url( __FILE__ ) . 'js/config.js', array( 'jquery' ), $this->version, true );
                wp_enqueue_script( 'profile-magic-emoji-util.js', plugin_dir_url( __FILE__ ) . 'js/util.js', array( 'jquery' ), $this->version, true );
                wp_enqueue_script( 'profile-magic-emojiarea.js', plugin_dir_url( __FILE__ ) . 'js/jquery.emojiarea.js', array( 'jquery' ), $this->version, true );
                wp_enqueue_script( 'profile-magic-emoji-picker.js', plugin_dir_url( __FILE__ ) . 'js/emoji-picker.js', array( 'jquery' ), $this->version, true );

                wp_enqueue_media();
                wp_enqueue_script('jquery-form');
                wp_enqueue_script('jcrop');
		wp_enqueue_script('jquery-ui-tooltip');
		wp_enqueue_script('jquery-effects-core');
		wp_enqueue_script("crf-recaptcha-api","https://www.google.com/recaptcha/api.js?hl=$lang");
		wp_enqueue_script( $this->profile_magic, plugin_dir_url( __FILE__ ) . 'js/profile-magic-public.js', array( 'jquery' ), $this->version, false );
                wp_enqueue_script( 'modernizr-custom.min.js', plugin_dir_url( __FILE__ ) . 'js/modernizr-custom.min.js', array( 'jquery' ), $this->version, false );
                wp_enqueue_script( 'profile-magic-footer.js', plugin_dir_url( __FILE__ ) . 'js/profile-magic-footer.js', array( 'jquery' ), $this->version, true );
                wp_enqueue_script( 'profile-magic-friends-public.js', plugin_dir_url( __FILE__ ) . 'js/profile-magic-friends-public.js', array( 'jquery' ), $this->version, false );
                wp_enqueue_script('heartbeat');

                wp_localize_script( $this->profile_magic, 'pm_ajax_object',array( 'ajax_url' => admin_url( 'admin-ajax.php'),'plugin_emoji_url'=>plugin_dir_url( __FILE__ ).'partials/images/img') );
                $error = array();
                $error['valid_email'] = __('Please enter a valid e-mail address.','profile-grid');
                $error['valid_number'] = __('Please enter a valid number.','profile-grid');
                $error['valid_date'] = __('Please enter a valid date(yyyy-mm-dd format).','profile-grid');
                $error['required_field'] = __('This is a required field.','profile-grid');
                $error['file_type'] = __('This file type is not allowed.','profile-grid');
                $error['short_password'] = __('Your password should be at least 7 characters long.','profile-grid');
                $error['pass_not_match'] = __('Password and confirm password do not match.','profile-grid');
                $error['user_exist'] = __('Sorry, username already exist.','profile-grid');
                $error['email_exist'] = __('Sorry, email already exist.','profile-grid');
                $error['show_more'] = __('More...','profile-grid');
                $error['show_less'] = __('Show less','profile-grid');
                $error['user_not_exit'] = __('Username does not exist.','profile-grid');
                $error['password_change_successfully'] =  __('Password changed Successfully','profile-grid');
                $error['allow_file_ext'] = $dbhandler->get_global_option_value('pm_allow_file_types','jpg|jpeg|png|gif');	
                $error['valid_phone_number'] = __('Please enter a valid phone number.','profile-grid');
        	$error['valid_mobile_number'] = __('Please enter a valid mobile number.','profile-grid');
                $error['valid_facebook_url'] = __('Please enter a valid Facebook url.','profile-grid');
                $error['valid_twitter_url'] = __('Please enter a Twitter url.','profile-grid');
                $error['valid_google_url'] = __('Please enter a valid Google url.','profile-grid');
                $error['valid_linked_in_url'] = __('Please enter a Linked In url.','profile-grid');
                $error['valid_youtube_url'] = __('Please enter a valid Youtube url.','profile-grid');
                $error['valid_instagram_url'] = __('Please enter a valid Instagram url.','profile-grid');
                wp_localize_script( $this->profile_magic, 'pm_error_object',$error);



        }

        public function register_shortcodes()
        {
                add_shortcode( 'PM_Registration', array( $this, 'profile_magic_registration_form' ) );
                add_shortcode( 'PM_Group', array( $this, 'profile_magic_group_view' ) );
                add_shortcode( 'PM_Groups', array( $this, 'profile_magic_groups_view' ) );
                add_shortcode( 'PM_Login', array( $this, 'profile_magic_login_form' ) );
                add_shortcode( 'PM_Profile', array( $this, 'profile_magic_profile_view' ) );
                add_shortcode( 'PM_Forget_Password', array( $this, 'profile_magic_forget_password' ) );
                add_shortcode( 'PM_Password_Reset_Form', array( $this, 'profile_magic_password_reset_form' ) );
                add_shortcode( 'PM_Search', array( $this, 'profile_magic_user_search' ) );
                add_shortcode( 'PM_Messenger', array( $this, 'profile_magic_messenger' ) );
             
                
                add_shortcode( 'PM_Blogs', array( $this, 'profile_magic_user_blogs' ) );
                add_shortcode( 'PM_Add_Blog', array( $this, 'profile_magic_add_blog' ) );
        }

        private function profile_magic_get_template_html($template_name,$content,$attributes = null) 
        {
                if ( ! $attributes )$attributes = array();
                ob_start();
                do_action( 'profile_magic_before_' . $template_name );
                require( 'partials/' . $template_name . '.php');
                do_action( 'profile_magic_after_' . $template_name );
                $html = ob_get_contents();
                ob_end_clean();
                return $html;
        }

        private function profile_magic_get_pm_theme_tmpl($type,$gid,$fields)
        {
                $path = $this->profile_magic_get_pm_theme($type);
                require($path);
        }

        public function profile_magic_get_pm_theme($type)
        {

            $plugin_path = plugin_dir_path( __FILE__ );
           $wp_theme_dir = get_template_directory();
            $override_pm_theme_path = $wp_theme_dir . "/profilegrid-user-profiles-groups-and-communities/themes/";
            $override_pm_theme = $override_pm_theme_path.$this->pm_theme.'/'.$type.'.php';
            $default_pm_theme = $plugin_path.'partials/themes/'.$this->pm_theme.'/'.$type.'.php';
            if(file_exists($override_pm_theme))
            {
                $path = $override_pm_theme;
            }
            else if(file_exists($default_pm_theme))
            {
                $path = $default_pm_theme;
            }
            else
            {
                $path = $plugin_path.'partials/themes/default/'.$type.'.php';
            }

            return $path;
        }
        public function profile_magic_messenger($content)
        {
            return $this->profile_magic_get_template_html('profile-magic-messenger', $content);
        }

        public function profile_magic_add_blog($content)
        {
            $dbhandler = new PM_DBhandler;
            if($dbhandler->get_global_option_value('pm_enable_blog','1')==1):
                return $this->profile_magic_get_template_html( 'profile-magic-add-blog', $content );	
            else:
               return  '<div class="pm-login-box-error">'.__('Admin has disabled blog submissions. You cannot submit a new blog post at the moment.','profile-grid').'</div>';
            endif;
            
        }
        
        public function profile_magic_user_search($content)
        {
            return $this->profile_magic_get_template_html( 'profile-magic-search', $content );	
        }

        public function profile_magic_login_form($attributes,$content = null)
        {
                return $this->profile_magic_get_template_html( 'profile-magic-login-form', $content,$attributes );
        }
        public function profile_magic_registration_form($content)
        {
                return $this->profile_magic_get_template_html( 'profile-magic-registration-form', $content );
        }

        public function profile_magic_group_view($content)
        {	
                return $this->profile_magic_get_template_html( 'profile-magic-group', $content );	
        }

        public function profile_magic_groups_view($content)
        {
                return $this->profile_magic_get_template_html('profile-magic-groups',$content);	
        }

        public function profile_magic_profile_view($content)
        {
                return $this->profile_magic_get_template_html( 'profile-magic-profile', $content );
        }

        public function profile_magic_forget_password($attributes,$content = null)
        {
                $default_attributes = array( 'show_title' => false );
        $attributes = shortcode_atts( $default_attributes, $attributes );
                $attributes['lost_password_sent'] = isset( $_REQUEST['checkemail'] ) && $_REQUEST['checkemail'] == 'confirm';
                return $this->profile_magic_get_template_html( 'profile-magic-forget-password', $content );
        }

        public function profile_magic_password_reset_form( $attributes, $content = null ) 
        {
                // Parse shortcode attributes
                $pmrequests = new PM_request;
                $default_attributes = array( 'show_title' => false );
                $attributes = shortcode_atts( $default_attributes, $attributes );

                if ( is_user_logged_in() ) 
                {
                        return __( 'You are already signed in.','profile-grid' );
                } 
                else 
                {
                        if ( isset( $_REQUEST['login'] ) && isset( $_REQUEST['key'] ) ) 
                        {
                                $attributes['login'] = $_REQUEST['login'];
                                $attributes['key'] = $_REQUEST['key'];
                                // Error messages
                                $errors = array();
                                if ( isset( $_REQUEST['error'] ) ) 
                                {
                                        $error_codes = explode( ',', $_REQUEST['error'] );
                                        foreach ( $error_codes as $code ) 
                                        {
                                                $errors []= $pmrequests->profile_magic_get_error_message($code,$this->profile_magic);
                                        }
                                }
                                $attributes['errors'] = $errors;

                                return $this->profile_magic_get_template_html( 'profile-magic-password-reset-form', $content, $attributes );
                        } 
                        else 
                        {
                                return __( 'Invalid password reset link.','profile-grid' );
                        }
                }
        }

        public function profile_magic_do_password_reset()
        {
                $pmrequests = new PM_request;
                if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) 
                {
                        $rp_key = $_REQUEST['rp_key'];
                        $rp_login = $_REQUEST['rp_login'];

                        $user = check_password_reset_key( $rp_key, $rp_login );

                        if ( ! $user || is_wp_error( $user ) ) {

                                if ( $user && $user->get_error_code() === 'expired_key' ) {
                                        $redirect_url = $pmrequests->profile_magic_get_frontend_url('pm_user_login_page',site_url('/wp-login.php'));
                                        $redirect_url = add_query_arg( 'errors','expiredkey', $redirect_url );
                                } 
                                else 
                                {
                                        $redirect_url = $pmrequests->profile_magic_get_frontend_url('pm_user_login_page',site_url('/wp-login.php'));
                                        $redirect_url = add_query_arg( 'errors','invalidkey', $redirect_url );
                                }
                                wp_redirect( $redirect_url );
                exit;
                        }

                        if ( isset( $_POST['pass1'] ) ) 
                        {
                                if ( $_POST['pass1'] != $_POST['pass2'] ) 
                                {
                                        // Passwords don't match
                                        $redirect_url = $pmrequests->profile_magic_get_frontend_url('pm_password_reset_page',site_url('/wp-login.php'));
                                        $redirect_url = add_query_arg( 'key', $rp_key, $redirect_url );
                                        $redirect_url = add_query_arg( 'login', $rp_login, $redirect_url );
                                        $redirect_url = add_query_arg( 'error','password_reset_mismatch', $redirect_url );
                                        wp_redirect( $redirect_url );
                                        exit;
                                }

                                if ( empty( $_POST['pass1'] ) ) {
                                        // Password is empty
                                        $redirect_url = $pmrequests->profile_magic_get_frontend_url('pm_password_reset_page',site_url('/wp-login.php'));
                                        $redirect_url = add_query_arg( 'key', $rp_key, $redirect_url );
                                        $redirect_url = add_query_arg( 'login', $rp_login, $redirect_url );
                                        $redirect_url = add_query_arg( 'error', 'password_reset_empty', $redirect_url );
                                        wp_redirect( $redirect_url );
                                        exit;
                                }


                                if(strlen($_POST['pass1'])<7)
                                {
                                        $redirect_url = $pmrequests->profile_magic_get_frontend_url('pm_password_reset_page',site_url('/wp-login.php'));
                                        $redirect_url = add_query_arg( 'key', $rp_key, $redirect_url );
                                        $redirect_url = add_query_arg( 'login', $rp_login, $redirect_url );
                                        $redirect_url = add_query_arg( 'error', 'password_too_short', $redirect_url );
                                        wp_redirect( $redirect_url );
                                        exit;
                                }		


                                // Parameter checks OK, reset password
                                reset_password( $user, $_POST['pass1'] );
                                $redirect_url = $pmrequests->profile_magic_get_frontend_url('pm_user_login_page',site_url('/wp-login.php'));
                                $redirect_url = add_query_arg( 'password','changed', $redirect_url );
                                wp_redirect($redirect_url);
                                exit;
                        } else {
                                echo "Invalid request.";
                        }

                        exit;
                }
        }

        public function profile_magic_send_email_after_password_reset($user, $new_pass)
        {
            $pmrequests = new PM_request;
            $pmemail = new PM_Emails;
            $userid = $user->ID;
            $newpass = $pmrequests->pm_encrypt_decrypt_pass('encrypt',$new_pass);
            update_user_meta( $userid,'user_pass',$newpass);
            $gid = $pmrequests->profile_magic_get_user_field_value($userid,'pm_group');
            if(isset($gid))
            {
                $pmemail->pm_send_group_based_notification($gid,$userid,'on_password_change');
            }
        }

        public function profile_magic_do_password_lost()
        {
                $pmrequests = new PM_request;
                if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) 
                {
                        $errors = retrieve_password();
                        if ( is_wp_error( $errors ) ) {
                                // Errors found
                                $redirect_url = $pmrequests->profile_magic_get_frontend_url('pm_forget_password_page',site_url('/wp-login.php?action=lostpassword'));
                                $redirect_url = add_query_arg( 'errors', join( ',', $errors->get_error_codes() ), $redirect_url );
                        } else {
                                // Email sent
                                $redirect_url = $pmrequests->profile_magic_get_frontend_url('pm_user_login_page',site_url('/wp-login.php'));
                                $redirect_url = add_query_arg( 'checkemail', 'confirm', $redirect_url );
                        }
                        wp_redirect( $redirect_url );
                        exit;
                }	
        }

        public function profile_magice_retrieve_password_message( $message, $key, $user_login, $user_data ) 
        {
                // Create new message
                $msg  = __( 'Hello!','profile-grid' ) . "\r\n\r\n";
                $msg .= sprintf( __( 'You asked us to reset your password for your account using the email address %s.','profile-grid' ), $user_login ) . "\r\n\r\n";
                $msg .= __( "If this was a mistake, or you didn't ask for a password reset, just ignore this email and nothing will happen.",'profile-grid' ) . "\r\n\r\n";
                $msg .= __( 'To reset your password, visit the following address:','profile-grid' ) . "\r\n\r\n";
                $msg .= site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' ) . "\r\n\r\n";
                $msg .= __( 'Thanks!','profile-grid' ) . "\r\n";

                return $msg;
        }

        public function profile_magic_redirect_to_password_reset()
        {
                $pmrequests = new PM_request;
                if ( 'GET' == $_SERVER['REQUEST_METHOD'] ) {
                        // Verify key / login combo
                        $user = check_password_reset_key( $_REQUEST['key'], $_REQUEST['login'] );
                        if ( ! $user || is_wp_error( $user ) ) 
                        {
                                if ( $user && $user->get_error_code() === 'expired_key' ) {
                                        $redirect_url = $pmrequests->profile_magic_get_frontend_url('pm_user_login_page',site_url('/wp-login.php'));
                                        $redirect_url = add_query_arg( 'login','expiredkey', $redirect_url );
                                } 
                                else 
                                {
                                        $redirect_url = $pmrequests->profile_magic_get_frontend_url('pm_user_login_page',site_url('/wp-login.php'));
                                        $redirect_url = add_query_arg( 'login','invalidkey', $redirect_url );
                                }
                                wp_redirect( $redirect_url );
                                exit;
                        }

                        $redirect_url = $pmrequests->profile_magic_get_frontend_url('pm_password_reset_page',site_url('/wp-login.php?action=lostpassword'));
                        $redirect_url = add_query_arg( 'login', esc_attr( $_REQUEST['login'] ), $redirect_url );
                        $redirect_url = add_query_arg( 'key', esc_attr( $_REQUEST['key'] ), $redirect_url );

                        wp_redirect( $redirect_url );
                        exit;
                }	
        }

        public function profile_magic_lost_password_form()
        {
                $pmrequests = new PM_request;
                $url = $pmrequests->profile_magic_get_frontend_url('pm_forget_password_page',site_url('/wp-login.php?action=lostpassword'));
                wp_redirect($url);
                exit;
        }

        public function profile_magic_check_login_status( $user_login, $user )
        {
                // Get user meta
                $pmrequests = new PM_request;
                $disabled = get_user_meta( $user->ID, 'rm_user_status', true );


                // Is the use logging in disabled?
                if ($disabled == '1') 
                {
                        // Clear cookies, a.k.a log user out
                         wp_clear_auth_cookie();
                        // Build login URL and then redirect
                        $login_url = $pmrequests->profile_magic_get_frontend_url('pm_user_login_page',site_url('/wp-login.php'));

                        $login_url = add_query_arg( 'disabled', '1', $login_url );
                        $gid = get_user_meta( $user->ID, 'pm_group', true );

                        $payment_status = get_user_meta( $user->ID, 'pm_user_payment_status', true );
                        $price = $pmrequests->profile_magic_check_paid_group($gid);
                        if($price>0 && $payment_status=='pending')
                        {
                                $login_url = add_query_arg( 'errors','payment_pending', $login_url );
                                $login_url = add_query_arg( 'id',$user->ID, $login_url );	
                        }
                        else
                        {
                                $login_url = add_query_arg( 'errors', 'account_disabled', $login_url );	
                        }

                        wp_redirect( $login_url );exit;
                }
                update_user_meta( $user->ID, 'pm_last_login', time() );
                update_user_meta( $user->ID, 'pm_login_status',1 );
        }
        
        public function profile_magic_update_logout_status()
        {
            $current_user = wp_get_current_user(); 
            update_user_meta( $current_user->ID, 'pm_login_status',0 );
        }
        public function profile_magic_login_notice( $message ) 
        {
                // Show the error message if it seems to be a disabled user
            $pmrequests = new PM_request;
                if ( isset( $_GET['disabled'] ) && $_GET['disabled'] == 1 ) 
                        $message =  '<div id="login_error">' . __( 'Account disabled','profile-grid') . '</div>';
                if ( isset( $_GET['errors'] )) 
                        $message =  '<div id="login_error">' .$pmrequests->profile_magic_get_error_message( $_GET['errors'],'profile-grid'). '</div>';
                if ( isset( $_GET['activated'] ) && $_GET['activated']=='success') 
                        $message =  '<div class="message">' .__('Your account has been successfully activated.','profile-grid'). '</div>';
                return $message;
        }

        public function profile_magic_default_registration_url($default_registration_url)
        {
                $pmrequests = new PM_request;
                $register_url = $pmrequests->profile_magic_get_frontend_url('pm_default_regisration_page',site_url('/wp-login.php?action=register'));
                return $register_url;
        }

        public function profile_magic_redirect_after_login( $redirect_to, $request, $user ) 
        {
                //is there a user to check?
                $pmrequests = new PM_request;
                $pm_redirect_after_login = $pmrequests->profile_magic_get_frontend_url('pm_redirect_after_login',$redirect_to);
                if( $user && is_object( $user ) && is_a( $user, 'WP_User' ) ) 
                {
                        if( $user->has_cap( 'administrator' ) ) 
                        {
                                $pm_redirect_after_login = admin_url();
                        } 
                }
                return $pm_redirect_after_login;
        }

        public function profile_magic_get_avatar($avatar,$id_or_email, $size, $default, $alt,$args)
        {
                        $pmrequests = new PM_request;
                        if ( is_numeric( $id_or_email ) ) 
                        {
                                $id = (int) $id_or_email;
                                $user = get_user_by( 'id' , $id );
                        } 
                        elseif ( is_object( $id_or_email ) ) 
                        {
                                if ( ! empty( $id_or_email->user_id ) ) 
                                {
                                        $id = (int) $id_or_email->user_id;
                                        $user = get_user_by( 'id' , $id );
                                }

                        } 
                        else 
                        {
                                $user = get_user_by( 'email', $id_or_email );	
                }

                        if(isset($user) && !empty($user))$avatarid = $pmrequests->profile_magic_get_user_field_value($user->data->ID,'pm_user_avatar');
                        if(isset($avatarid) && $avatarid!='')
                        {
                                 $pm_avatar =  wp_get_attachment_image($avatarid,array($size,$size),false,$args);
                                 if(!empty($pm_avatar))
                                 {
                                        return $pm_avatar;	 
                                 }
                                 else
                                 {
                                         return $avatar;
                                 }
                        }
                        else
                        {
                                return $avatar;	
                        }	
        }

        public function pm_update_user_profile()
        {
                echo update_user_meta($_POST['user_id'],$_POST['user_meta'],$_POST['user_meta_value']);
        die;
        }

        public function pm_send_change_pass_email()
        {
            $userid = $_POST['userid'];
            $pmrequests = new PM_request;
            $pmemail = new PM_Emails;
            $gid = $pmrequests->profile_magic_get_user_field_value($userid,'pm_group');
            $pmemail->pm_send_group_based_notification($gid,$userid,'on_password_change');
            die;
        }


                public function pm_advance_search_get_search_fields_by_gid()
        {   
              $gid =  filter_input(INPUT_POST, 'gid');       
              $match_fields= filter_input(INPUT_POST, 'match_fields');   
              $dbhandler = new PM_DBhandler;
             
              if($gid==''){
                $additional = " field_type not in('file', 'user_avatar', 'heading', 'paragraph', 'confirm_pass', 'user_pass','user_url','user_name')";
               $fields = $dbhandler->get_all_result('FIELDS','*',1,'results',0,false,'ordering',false,$additional); 
              }else{
                   $additional = "and field_type not in('file', 'user_avatar', 'heading', 'paragraph', 'confirm_pass', 'user_pass','user_url','user_name')";
                $fields =  $dbhandler->get_all_result('FIELDS','*',array('associate_group'=>$gid),'results',0,false,'ordering',false,$additional);  
              }
                  $resp =" ";
                 foreach ($fields as $field) {
                     $ischecked = " ";
                     if ($field->field_options != "")
                            $field_options = maybe_unserialize($field->field_options);
                       
                     if(in_array($field->field_key,$match_fields)||$field->field_key==$match_fields){
                                 $ischecked = "checked";
                            }else{
                                $ischecked = " ";
                            }
                                 if (isset($field_options['display_on_search']) && ($field_options['display_on_search'] == 1))
                                     {
                               $resp .=" <li class=\"pm-filter-item\"><input class=\"pm-filter-checkbox\" type=\"checkbox\" name=\"match_fields\" onclick=\"pm_advance_user_search()\" ".$ischecked." value=\"".$field->field_key."\" ><span class=\"pm-filter-value\">".__($field->field_name, 'profile-grid')."</span></li>";
                        }
                        
                    
                }
                 echo $resp;
                die;
            
        }
        
        public function pm_messenger_show_thread_user(){
            $pmmessenger = new PM_Messenger();
            $uid = filter_input(INPUT_POST, 'uid'); 
            $return = $pmmessenger->pm_messenger_show_thread_user($uid);
             $return = json_encode($return);
            echo $return;
            die;
        }


        public function pm_messenger_show_threads(){
            $pmmessenger = new PM_Messenger();
             $active_tid = filter_input(INPUT_POST, 'tid');
            $result = $pmmessenger->pm_messenger_show_threads($active_tid);
            echo $result; die;
          
        }

        public function pm_messenger_send_new_message(){
            $pmmessenger = new PM_Messenger();    
            if(isset($_POST)){
                $rid = filter_input(INPUT_POST, 'rid');
                $content = filter_input(INPUT_POST, 'content');
                $result = $pmmessenger->pm_messenger_send_new_message($rid,$content);
                echo $result;
            }else{
                echo " no post created";
            }
            die;
        }

        
        public function pm_messenger_show_messages(){
            $pmmessenger = new PM_Messenger();
            $tid = filter_input(INPUT_POST, 'tid');  
            $t_status = filter_input(INPUT_POST, 't_status'); 
            $loadnum = filter_input(INPUT_POST, 'loadnum');
            $last_mid = filter_input(INPUT_POST, 'last_mid');
            $timezone = filter_input(INPUT_POST, 'timezone');
            $return = $pmmessenger->pm_messenger_show_messages($tid, $t_status, $loadnum,$last_mid,$timezone);
            echo $return;
            die;
        }
        
  
           public function pm_get_messenger_notification()
	{
                $pmmessenger = new PM_Messenger();
		$timestamp = filter_input(INPUT_GET, 'timestamp');		
		$activity =  filter_input(INPUT_GET, 'activity');
                $tid =  filter_input(INPUT_GET, 'tid');
                $return = $pmmessenger->pm_get_messenger_notification($timestamp, $activity, $tid);
		echo $return;
               die;
	}
        
     

        
    public function pm_messenger_delete_threads(){
            $pmmessenger = new PM_Messenger();
            $tid = filter_input(INPUT_POST, 'tid');
            $return = $pmmessenger->pm_messenger_delete_threads($tid);
            echo $return;
            die;
            }
            
    public function pm_messenger_notification_extra_data(){
            $pmmessenger = new PM_Messenger();
            $return = $pmmessenger->pm_messenger_notification_extra_data();
            echo $return;
            die;
    }

    public function pm_autocomplete_user_search(){
    $dbhandler = new PM_DBhandler;
    $pmrequests = new PM_request;
    $uid = wp_get_current_user()->ID;
    $name = filter_input(INPUT_POST, 'name');
    $user_group = get_user_meta($uid,'pm_group',true);
    $meta_args = array('status'=>'0');
    $search =$name; 
    $limit = 20;
    $meta_query_array = $pmrequests->pm_get_user_meta_query($meta_args);
    $users =  $dbhandler->pm_get_all_users($search,$meta_query_array,'',0,$limit,'ASC','ID',array());
    $return=array();      
    if(!empty($users)){
                
            foreach($users as $user)
                { 
                if($user->ID!=$uid){
                $user_info['id']=$user->ID;
                $user_info['label']=$user->user_login;
                $return[]=$user_info;
                }
                }
          }
          $data = json_encode($return);
          echo $data;
          die;
}

   public function pm_advance_user_search()
        {  
            $dbhandler = new PM_DBhandler;
            $pmrequests = new PM_request;
            $pagenum = filter_input(INPUT_POST, 'pagenum');
            $gid = filter_input(INPUT_POST, 'gid');
               
            if(isset($_POST['match_fields']))
            {
           
                $search = '';
                $meta_query_array = $pmrequests->pm_get_user_advance_search_meta_query($_POST);
                 
            }
            else
            {
                $search =$_POST['pm_search'];    
                $meta_query_array = $pmrequests->pm_get_user_meta_query($_POST);
            
            }
                
          

            $current_user = wp_get_current_user();
            $pagenum = isset($pagenum) ? absint($pagenum) : 1;
            $limit = 20; // number of rows in page
            $offset = ( $pagenum - 1 ) * $limit;
            $date_query = $pmrequests->pm_get_user_date_query($_POST);
            $users =  $dbhandler->pm_get_all_users($search,$meta_query_array,'',$offset,$limit,'ASC','ID',array(),$date_query);
            $total_users = count($dbhandler->pm_get_all_users($search,$meta_query_array,'','','','ASC','ID',array(),$date_query));
            $num_of_pages = ceil( $total_users/$limit);
            $pagination = $dbhandler->pm_get_pagination($num_of_pages,$pagenum);
            $user_info =array();
        
               if(isset($total_users)){
                $return .="<div  class=\"pm-all-members pm-dbfl pm-pad10\">"
                       . translate('Total ', 'profile-grid')."<b>".$total_users
                       ."</b>". translate(' members', 'profile-grid')."</div>";
               }
           
               if(!empty($users))
            {
                
            
            foreach($users as $user)
                {       
                        $user_info['avatar'] = get_avatar($user->user_email, 100, '', false, array('class' => 'pm-user-profile'));
                        $user_info['id']=$user->ID;
                        $profile_url= $pmrequests->pm_get_user_profile_url($user->ID);
                        $user_info['profile_url'] = $profile_url;
                        $user_info['name']=$pmrequests->pm_get_display_name($user->ID); 
                        $group_leader_class="";
                        if($user_info['group_leader'])$group_leader_class="pm-group-leader-medium";
                      
                        
                        $return .= "<div id=\"search_result\" class=\"pm-user pm-difl pm-radius5 pm-border $group_leader_class \"> ". 
                        "<a href=".$user_info['profile_url'].">"
                                            .$user_info['avatar']
                                            . "<div class=\"pm-user-name pm-dbfl pm-clip\">".$user_info['name']."</div></a></div>";
                        
                }

                }
                else{
                    $return ="<div class=\"pm-message pm-dbfl pm-pad10\">"
                            . translate("Sorry, your search returned no results." , 'profile-grid')
                            ."</div>";
                }
              
            
                if(isset($pagination))
                $return.="<div class=\"pm_clear\"></div>".$pagination;

//                echo'<pre>';
//               print_r($users);
//              echo'  </pre>';
           
              echo $return;
            die;
        }

        public function pm_change_frontend_user_pass()
        {
                $textdomain = $this->profile_magic;
                $pmrequests = new PM_request;
                $current_user = wp_get_current_user();
                if(isset($current_user->ID) && !empty($_POST['pass1']))
                {
                        if(strlen($_POST['pass1'])<7)
                        {
                                $pm_error = __('Password is too short. At least 7 characters please!','profile-grid');
                        }
                        else
                        {
                                if($_POST['pass1']==$_POST['pass2'])
                                {   

                                    $newpass = $pmrequests->pm_encrypt_decrypt_pass('encrypt',$_POST['pass1']);
                                    update_user_meta( $current_user->ID,'user_pass',$newpass);
                                    wp_set_password( $_POST['pass1'], $current_user->ID );

                                    $pm_error = true;

                                }
                                else
                                {
                                        $pm_error = __('New Password and Repeat password does not match.','profile-grid');
                                }
                        }
                }
                else
                {
                        $pm_error = __('Password didn\'t changed.','profile-grid');
                }
                echo $pm_error;
                die;	
        }

        public function profile_magic_recapcha_field($gid)
        {
            $pmrequests = new PM_request;
            $html_creator = new PM_HTML_Creator($this->profile_magic,$this->version);
             if($pmrequests->profile_magic_show_captcha('pm_enable_recaptcha_in_reg'))
             {
                $html_creator->pm_get_captcha_html();
             }
        }

        public function pm_submit_user_registration($post,$files,$server,$gid,$fields,$user_id,$textdomain)
        {
                $dbhandler = new PM_DBhandler;
                $pmemails = new PM_Emails;
                $pmrequests = new PM_request;
                $pm_admin_notification = $dbhandler->get_global_option_value('pm_admin_notification',0);
                if($pm_admin_notification==1)
                {
                        $exclude = array('user_avatar','file','user_pass','confirm_pass','heading','paragraph');
                        $admin_html = $pmrequests->pm_admin_notification_message_html($post,$gid,$fields,$exclude);
                        $subject = __('New User Created','profile-grid');
                        $admin_message = '<p>'.__('New user created','profile-grid').'</p>'.$admin_html;
                        $pmemails->pm_send_admin_notification($subject,$admin_message);	
                }

                $pmemails->pm_send_group_based_notification($gid,$user_id,'on_registration');
                $autoapproval = $dbhandler->get_global_option_value('pm_auto_approval',0);
                $send_user_activation_link = $dbhandler->get_global_option_value('pm_send_user_activation_link',0);
                if($autoapproval=='1')
                {
                    if($send_user_activation_link=='1' && $pmrequests->profile_magic_check_paid_group($gid)=='0')
                    {
                      $userstatus = '1';   
                      $pmrequests->pm_update_user_activation_code($user_id);
                      $pmemails->pm_send_activation_link($user_id,$this->profile_magic);
                    }
                    else
                    {
                        $userstatus = '0';
                    }
                }
                else
                {
                    $userstatus = '1';
                }
                update_user_meta( $user_id,'rm_user_status',$userstatus);

        }

       public function pm_submit_user_registration_paypal($post,$files,$server,$gid,$fields,$user_id,$textdomain)
        {
                $pmrequests = new PM_request;
                $pm_payapl_request = new PM_paypal_request();
                if($pmrequests->profile_magic_check_paid_group($gid)>0)
                {
                    
                    switch($post['pm_payment_method'])
                    {
                        case 'paypal':
                            $pm_payapl_request->profile_magic_payment_process($post,$post["action"],$gid,$user_id,$textdomain);
                            break;
                        default:
                            do_action('profile_magic_custom_payment_process',$post,$gid,$user_id);
                            break;
                    }
                        
                }
        }

        public function pm_payment_process($post,$request,$gid,$textdomain)
        {
            $dbhandler = new PM_DBhandler;
            $pmrequests = new PM_request;
            $pm_payapl_request = new PM_paypal_request();
            if(isset($request["action"]) && $request["action"]!='process')
		{
			if(isset($request["uid"]))$uid = $request["uid"];else $uid = false;
			
			if($request["action"]=='re_process')
			{
                                $additional = "uid = $uid";
                                $payment_log = $dbhandler-> get_all_result('PAYPAL_LOG','*',1,'results',0,1,'id','DESC',$additional);
                                if(isset($payment_log))
                                {
                                    //print_r($payment_log);
                                    $payment_method = $payment_log[0]->pay_processor;
                                }
                                else
                                {
                                    $payment_method = 'paypal';
                                }
                                
                                if($payment_method=='paypal')
                                {
                                    $pm_payapl_request->profile_magic_repayment_process($uid,$gid);
                                }
                                else
                                {
                                    do_action('profile_magic_custom_repayment_process',$uid,$gid,$payment_method);
                                }
				
			}
			else
			{
				$pm_payapl_request->profile_magic_payment_process($post,$request["action"],$gid,$uid,$textdomain);
			}
			
			
			return false;
		}

	}
        
        public function pm_upload_image()
        {
            require( 'partials/crop.php');
            die;
        }
	
        public function pm_upload_cover_image()
        {
            require( 'partials/coverimg_crop.php');
            die;
        }
	
        public function pg_create_post_type()
        {
            
            register_post_type( 'profilegrid_blogs',
                array(
                  'labels' => array(
                    'name' => __( 'User Blogs' ),
                    'singular_name' => __( 'User Blog' )
                  ),
                  'public' => true,
                  'has_archive' => true,
                  'rewrite' => array('slug' => 'profilegrid_blogs'),
                  'taxonomies'            => array('post_tag'),
                  'supports' => array('title','editor','author','thumbnail','comments')

                )
              );
            
            add_theme_support( 'post-thumbnails' );
        }
        
        public function pm_load_pg_blogs()
        {
            $pmhtmlcreator = new PM_HTML_Creator($this->profile_magic,$this->version);
            $pmhtmlcreator->pm_get_user_blog_posts($_POST['uid'],$_POST['page'] );
            die;
        }
        
        public function pm_get_rid_by_uname()
        {
            $current_user = wp_get_current_user();
            $user = get_user_by('login', $_POST['uname']);
            if($user)
            {
                if(get_user_meta($user->ID,'rm_user_status', true)==0):
                    if($current_user->ID!=$user->ID)
                    {
                        echo $user->ID;
                    }
                endif;
            }
            die;
        }
        public function pm_show_friends_tab($uid,$gid)
	{
            $dbhandler = new PM_DBhandler;
            if($dbhandler->get_global_option_value('pm_friends_panel','0'))
            {
                echo '<li class="pm-profile-tab pm-pad10"><a class="pm-dbfl" href="#pg-friends">'. __('Friends','profile-grid').'</a></li>';
            }
	}
	
        public function pm_show_friends_content($uid,$gid)
	{
            $dbhandler = new PM_DBhandler;
            if($dbhandler->get_global_option_value('pm_friends_panel','0'))
            {
                    echo '<div id="pg-friends" class="pm-dbfl">';
                    include 'partials/profile-magic-friends.php';
                    echo '</div>';
            }
	}
        
        public function pm_fetch_my_friends()
	{
		$pmrequests = new PM_request;
		$dbhandler = new PM_DBhandler;
                $pmfriends = new PM_Friends_Functions;
                $pmhtmlcreator = new PM_HTML_Creator($this->profile_magic,$this->version);
		$uid = filter_input(INPUT_POST, 'uid');
		$path =  plugin_dir_url(__FILE__);
                $pm_f_search = filter_input(INPUT_POST,'pm_f_search');
                $view = filter_input(INPUT_POST,'pm_friend_view');
                $limit = 20; // number of rows in page
                //echo $uid;die;
		$pagenum = filter_input(INPUT_POST, 'pagenum');
                
		if($pagenum)
		{
                    $pmhtmlcreator->pm_get_my_friends_html($uid,$pagenum,$pm_f_search,$limit,$view);         
		}
		die;
	}
        
        public function pm_fetch_friend_list_counter()
        {
            $pmfriends = new PM_Friends_Functions;
            $uid = filter_input(INPUT_POST, 'uid');
            $view = filter_input(INPUT_POST,'pm_friend_view');
            switch($view)
            {
                case 1:
                    echo $pmfriends->pm_count_my_friends($uid);
                    break;
                case 2:
                    echo $pmfriends->pm_count_my_friend_requests($uid);
                    break;
                case 3:
                    echo $pmfriends->pm_count_my_friend_requests($uid,1);
                    break;
            }
            die;
        }
        
        public function pm_fetch_my_suggestion()
	{
		$pmrequests = new PM_request;
		$dbhandler = new PM_DBhandler;
                $pmfriends = new PM_Friends_Functions;
		$identifier = 'FRIENDS';
		$uid = filter_input(INPUT_POST, 'uid');
		$path =  plugin_dir_url(__FILE__);
		$pagenum = filter_input(INPUT_POST, 'pagenum');
		$suggestions = $pmfriends->profile_magic_friends_suggestion($uid);
		//print_r($suggestions);die;
		if($pagenum)
		{
			$pm_u_search = filter_input(INPUT_POST,'pm_u_search');
			$limit = 10; // number of rows in page
			$pagenum = isset($pagenum) ? absint($pagenum) : 1;
			$offset = ( $pagenum - 1 ) * $limit;
			$meta_query_array = $pmrequests->pm_get_user_meta_query($_GET);
			$date_query = $pmrequests->pm_get_user_date_query($_GET);
			$suggestions = $pmfriends->profile_magic_friends_suggestion($uid);
			
			$users =  $dbhandler->pm_get_all_users($pm_u_search,$meta_query_array,'',$offset,$limit,'ASC','include',array(),$date_query,$suggestions);
			
			$pmfriends->profile_magic_friends_result_html($users,$uid);
		}
		die;
	}
        
        public function pm_add_friend_request()
	{
		$pmrequests = new PM_request;
		$dbhandler = new PM_DBhandler;
                $pmnotification = new Profile_Magic_Notification;
		$identifier = 'FRIENDS';
		$user1 = filter_input(INPUT_POST, 'user1');
		$user2 = filter_input(INPUT_POST, 'user2');
		$u1 = $pmrequests->pm_encrypt_decrypt_pass('decrypt',$user1);
		$u2 = $pmrequests->pm_encrypt_decrypt_pass('decrypt',$user2);
		$data = array();
		$data['user1'] = $u1;
		$data['user2'] = $u2;
		$date = date("Y-m-d h:i:s");
		$data['created_date'] = $date;
		$data['action_date'] = $date;
		$data['status'] = 1;
		$id = $dbhandler->insert_row($identifier,$data);
                $pmnotification->pm_friend_request_notification($u2, $u1)
		?>
        <span><?php _e('Request Sent',$this->profile_magic);?></span>
        
        <?php
		die;
	}
	
        public function pm_remove_friend_suggestion()
	{
		$pmrequests = new PM_request;
		$dbhandler = new PM_DBhandler;
		$identifier = 'FRIENDS';
		$user1 = filter_input(INPUT_POST, 'user1');
		$user2 = filter_input(INPUT_POST, 'user2');
		$u1 = $pmrequests->pm_encrypt_decrypt_pass('decrypt',$user1);
		$u2 = $pmrequests->pm_encrypt_decrypt_pass('decrypt',$user2);
		$data = array();
		$data['user1'] = $u1;
		$data['user2'] = $u2;
		$date = date("Y-m-d h:i:s");
		$data['created_date'] = $date;
		$data['action_date'] = $date;
		$data['status'] = 5;
		$id = $dbhandler->insert_row($identifier,$data);
		echo $id;
		die;
	}
        
        public function pm_confirm_friend_request()
	{
		$pmrequests = new PM_request;
		$dbhandler = new PM_DBhandler;
                $pmfriends = new PM_Friends_Functions;
                $pmnotification = new Profile_Magic_Notification;
		$identifier = 'FRIENDS';
		$user1 = filter_input(INPUT_POST, 'user1');
		$user2 = filter_input(INPUT_POST, 'user2');
		$u1 = $pmrequests->pm_encrypt_decrypt_pass('decrypt',$user1);
		$u2 = $pmrequests->pm_encrypt_decrypt_pass('decrypt',$user2);
		$data = array();
		//$data['user1'] = $u1;
		//$data['user2'] = $u2;
		$date = date("Y-m-d h:i:s");
		//$data['created_date'] = $date;
		$data['action_date'] = $date;
		$data['status'] = 2;
		$requests = $pmfriends->profile_magic_is_exist_in_table($u1,$u2);
                $pmnotification->pm_friend_added_notification($u2,$u1);
		$dbhandler->update_row($identifier,'id',$requests->id,$data,array('%s','%d'),'%d');
		echo '<b>'.__('Request Accepted!',$this->profile_magic).'</b><br />'.__('You are now friends',$this->profile_magic);	
		die;
	}
        
        public function pm_reject_friend_request()
	{
		$pmrequests = new PM_request;
		$dbhandler = new PM_DBhandler;
                $pmfriends = new PM_Friends_Functions;
		$identifier = 'FRIENDS';
		$user1 = filter_input(INPUT_POST, 'user1');
		$user2 = filter_input(INPUT_POST, 'user2');
		$u1 = $pmrequests->pm_encrypt_decrypt_pass('decrypt',$user1);
		$u2 = $pmrequests->pm_encrypt_decrypt_pass('decrypt',$user2);
		$data = array();
		//$data['user1'] = $u1;
		//$data['user2'] = $u2;
		$date = date("Y-m-d h:i:s");
		//$data['created_date'] = $date;
		$data['action_date'] = $date;
		$data['status'] = 3;
		$requests = $pmfriends->profile_magic_is_exist_in_table($u1,$u2);
		$dbhandler->update_row($identifier,'id',$requests->id,$data,array('%s','%d'),'%d');
                $username2 = $pmrequests->pm_get_display_name($u2);
                echo '<b>'.__('Request Rejected!',$this->profile_magic).'</b><br />'. sprintf(__("You cancelled friend request from %s.","profile-grid"),$username2);
		die;
	}
        
        public function pm_block_friend()
	{
		$pmrequests = new PM_request;
		$dbhandler = new PM_DBhandler;
                $pmfriends = new PM_Friends_Functions;
		$identifier = 'FRIENDS';
		$user1 = filter_input(INPUT_POST, 'user1');
		$user2 = filter_input(INPUT_POST, 'user2');
		$u1 = $pmrequests->pm_encrypt_decrypt_pass('decrypt',$user1);
		$u2 = $pmrequests->pm_encrypt_decrypt_pass('decrypt',$user2);
		$data = array();
		//$data['user1'] = $u1;
		//$data['user2'] = $u2;
		$date = date("Y-m-d h:i:s");
		//$data['created_date'] = $date;
		$data['action_date'] = $date;
		$data['status'] = 4;
		$requests = $pmfriends->profile_magic_is_exist_in_table($u1,$u2);
		$dbhandler->update_row($identifier,'id',$requests->id,$data,array('%s','%d'),'%d');
		echo '<b>'.__('Friend Blocked!',$this->profile_magic).'</b><br />'.__('You have blocked this user',$this->profile_magic);	
		die;
	}
        
        public function pm_unfriend_friend()
	{
		$pmrequests = new PM_request;
		$dbhandler = new PM_DBhandler;
                $pmfriends = new PM_Friends_Functions;
		$identifier = 'FRIENDS';
		$user1 = filter_input(INPUT_POST, 'user1');
		$user2 = filter_input(INPUT_POST, 'user2');
                $cancel_request = filter_input(INPUT_POST, 'cancel_request');
		$u1 = $pmrequests->pm_encrypt_decrypt_pass('decrypt',$user1);
		$u2 = $pmrequests->pm_encrypt_decrypt_pass('decrypt',$user2);
		$data = array();
		//$data['user1'] = $u1;
		//$data['user2'] = $u2;
		$date = date("Y-m-d h:i:s");
		//$data['created_date'] = $date;
		$data['action_date'] = $date;
		$data['status'] = 6;
		$requests = $pmfriends->profile_magic_is_exist_in_table($u1,$u2);
                $dbhandler->update_row($identifier,'id',$requests->id,$data,array('%s','%d'),'%d');
		if($cancel_request==1):
                $dbhandler->remove_row($identifier,'id', $requests->id,'%d');
                echo '<b>'.__('Request Removed!','profile-grid').'</b>';
                else:
                         $username2 = $pmrequests->pm_get_display_name($u2);
                    echo '<b>'.__('Friend Removed!',$this->profile_magic).'</b><br />'.sprintf(__("You have removed %s from your friend list.","profile-grid"),$username2);
                endif;
		
                	
		die;
	}
        
        public function pm_get_friends_notification()
	{
		$dbhandler = new PM_DBhandler;
		$identifier = 'FRIENDS';
		$timestamp = filter_input(INPUT_GET, 'timestamp');		
		$current_user = wp_get_current_user();
		$uid = $current_user->ID;		
		set_time_limit(0);
		while (true) {
			$last_ajax_call = isset($timestamp) ? (int)($timestamp) : null;
			$where = array('user2'=>$uid,'status'=>1);
			$last_change_data = $dbhandler->get_all_result($identifier,'*',$where);
			foreach($last_change_data as $last_row)
			{
				$last_change_time = $last_row->action_date; 
			}
					
			// get timestamp of when file has been changed the last time
			$last_change_in_data_file = strtotime($last_change_time);
		
			// if no timestamp delivered via ajax or data.txt has been changed SINCE last ajax timestamp
			if ($last_ajax_call == null || $last_change_in_data_file > $last_ajax_call) {
		
				// get content of data.txt
				$data = count($last_change_data);
				if(!isset($data) || empty($data))$data = '0';
				// put data.txt's content and timestamp of last data.txt change into array
				$result = array(
					'data_from_file' => $data,
					'timestamp' => $last_change_in_data_file
				);
		
				// encode to JSON, render the result (for AJAX)
				$json = json_encode($result);
				echo $json;
		
				// leave this loop step
				break;
		
			} else {
				// wait for 1 sec (not very sexy as this blocks the PHP/Apache process, but that's how it goes)
				sleep( 1 );
				continue;
			}
		}

		
		die;
	}
        
        
        public function pm_right_side_options($uid,$gid)
        {
            $pmrequests = new PM_request;
            $dbhandler = new PM_DBhandler;
            $pmfriends = new PM_Friends_Functions;
            $PM_Messanger = new PM_Messenger;
            $current_user = wp_get_current_user();
            if($uid !=$current_user->ID && $dbhandler->get_global_option_value('pm_enable_private_messaging','1')==1):
                $messenger_url = $PM_Messanger->pm_get_message_url($uid);
            ?>
              <div class="pm-difr pm-pad20">
                  <a id="message_user" href="<?php echo $messenger_url; ?>" ><?php _e('Message','profile-grid');?></a>
            </div>
            <?php endif; 
            
            
            if($uid !=$current_user->ID && $dbhandler->get_global_option_value('pm_friends_panel','0')==1):
               echo '<div class="pm-difr pm-pad20">';
                $pmfriends->profile_magic_friend_list_button($current_user->ID, $uid);
                echo '</div>';
             endif; 
          
        }
        
        public function pm_delete_notification(){
            $notif_id = filter_input(INPUT_POST, 'id');
            $pm_notification = new Profile_Magic_Notification();
            $return = $pm_notification->pm_delete_notification($notif_id);
            echo $return;
            die;
        }
        
        public function pm_load_more_notification(){
            $loadnum = filter_input(INPUT_POST, 'loadnum');
            $pm_notification = new Profile_Magic_Notification();
            $pm_notification->pm_generate_notification_without_heartbeat($loadnum);
            die;
         
        }
        
        public function pm_read_all_notification(){
            $uid = get_current_user_id();
            $pm_notification = new Profile_Magic_Notification();
            $pm_notification->pm_mark_all_notification_as_read($uid);
            die;
         
        }
        
        public function pm_refresh_notification(){
             $pm_notification = new Profile_Magic_Notification();
            $pm_notification->pm_generate_notification_without_heartbeat();
            die;
        }
        
        public function profile_magic_custom_payment_fields($gid)
        {
            $pmrequests = new PM_request;
            $dbhandler = new PM_DBhandler;
            $paypal_enable = $dbhandler->get_global_option_value('pm_enable_paypal','0');
            
            if($pmrequests->profile_magic_check_paid_group($gid)>0):
             ?>        
        
        <div class="pmrow">
    
                <div class="pm-col">
                    <div class="pm-form-field-icon"></div>
                    <div class="pm-field-lable">
                        <label for=""><?php _e('Price','profile-grid');?></label>
                    </div>
                    <div class="pm-field-input">
                        <div class="pm_group_price">
              <?php if($dbhandler->get_global_option_value('pm_currency_position','before')=='before'):
                    echo $pmrequests->pm_get_currency_symbol().' '.$pmrequests->profile_magic_check_paid_group($gid);
                else:
                    echo $pmrequests->profile_magic_check_paid_group($gid).' '.$pmrequests->pm_get_currency_symbol();
                endif;
                ?>
            </div>
                        <div class="errortext" style="display:none;"></div>
                       
                    </div>
                </div>
                
            </div>
        <div class="pmrow">
                <div class="pm-col">
                    <div class="pm-form-field-icon"></div>
                    <div class="pm-field-lable">
                        <label for=""><?php _e('Payment Method','profile-grid');?><sup>*</sup></label>
                    </div>
                    <div class="pm-field-input pm_radiorequired">
                        <div class="pmradio">
                            <?php if($paypal_enable==1):?>
                            <div class="pm-radio-option"><input title="<?php _e('PayPal','profile-grid'); ?>" type="radio"  id="pm_payment_method" name="pm_payment_method" value="paypal" checked><?php _e('PayPal','profile-grid');?></div>
                            <?php endif;?>
                             <?php do_action('profile_magic_additional_payment_options',$gid); ?>
                        </div>
                        <div class="errortext" style="display:none;"></div>
                    </div>
                </div>
        </div>
             <?php
            endif;
        }
        
        public function profile_magic_check_paypal_config($msg)
        {
            $dbhandler = new PM_DBhandler;
            $paypal_enable = $dbhandler->get_global_option_value('pm_enable_paypal','0');
            if($paypal_enable==1)
            {
                $paypal_email = trim($dbhandler->get_global_option_value('pm_paypal_email'));
                if($paypal_email=='')
                {
                    $msg = __('Oops! It looks like the PayPal payment system is not configured properly. Please check its settings.','profile-grid');
                }
                else
                {
                    $msg = '';
                }
            }
            else
            {
                    $msg = 'disabled';
            }
            return $msg;
        }
}
