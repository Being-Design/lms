<div class="wrap settings-wrap" id="page-settings">
    <h2><?php _e('Settings','easy-coming-soon'); ?></h2>
    <div id="option-tree-header-wrap">
        <ul id="option-tree-header">
            <li id=""><a href="" target="_blank"></a></li>
            <li id="option-tree-version"><span><?php _e('Easy Coming Soon Plugin','easy-coming-soon'); ?></span></li>
			<a style="margin-right:16px;" target="_blank" href="<?php bloginfo("url"); ?>/?my_preview=true&TB_iframe=true&width=500&height=532" class="button button-primary button-large fb-btn"><?php _e('Preview','easy-coming-soon'); ?></a>
			<a style="margin-right:16px;" target="_blank" href="http://easycomingsoon.com/" class="button button-primary button-large fb-btn"><?php _e('Upgrade to pro version','easy-coming-soon'); ?></a>
            <a style="margin-right:16px;" target="_blank" href="https://wordpress.org/plugins/easy-coming-soon" class="button button-primary button-large fb-btn"><?php _e('Share Your Feedback','easy-coming-soon'); ?></a>
        </ul>
		
    </div>
    <div id="option-tree-settings-api">
    <div id="option-tree-sub-header"></div>
        <div class = "ui-tabs ui-widget ui-widget-content ui-corner-all">
            <ul>
				
                <li id="tab_create_setting"><a href="#section_general"><?php _e('General Settings','easy-coming-soon');?></a>
                </li>
				<li id="tab_design_setting"><a href="#section_design"><?php _e('Design','easy-coming-soon');?></a>
                </li>
                <li id="tab_notification_setting"><a href="#section_notification"><?php _e('Notification Settings','easy-coming-soon');?></a>
                </li>
                <li id="tab_templates_setting"><a href="#section_templates"><?php _e('Templates','easy-coming-soon');?></a>
                </li>
                <li id="tab_live_preview_setting"><a href="#section_live_preview"><?php _e('Live Preview','easy-coming-soon');?></a>
                </li>
                <li id="tab_pro_features_setting"><a href="#section_pro_features"><?php _e('Pro Features','easy-coming-soon');?></a>
                </li>
                <li id="tab_aboutpro_version_setting"><a href="#section_aboutpro_version"><?php _e('Whats included in the Pro Version','easy-coming-soon');?></a>
                </li>
				<li id="tab_pricing_setting"><a href="#section_pricing_version"><?php _e('Pricing','easy-coming-soon');?></a>
                </li>
				<li id="tab_about_show_some_love"><a href="#section_about_show_some_love"><?php _e('Show Some Love','easy-coming-soon');?></a>
                </li>
                <li id="tab_about_vid_tutorial"><a href="#section_about_vid_tutorial"><?php _e('Video Tutorial','easy-coming-soon');?></a>
                </li>
               
            </ul>
    <div id="poststuff" class="metabox-holder">
        <div id="post-body">
			<div id="post-body-content">
                <div id="section_general" class = "postbox">
                    <div class="inside">
                        <div id="setting_theme_options_ui_text" class="format-settings">
                            <div class="format-setting-wrap">             
                    <?php load_template( dirname( __FILE__ ) . '/pages/header_page_settings.php' );  ?>    
                </div>
            </div>
        </div>
    </div>

    
	<div id="section_design" class = "postbox">
        <div class="inside">
            <div id="design_customization_settings" class="format-settings">
                <div class="format-setting-wrap">
      <div class = "format-setting type-textarea has-desc">
        <div class = "format-setting-inner">
        
		<?php  load_template( dirname( __FILE__ ) . '/pages/design_page_settings.php' ); ?>
                                              
						</div>
					</div>
				</div>
			</div>
		</div>
    </div>


    <div id="section_notification" class = "postbox">
        <div class="inside">
            <div id="design_customization_settings" class="format-settings">
                <div class="format-setting-wrap">
      <div class = "format-setting type-textarea has-desc">
        <div class = "format-setting-inner">
        
        <?php  load_template( dirname( __FILE__ ) . '/pages/notification_page_setting.php' ); ?>
                                              
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="section_templates" class = "postbox">
        <div class="inside">
            <div id="design_customization_settings" class="format-settings">
                <div class="format-setting-wrap">
      <div class = "format-setting type-textarea has-desc">
        <div class = "format-setting-inner">
        
        <?php  load_template( dirname( __FILE__ ) . '/pages/template_page_settings.php' ); ?>
                                              
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div id="section_live_preview" class = "postbox">
        <div class="inside">
            <div id="design_customization_settings" class="format-settings">
                <div class="format-setting-wrap">
      <div class = "format-setting type-textarea has-desc">
        <div class = "format-setting-inner">
        
        <?php  load_template( dirname( __FILE__ ) . '/pages/live_preview_settings.php' ); ?>
                                              
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div id="section_pro_features" class = "postbox">
        <div class="inside">
            <div id="design_customization_settings" class="format-settings">
                <div class="format-setting-wrap">
      <div class = "format-setting type-textarea has-desc">
        <div class = "format-setting-inner">
        
        <?php  load_template( dirname( __FILE__ ) . '/pages/pro_features_settings.php' ); ?>
                                              
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div id="section_aboutpro_version" class = "postbox">
        <div class="inside">
            <div id="design_customization_settings" class="format-settings">
                <div class="format-setting-wrap">
      <div class = "format-setting type-textarea has-desc">
        <div class = "format-setting-inner">
        
        <?php  load_template( dirname( __FILE__ ) . '/pages/aboutpro_version_settings.php' ); ?>
                                              
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
	
	<div id="section_pricing_version" class = "postbox">
        <div class="inside">
            <div id="design_customization_settings" class="format-settings">
                <div class="format-setting-wrap">
      <div class = "format-setting type-textarea has-desc">
        <div class = "format-setting-inner">
        
        <?php  load_template( dirname( __FILE__ ) . '/pages/pricing_settings.php' ); ?>
                                              
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
	
	<div id="section_about_show_some_love" class = "postbox">
        <div class="inside">
            <div id="design_customization_settings" class="format-settings">
                <div class="format-setting-wrap">
				  <div class = "format-setting type-textarea has-desc">
					<div class = "format-setting-inner">
						<div class="block ui-tabs-panel active" id="option-ui-id-5" >
							<form method="post" id="easy-coming-soon_lite_theme_options_5">
									<div id="heading">
										<table style="width:100%;"><tr>
											<td><h2><?php _e('Show Some Love','easy-coming-soon');?></h2>
											<br>
											<p><?php _e('Like this plugin? Show your support by','easy-coming-soon') ?></p>
											</td>
											<td style="width:30%;">
											</td>
											<td style="text-align:right;">					
												
											</tr>
										</table>			
									</div>

									<div class="section">
										<a class="button button-primary button-large" target="_blank" href="https://wordpress.org/support/view/plugin-reviews/easy-coming-soon"><?php _e('Rate It','easy-coming-soon') ?></a>
										<a class="button button-primary button-large" target="_blank" href="http://twitter.com/share?url=https%3A%2F%2Fwordpress.org%2Fplugins%2Feasy-coming-soon%2F&amp;text=Check out this awesome %23WordPress Plugin I'm using,  Easy Coming Soon by @webriti"><i class="fa fa-twitter"></i><?php _e('Tweet It','easy-coming-soon') ?></a>
										<a class="button button-primary button-large" target="_blank" href="http://member.easycomingsoon.com/pricing/"><?php _e('Buy Pro','easy-coming-soon') ?></a>
									</div>	
							</form>
						</div>								  
					</div>
				</div>
				</div>
			</div>
		</div>
    </div>
	



	<div id="section_about_vid_tutorial" class = "postbox">
        <div class="inside">
            <div id="design_customization_settings" class="format-settings">
                <div class="format-setting-wrap">
				  <div class = "format-setting type-textarea has-desc">
					<div class = "format-setting-inner">
						<div class="block ui-tabs-panel active" id="option-ui-id-5" >
							<form method="post" id="easy-coming-soon_lite_theme_options_5">
									<div id="heading">
										<table style="width:100%;"><tr>
											<td><h2><?php _e('Fully functional coming soon page in 30 minutes','easy-coming-soon');?></h2>
											<br>
											<p><?php echo sprintf (__("In this<a target = '_blank' href ='https://www.youtube.com/watch?v=jwXOO9DDSpY'>video tutorial</a>, I will demonstrate how you can create a fully functional Coming Soon Page in as little as 30 minutes.","easy-coming-soon")); ?></p>
											<p> <?php _e('Here is what we the landing page will look like.','easy-coming-soon') ?>
											<div class="span6" style="width:100%">
												<img  style="height:50%; width:50%" src="<?php echo plugins_url('/pages/images/img/video-thumb.jpg',__FILE__);?>" alt="" style="width:100%"> 
											</div>
                                            <br></br>
											
											<b><?php _e('This video tutorial is for the premium version of the plugin. As you can see, it has:','easy-coming-soon') ?></b>
										    <br></br>
											
											<ul><?php _e('1. Company logo','easy-coming-soon') ?></ul>
											<ul><?php _e('2. An image slide show backGround','easy-coming-soon') ?></ul>
											<ul><?php _e('3. Email capture box, with option to capture first name and last name','easy-coming-soon') ?></ul>
											<ul><?php _e('4. Countdown timer','easy-coming-soon') ?></ul>
											<ul><?php _e('5. A progress bar','easy-coming-soon') ?></ul>
											<ul><?php _e('6. Social media icons','easy-coming-soon') ?></ul>
											<ul><?php _e('7. The video also contains a brief overview of the features like multiple templates, Ip based access, newsletter integration etc.','easy-coming-soon') ?></ul>
											
											
											<?php echo sprintf(__('</p>The premium version is priced at 29 USD and lets you use the plugin on unlimited website.</p><p>Here find the <a target = "_blank" href = "https://www.youtube.com/watch?v=JEbKUdvbzys"> link to the video </a>demonstrating how to create coming soon page.</p>','easy-coming-soon')); ?>
											</td>
											<td style="width:30%;">
											</td>
											<td style="text-align:right;">					
												
											</tr>
										</table>			
									</div>
							</form>
						</div>								  
					</div>
				</div>
				</div>
			</div>
		</div>
    </div>


		</div>
    </div>
    </div>
	<div class="webriti-submenu" style="height:35px;">			
            <div class="webriti-submenu-links" style="margin-top:5px;">
			<form method="POST">
				<input type="submit" onclick="return confirm( 'Click OK to reset Plugin data. Theme settings will be lost!' );" value="Restore All Defaults" name="restore_all_defaults" id="restore_all_defaults" class="reset-button btn">
			<form>
            </div><!-- webriti-submenu-links -->
        </div>
        <div class="clear"></div>
        </div>
    </div>
</div>

<?php
$general_default_settings = array(

    // general page settings

    'status'=>'1',
    'background'=>'',
    'title'=>'',
    'descrip'=>'',
    'google_code'=>'',
    'fb'=>'',
    'twit'=>'',
    'gp'=>''

    );
$design_page_setting =  array(

    // design page settings
    'background_color'=>'#21759B',
    'title_color'=>'#ffffff',
    'descrip_color'=>'#ffffff',
    'title_font_size'=>'30',
    'title_font_format'=>'Arial Black, sans-serif',
    'description_font_size'=>'20',
    'description_font_format'=>'Verdana, Geneva, sans-serif',
    'background_effect'=>'0'

    );
$notification_default_settings = array(

    // notification page settings

    'mailto'=>'no',
    'email_address'=>'',
    'message'=>__('Thanks for subscribing this page','easy-coming-soon'),
    'sb_btn'=>'',
    'placeholder_text'=>''

    );
// Restore all defaults
if(isset($_POST['restore_all_defaults'])) 
	{
		update_option('soon_page_settings',$general_default_settings);
        update_option('soon_page_desgin_settings',$design_page_setting);
        update_option('soon_page_notification_settings',$notification_default_settings);
	}
?>