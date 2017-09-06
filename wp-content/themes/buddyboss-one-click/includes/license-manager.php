<?php
if( !defined( 'ABSPATH' ) ){
    exit();
}

class buddyboss_onclick_license_manager{
    private $option_name = '_bboneclick_license_details';
    
    public function __construct(){
        add_action( 'wp_ajax_bboc_lconnect_received_message', array( $this, 'updater_bb_connect_received_message' ) );
        
        add_action( 'admin_init', array( $this, 'maybe_process_disconnect' ) );
    }
    
    public function connect_ui(){
        ?>
        <div id="bboclm_connect_outer">
            <div class="clearfix">
                <div id="bboclm_connect_sidebar">
                    <div class="padder">
                        <?php buddyboss_oneclick_installer()->system_status_helper->display_brief();?>
                    </div>
                </div>
                
                
                <div id="bboclm_connect_inner">
                    <div class="padder">
                        <h3><em><?php _e( "Before we proceed..", "buddyboss-one-click" );?></em></h3>
                        
                        <table class="table table-form bb_connect">
                            <tr>
                                <td>
                                    <button id="btn_bblicense_connect" class="button button-primary button-hero">
                                        <span class="dashicons dashicons-admin-plugins"></span> <?php _e( 'Connect your BuddyBoss.com account', 'buddyboss-one-click' );?>
                                    </button>
                                </td>
                                <td>
                                    <span class="connecting" style="display:none;"><?php _e( 'Connecting', 'buddyboss-one-click' );?></span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2">&nbsp;</td>
                            </tr>
                        </table>
                        
                        <p>
                            <?php _e( "Please connect with your buddyboss.com account to proceed. After successful connection, you'll see a list of demo packages available. You can install packages that you have a valid license for.", "buddyboss-one-click" );?>
                            <br>
                            <?php 
                            $link = "<a href='https://www.buddyboss.com/my-account/?part=mysubscriptions' rel='noopener' target='_blank'>" . __( 'here', 'buddyboss-one-click' ) . "</a>";
                            printf( __( "You can see a list of your licenses %s.", "buddyboss-one-click" ), $link );
                            ?>
                        </p>
                        
                    </div>
                </div>
            </div>
        </div>
        
        

        <div id="bb_connector_overlay_wrapper" style="display: none;">
            <div id="bb_connector_overlay">
                <img src="<?php echo home_url( 'wp-includes/images/spinner-2x.gif' );?>" >
            </div>
        </div>

        <style type="text/css">
            #bboclm_connect_sidebar{
                float: right;
                width: 400px;
                border-left: 1px solid #ccc;
            }
            
