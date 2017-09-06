<style>
.span6 h3{
font: 33px/29px "Helvetica Neue","Helvetica Neue",Helvetica,Arial,sans-serif;
color: #000;
-webkit-font-smoothing: antialiased;
}
.span6 p{
font-size: 20px;
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
 <div class="block ui-tabs-panel ui-widget-content ui-corner-bottom" id="coming_soon_pro_detail" aria-labelledby="ui-id-9" style="display: none;" >
<div class="row" style="margin-left:10px;background:#fff;text-align:center">
	
	<div class="span6" style="width:80%;margin-top: auto;text-align:center">
		<h3 style="font-size: 45px;
font-style: normal;
font-weight: 900;
line-height:1.1;
color: #000;
letter-spacing: -1px;
text-align: center;
"><?php _e('Coming Soon Pro Features','easy-coming-soon') ?></h3>
		<p style="font-size: 18px;
font-style: normal;

color: #707070;
text-align: left;">
<?php echo sprintf (__("Hey There <br><br> Thanks for using the lite version of Easy Coming Soon. I hope the plugin will enable you to quickly and easily create a landing page If you are looking to create a more feature rich landing page then I would suggest you to have a look at the premium version of the Easy Coming Soon Plugin. It has a lot of additional features which will allow you to quickly create a good looking landing page.<br><br> I would suggest you to checkout the <a href ='http://easycomingsoon.com/demos/' target='_blank'> Sample Coming Soon Page </a> created by the premium version of the plugin <br><br> Also Check out the Admin Panel <a href = 'http://easycomingsoon.com/demos/' target='_blank'> here </a> User Login: user and Password: user","easy-coming-soon")); ?>
<br><br>
</p>
	</div>
			
	
			
		
 </div>
 
 <!-- Email Capture Feature Start -->
 
 <div class = "row"  style="margin-left:25px;background:#f7f7f7;">
 <h3 style="margin-left:25px;"><?php _e('Here is the complete List of Features in Pro Version ','easy-coming-soon') ?></h3><br>
 </div>
  <div class="row" style="margin-left:10px;background:white;padding-top:70px;padding-bottom:70px;">
	
	

	<div class="span6" style="width:84%;margin-top: auto;margin-left:5%; font-size:15px;">
	    
		<h3><?php _e('1. Multiple Design Templates ','easy-coming-soon') ?></h3>
		<p><?php _e('A Launch Page can play an important role in branding so we made sure that all included templates are good looking.','easy-coming-soon') ?> <br><?php printf( __('
As a user you just have to fill in the data via option panel and a Coming Soon Page will be created as per the selected template.','easy-coming-soon') ) ?></p>
	
	
		<h3><?php _e('2. Email Capture Form','easy-coming-soon') ?></h3>
	<p><?php _e('A Launch Page is ideal for collecting Email address of the interested visitors. You can quickly setup a Email Capture Form and Start collecting Email Address. All the Emails are stored on in your Website Database','easy-coming-soon') ?></p>
	
	<h3><?php _e('3. Newsletter / Autoresponder Integration','easy-coming-soon') ?></h3>
	<p><?php _e('Easy Coming Soon integrates with MailChimp, Aweber, Constant Contact , CampaignMonitor and Madmimi. You can connect the plugin to third party services and started building your Email List','easy-coming-soon') ?>
 </p>
	
	<h3><?php _e('4. User White List','easy-coming-soon') ?></h3>
	
	<p><?php _e('This feature is very useful if you are building the website for a client. With this feature you can allow client / contributor to view the live site while all others will be presented with the Coming Soon Page.','easy-coming-soon') ?></p>
	
	<h3><?php _e('5. Landing Page Mode','easy-coming-soon') ?></h3>
	<p> <?php printf(__('With this feature you can choose to display the Coming Soon Page on the entire site or on a single Page. <br> Here is a Use Case: Suppose you want the blog to be accessible to all users while displaying the coming soon page on the font page. It can be done with the Landing Page Mode.','easy-coming-soon')) ?> </p>
	
	
			
	
	
	
	
	<h3><?php _e('6. Custom Favicon and Logo Support','easy-coming-soon') ?></h3>
		
		<p><?php _e('You can quickly upload a Custom Logo and favicon via the option Panel. We make use of Wordpress Mdeia Uploader, so you can also insert the logo from the Wordpress Media Library.','easy-coming-soon') ?></p>
	

	<h3><?php _e('7. Count-Down Timer Support','easy-coming-soon') ?></h3>
		
	<p> <?php _e('This feature was implemented as a response to a suggestion by one of our user. With this feature you can display a count down timer on the coming soon page. The timer can be quickly configure via option panel.','easy-coming-soon') ?></p>	
	
	
	
	<h3><?php _e('8. Progress Bar Support','easy-coming-soon') ?></h3>
		
<p><?php _e('It is also possible to display a Progress Bar on the coming soon page. Currently 3 styles of progress bars are supported, Basic, Striped and Animated. ','easy-coming-soon') ?></p>
	
<h3><?php _e('9. Custom Text Strings','easy-coming-soon') ?></h3>
	<p><?php _e('This feature allows you to quickly change the labels of the Text Fields and Buttons.','easy-coming-soon') ?> </p>
	
	
	<h3><?php _e('10. BackGround Slide-Show','easy-coming-soon') ?></h3>
	<p><?php _e('You can create a SlideShow and display it as a background with this feature. You may add as many slides as you want. It is also possible to display a Static Image or simply a color as a background.','easy-coming-soon') ?>  </p>
	
	<h3><?php _e('11. Video BackGround Support','easy-coming-soon') ?></h3>
	<p><?php _e('This feature allows you to create a Full Screen Video BackGround. Just upload the video on Youtube and configure the settings via the Admin Panel. Its that simple!','easy-coming-soon') ?> </p>
	
	<h3><?php _e('12. Social Icon Support','easy-coming-soon') ?></h3>
	<p><?php _e('The premium version has support for 20 Social Media Network. You just need to fill in the url and the respective icon will be displayed on the coming soon page.','easy-coming-soon') ?>  </p>
	
	<h3><?php _e('13. Design Customization Options','easy-coming-soon') ?></h3>
	<p><?php _e('The plugin allows you to quickly customize the settings like Font Color, Font Size, Font type, Button Color etc. All of these settings can be accessed via the Option Panel.','easy-coming-soon') ?>  </p>
	
	<h3><?php _e('14. Custom CSS Support','easy-coming-soon') ?></h3>
	<p><?php _e('A lot of times the inbuilt settings are just not enough. With the Custom CSS support you can quikly add style rules to the coming soon page.','easy-coming-soon') ?>  </p>
	
	<h3><?php _e('15. Google Analytics Support','easy-coming-soon') ?></h3>
	<p><?php _e('You can paste the google analytics tracking code and track the pageviews. No need to install a separate plugin..','easy-coming-soon') ?> </p>
	

	
		</div>	
		
 </div>
 
 
  <!-- Email Capture Feature End -->
 
 
 
 
 
 
 
<div class="row" style="margin-left:10px;background: #f7f7f7;padding-top: 70px;padding-bottom: 70px;">
	
	<div class="span6" style="width:85%;margin-top: auto;">
		<h3><?php _e('Pricing','easy-coming-soon') ?></h3>
		<p> <?php _e('The pro version is priced at <strong>very reasonable 29 USD</strong> and entitles you to receive <strong>support and updates for 1 year</strong>. <br><br>If you need updates and support after one year, then simply renew the license. If not, then you may keep using the plugin. <br>You may use the plugin on any number of websites you want','easy-coming-soon') ?></p>


<h3><?php _e('How to Purchase.','easy-coming-soon') ?></h3>
			<p><?php printf(__('If you are interested then you can buy the plugin','easy-coming-soon')) ?><a href = "http://easycomingsoon.com/features/" target = "_blank"><?php _e('here','easy-coming-soon') ?></a> </p>

<h3><?php _e('I look Forward to Working with you','easy-coming-soon') ?></h3>
			<p><?php _e('Thousands of users have enjoyed using our. I hope you will also enjoy working with us','easy-coming-soon') ?> </p>



	</div>
			
	
			
		
 </div>
 

 
 <div class="row" style="margin-left:10px;background:white;padding-top: 70px;padding-bottom: 70px;">
	
	
	<div class="span6" style="width:45%;margin-top: auto;"></div>	
	<div class="span6" style="width:45%;margin-top: auto;">

	
	<h3><?php _e('Cheers','easy-coming-soon') ?></h3>
<h3>Priyanshu</h3>
<h3><?php _e('Co-Founder, Webriti Themes and Plugins','easy-coming-soon') ?></p></h3>
				</div>
	
		
 </div>
 
  

 

 
 
 
 
 
 
  

 
			
	



 
  <div class="row" style="margin-left:10px;background:#fff;text-align:center">
	

			
	
			
		
	</div>
	<br>

	<br>
	 
	<br>
	<div style="text-align: center;">
	<a class="btn btn-danger  btn-large" href="http://easycomingsoon.com/features/" target="_new"><?php _e('Upgrade to pro version','easy-coming-soon') ?></a>&nbsp;
	<a class="btn btn-success  btn-large" href="http://easycomingsoon.com/features/" target="_new"><?php _e('View Live demo','easy-coming-soon') ?></a>
	</div> 
	<br>
 </div>
 

 
 