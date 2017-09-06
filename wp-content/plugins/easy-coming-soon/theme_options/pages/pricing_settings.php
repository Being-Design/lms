<div class="block ui-tabs-panel active" id="option-ui-id-7" >
	<form method="post" id="easy-coming-soon_lite_theme_options_7">
		
<div class="row" style="margin-left:10px;background: #f7f7f7;padding-top: 10px;padding-bottom: 70px;">
	
	<div class="span6" style="width:85%;margin-top: auto;">
		<h3><?php _e('Pricing','easy-coming-soon') ?></h3>
		<p> <?php _e('We have 2 set of licenses','easy-coming-soon') ?></p> 
		
	<ul><li><?php printf(__('<strong>Single Site License</strong> - The price of this license is <b>$29</b>. Which allow you to use the Premium plugin on single site.','easy-coming-soon')) ?></li> 
		<li><?php printf(__('<strong>Unlimited Site License</strong> - This price of this license is <b>$59</b>. Which allow you to use the plugin on unlimited wordPress websites.','easy-coming-soon')) ?>
		</li></ul>
		<p><?php printf(__('Support and Updates will be given for 1 year. <br>If you need updates and support after one year, then simply renew the license. If not, then you may keep using the plugin.','easy-coming-soon')) ?></p>
		

<h3><?php _e('How to Purchase','easy-coming-soon') ?></h3>
			<p><?php echo sprintf (__("If you are interested then you can buy the plugin <a href = 'http://member.easycomingsoon.com/pricing/' target = '_blank'>here.</a> Once the purchase process gets complete, the license key will be emailed to you. Add and activate this license key in the license option page in the premium version of this plugin. Make sure that you activate this license key in order to enjoy the continue support and plugin updates.","easy-coming-soon")); ?></p>

<h3><?php _e('I look Forward to Working with you','easy-coming-soon') ?></h3>
			<p><?php _e('Thousands of users have enjoyed using our. I hope you will also enjoy working with us','easy-coming-soon') ?></p>

	</div>
	
 </div>


	<div class="row" style="margin-left:10px;background:#fff;text-align:center">

	</div>
	<br>

	<br>
	 
	<br>
	<div style="text-align: center;">
	<a class="btn btn-danger  btn-large" href="http://easycomingsoon.com/" target="_new"><?php _e('Upgrade to pro version','easy-coming-soon') ?></a>&nbsp;
	<a class="btn btn-success  btn-large" href="http://easycomingsoon.com/demos/" target="_new"><?php _e('View Live demo','easy-coming-soon') ?></a>
	</div> 
	<br>	

		<!--<div id="button_section">
			<input type="hidden" value="1" id="easy-coming-soon_lite_settings_save_7" name="easy-coming-soon_lite_settings_save_7" />
			<input class="reset-button btn" type="button" name="reset" value="Restore Defaults" onclick="easy-coming-soon_option_data_reset('7');">
			<input class="button button-primary" type="button" value="Save Options" onclick="easy-coming-soon_option_data_save('7')" >
		</div>-->
	</form>
</div>
<style>
.span6 h3{
font: 33px/29px "Helvetica Neue","Helvetica Neue",Helvetica,Arial,sans-serif;
color: #000;
-webkit-font-smoothing: antialiased;
}
.span6 p{
font-size: 17px;
font-family: "Source Sans Pro",sans-serif;
color: #707070;
line-height:1.8;
}

/* =Tooltips style
========================================================================*/

.icon1 {
	display: inline-block;
	width: 13px;
	height: 13px;
	position: relative;
	padding: 0 0px 0 0;
	vertical-align: middle;
	
	background: url(<?php echo plugins_url('images/icons1.png',__FILE__) ?>) no-repeat;
}

.tooltip1 {
	display: none;
	width: 200px;
	position: absolute;
	padding: 10px;
	margin: 4px 0 0 4px;
	top: 0;
	left: 16px;
	border: 1px solid #76B6D7;
	border-radius: 0 8px 8px 8px;
	background: #bedffe;
	font-size: 13px;
	box-shadow: 0 1px 2px -1px #21759B;
	z-index: 999;
}

/* Icons Sprite Position */

.help {
	background-position: 0 0;
}

.warning {
	background-position: -20px 0;
}

.error {
	background-position: -40px 0;
}

/* Tooltip Colors */

.help .tooltip1 {
	border-color: #76B6D7;
	background-color: #bedffe;
	box-shadow-color: #21759B;
}

.warning .tooltip1 {
	border-color: #cca863;
	background-color: #ffff70;
	box-shadow-color: #ac8c4e;
}

.error .tooltip1 {
	border-color: #b50d0d;
	background-color: #e44d4e;
	box-shadow-color: #810606;
}

.icon1:hover .tooltip1 {
	display: block;
	box-shadow: 0 10px 20px -1px rgba(0,0,0,0.5);
	
	
}
</style>