            #bboclm_connect_inner .padder{
                margin-right: 402px;
            }
            
            #bboclm_connect_inner .padder,
            #bboclm_connect_sidebar .padder{
                padding: 10px 30px;
            }
            
            #bb_connector_overlay_wrapper{
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: 1001;
                background-color: rgba( 255, 255, 255, 0.8 );
            }

            #bb_connector_overlay_wrapper #bb_connector_overlay{
                position: absolute;
                top: 50%; margin-top: -20px;
                left: 50%; margin-left: -20px;
            }

            body.bb_connect_overlay{
                overflow: hidden;
            }

            #btn_bblicense_connect .dashicons{
                line-height: inherit;
            }
        </style>
        
        <script type="text/javascript">
            var BBONECLICK_LICENSE_ADMIN = {
                'connector_url' : "https://www.buddyboss.com/?bb_updater_init_connect=1",
                'connector_host' : 'https://www.buddyboss.com/',
                'nonce_received_message' : '<?php echo wp_create_nonce( 'updater_bb_connect_received_message' );?>',
            };
            
            jQuery(document).ready(function(){
                BBONECLICK_LICENSE_ADMIN.bb_connect.init();
            });
            
            BBONECLICK_LICENSE_ADMIN.bb_connect = {};
            (function(me, window, $) {
                var _l = {};

                me.init = function(){
                    if( !me.getElements() )
                        return;

                    _l.$connector_button.click(function(){
                        _l.$overlay_outer.show();
                        $('body').addClass('bb_connect_overlay');

                        $('.bb_connect .connecting').show();

                        var left = Number((screen.width/2)-(390/2));
                        var top = Number((screen.height/2)-(555/2));

                        _l.win = window.open( 
                            BBONECLICK_LICENSE_ADMIN.connector_url, 
                            '<?php _e( "Connect to BuddyBoss.com", "buddyboss-one-click" );?>', 
                            "width=390,height=555,top="+top+",left="+left+"" 
                        );
                    });
                };

                me.getElements = function(){
                    _l.$overlay_outer = $('#bb_connector_overlay_wrapper');
                    if( _l.$overlay_outer.length == 0 )
                        return false;

                    _l.$connector_button = $( '#btn_bblicense_connect' );
                    return true;
                };

                me.receive_message = function( event ){
                    var data = event.data;
                    if( event.origin != BBONECLICK_LICENSE_ADMIN.connector_host && data.message_type != 'updater_bb_connect' )
                        return false;


                    data.action = 'bboc_lconnect_received_message';
                    data.nonce = BBONECLICK_LICENSE_ADMIN.nonce_received_message;
                    $.ajax({
                        method: 'POST',
                        url: ajaxurl,
                        data: data,
                        success: function( response ){
                            $('body').removeClass('bb_connect_overlay');
                            _l.$overlay_outer.hide();

                            response = $.parseJSON( response );
                            if( response.status ){
                                if( response.message ){
                                    alert( response.message );
                                }

                                if( response.redirect_to ){
                                    window.location.href = response.redirect_to;
                                }
                            }
                        },
                        error: function(){
                            $('body').removeClass('bb_connect_overlay');
                            _l.$overlay_outer.hide();
                            alert( '<?php _e( "Error - Operation Failed.", "buddyboss-one-click" );?>' );
                        }
                    });
                };

            })(BBONECLICK_LICENSE_ADMIN.bb_connect, window, window.jQuery);

            window.addEventListener("message", BBONECLICK_LICENSE_ADMIN.bb_connect.receive_message, false);
        </script>
        <?php 
    }
    
    public function reconnect_ui(){
        ?>
        <p style="text-align: right;"><a href="<?php echo admin_url( 'admin.php?page=buddyboss-oneclick-installer&reconnect_license=1' );?>"><?php _e( "Reconnect your BuddyBoss.com Account", "buddyboss-one-click" );?></a></p>
        <?php 
    }
    
    public function maybe_process_disconnect(){
        if( isset( $_GET['page'] ) && 'buddyboss-oneclick-installer' == $_GET['page'] && isset( $_GET['reconnect_license'] ) ){
            $this->disconnect();
            wp_safe_redirect( admin_url( 'admin.php?page=buddyboss-oneclick-installer' ) );
            exit();
        }
    }
    
    public function updater_bb_connect_received_message(){
        check_ajax_referer( 'updater_bb_connect_received_message', 'nonce' );

        if( !current_user_can( 'manage_options' ) )
            die();

        $retval = array( 'status' => true, 'redirect_to' => admin_url( 'admin.php?page=buddyboss-oneclick-installer' ) );
        $retval['message'] = __( "Account connected. You can now download and install packages which you have an active license for.", "buddyboss-one-click" );

        $licenses = @$_POST['licenses'];
        if( empty( $licenses ) ){
            $licenses = 'nothing';
        }
        
        update_option( $this->option_name, $licenses );

        die( wp_json_encode( $retval ) );
    }
    
    public function is_connected(){
        $data = $this->get_licenses();
        return $data ? true : false;
    }
    
    public function disconnect(){
        delete_option( $this->option_name );
    }
    
    public function get_licenses(){
        $data = get_option( $this->option_name );
        return !empty( $data ) && 'nothing' != $data ? $data : false;
    }
    
    public function can_download_package( $package ){
        if( ! isset( $package['software_ids'] ) || empty( $package['software_ids'] ) )
            return true;
        
        $licenses = $this->get_licenses();
        if( empty( $licenses ) ){
            //no license found.
            //dont allow
            return false;
        }
        
        $has_active_license = false;
        //check if there is a license for one of these software product ids
        foreach( $licenses as $license ){
            if( isset( $license['software_product_id'] ) && in_array( $license['software_product_id'], $package['software_ids'] ) ){
                $has_active_license = true;
                break;
            }
        }
        
        return $has_active_license;
    }
    
    public function support_ui(){
        ?>
        <h3><?php _e( 'Video Tutorials', 'buddyboss-one-click' );?></h3>
        <div class="bbocsu_videos_outer">
            <div class="bbocsu_videos">
                <?php 
                $video_icon_url = trailingslashit( BUDDYBOSS_ONECLICK_INSTALLER_PLUGIN_URL ) . 'assets/images/youtubeicon.png';
                
                $videos = array( 
                    array( 'url' => 'https://www.youtube.com/playlist?list=PL5kBYJSuuvEgvoCvFmsIdoeTOzJt4dmiL', 'title' => 'Boss. theme (version 2)' ),
                    array( 'url' => 'https://www.youtube.com/playlist?list=PL5kBYJSuuvEjpnYLNl7aaZCCSu-l72lUz', 'title' => 'Social Learner - LearnDash' ),
                    array( 'url' => 'https://www.youtube.com/playlist?list=PL5kBYJSuuvEgGJmNyFHwwdcsM2NdK9f6o', 'title' => 'Social Learner - Sensei' ),
                    array( 'url' => 'https://www.youtube.com/playlist?list=PL5kBYJSuuvEhTwe7kubk0z8NDXn5yyRlo', 'title' => 'Social MarketPlace' ),
                    array( 'url' => 'https://www.youtube.com/playlist?list=PL5kBYJSuuvEip3zvZSGnonvDTLA3nf2mV', 'title' => 'Social Portfolio' ),
                    array( 'url' => 'https://www.youtube.com/playlist?list=PL5kBYJSuuvEjJw7w-2hdUavyDjMYJoZqY', 'title' => 'Social Blogger' ),
                );
                
                foreach( $videos as $video ):?>
                <div class="bbocsu_video">
                    <div class="bbocsu_video_inner">
                        <a href="<?php echo esc_attr( $video['url'] );?>" title="<?php echo esc_attr( $video['title'] );?>">
                            <img src="<?php echo $video_icon_url;?>" alt="<?php _e( 'Video Icon', 'buddyboss-one-click' );?>">
                            
                            <span><?php echo $video['title'];?></span>
                        </a>
                    </div>
                </div>
                <?php endforeach;?>
            </div>
            
            <a class="button button-secondary button-large" href="https://www.youtube.com/user/eisenwasser/playlists"><?php _e( 'View more videos', 'buddyboss-one-click' );?></a>
        </div>
        
        <div style="margin-bottom: 30px;"></div>
        
        <h3><?php _e( 'Resources', 'buddyboss-one-click' );?></h3>
        <div class="bbocsu_resources_outer">
            <a href="http://support.buddyboss.com/"><?php _e( 'Documentation &amp; Help Center', 'buddyboss-one-click' );?></a>
            <a href="https://www.buddyboss.com/tutorials/"><?php _e( 'Product Tutorials', 'buddyboss-one-click' );?></a><br>
            <a href="https://www.buddyboss.com/faq/"><?php _e( 'Frequently Asked Questions', 'buddyboss-one-click' );?></a>
            <a href="https://www.buddyboss.com/release-notes/"><?php _e( 'Release Notes', 'buddyboss-one-click' );?></a><br>
            <a href="https://www.buddyboss.com/support-forums/"><?php _e( 'Community Forums', 'buddyboss-one-click' );?></a>
        </div>
        
        <style type="text/css">
            .bbocsu_video{
                display: inline-block;
                width: 200px;
                height: 120px;
                margin: 0 30px 30px 0;
                background: #333 url(https://i.ytimg.com/vi/WlhULYTRqHM/hqdefault.jpg?sqp=-oaymwEWCMQBEG5IWvKriqkDCQgBFQAAiEIYAQ==&rs=AOn4CLAAf6klxHmzhW1FD-JENHKrie7TCw) center center no-repeat;
                text-align: center;
            }
            
            .bbocsu_video_inner{
                width: 200px;
                height: 90px;
                padding-top: 30px;
                background-color: rgba(0,0,0,0.7);
            }
            .bbocsu_video_inner a{
                color: #eee;
            }
            .bbocsu_video_inner a img{
                display: block;
                width: 50px;
                margin: 0 auto 5px;
            }
            
            .bbocsu_resources_outer a{
                display: inline-block;
                margin: 0 10px 10px 0;
                min-width: 250px;
            }
        </style>
        <?php 
    }
}