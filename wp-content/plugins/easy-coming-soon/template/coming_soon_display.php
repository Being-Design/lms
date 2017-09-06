<!DOCTYPE html>
<html lang="en">
<?php $general_settings = get_option('soon_page_settings');
$design_settings = get_option('soon_page_desgin_settings');
$notification_settings =get_option('soon_page_notification_settings');

?>

  <head>
    <meta charset="utf-8">
    <title><?php
    bloginfo( 'name' );
    $site_description = get_bloginfo( 'description' );
    ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
   
      <link rel="stylesheet" href="<?php echo plugins_url('template/css/style.css',dirname(__FILE__)); ?>">
   <link rel="stylesheet" href="<?php echo plugins_url('template/css/cs-bootstrap.css',dirname(__FILE__)); ?>">
    <style type="text/css">
      body {
	  padding-top:9%;
	  padding-bottom:9%;
		<?php if($general_settings['background']==""){?>
		background:<?php if($design_settings['background_color']!=''){echo ($design_settings['background_color']); } else { echo('#21759B');}?>  <?php if($design_settings['background_effect']=="1"){ ?>  url(<?php echo plugins_url('/images/overlay/overlay-1.png',__FILE__); ?>) repeat <?php } ?> ;
         <?php } 
		 else{
		 ?>
		 background:url('<?php echo $general_settings['background']; ?>') ;
		 background-repeat: no-repeat;
          background-attachment: fixed;
          background-position: top center;
          -webkit-background-size: cover;
          -moz-background-size: cover;
          -o-background-size: cover;
          background-size: cover;
		 <?php } 
		 ?>
		 
		  }
	  


#title_style
{
	
	color:<?php if($design_settings['title_color']!=''){echo ($design_settings['title_color']); } else { echo('#ffffff');} ?>;
	font-size:<?php if($design_settings['title_font_size']!=''){echo ($design_settings['title_font_size']); } else { echo('30');} ?>px;
	font-family:<?php echo $design_settings['title_font_format']  ?>;
	font-style:normal;
	line-height:1.1;
	word-break: break-word;
	word-break break-word;
	-ms-word-wrap: normal | break-word;
	
 }
 
 #left_content
 {
	
 }
#descrip
{
	
	color:<?php if($design_settings['descrip_color']!=''){echo ($design_settings['descrip_color']); } else { echo('#ffffff');} ?>;
	font-size:<?php if($design_settings['description_font_size']!=''){echo ($design_settings['description_font_size']); } else { echo('20');} ?>px;
	font-family:<?php echo $design_settings['description_font_format']  ?>;
	font-style:normal;
	line-height:1.5;
	text-align:center;
	width:80%;
	word-break: break-word;
	word-break break-word;
	-ms-word-wrap: normal | break-word;
	
}
#social_links
{
}
.container{

}

    </style>
    
    <link rel="stylesheet" href="<?php echo plugins_url('template/css/cs-bootstrap-responsive.css',dirname(__FILE__)); ?>">

 

 <?php echo $general_settings['google_code']; ?>
  
  </head>

  <body>

   

    <div class="container" style="height:auto">

      <!-- Main hero unit for a primary marketing message or call to action -->
      <div class="hero-unit" style=" background:rgba(0,0,0,0.5);">
      <center>
        <div id="title_style" >
<h1><?php if($general_settings['title']!=''){echo ucwords($general_settings['title']); } else { echo('coming soon page');} ?></h1></div>

        
<div id="descrip" >

<p> <?php if($general_settings['descrip']!=''){echo ($general_settings['descrip']); } else { echo('This website will launch soon. This a place-holder text can be edited through option panel. Also add email subscription box through option panel. ');} ?> </p>
</div>

<?php if ($notification_settings['mailto']=="yes")
{?>

 <form id="subscribe" class="form-signin" action="#" method="post"  style=" width:80%;background:#2a6888; border:0px groove #333333;padding: 6px 5px 7px 5px; ">
                    
                   <input id="email" name="email" type="text" class="input-xlarge"   placeholder="<?php if($notification_settings['placeholder_text']!=''){echo ($notification_settings['placeholder_text']); } else { echo('Enter your email to notify');} ?>"/>
                    
                  <button id="notify" class="btn btn-primary" name="notify" onClick="checkvalidation()"><?php if($notification_settings['sb_btn']!=''){echo ($notification_settings['sb_btn']); } else { echo('Notify Me!');} ?></button>
          </form><?php } ?></center>
 
        </div>
        <?php if($general_settings['fb']!='' || $general_settings['twit']!='' || $general_settings['gp']!='') { ?>
         <div class="footer" style="background:#A4C5D6;margin-top:-30px;height:40px" >
         
          
         <div id="social_links" style="margin-right:10px;padding-top:1%;float:right" >
       <?php _e('Social Settings','easy-coming-soon') ?>&nbsp;
<?php if($general_settings['fb']!='')
{?>
	<a href="<?php echo $general_settings['fb']; ?>"> <img src="<?php echo plugins_url('images/facebook.png',__FILE__); ?>"></a>
   
<?php } ?>
&nbsp;
<?php if($general_settings['twit']!='')
{?>
	<a href="<?php echo $general_settings['twit']; ?>"> <img src="<?php echo plugins_url('images/twitter.png',__FILE__); ?>"></a>
   
<?php } ?>

&nbsp;
<?php if($general_settings['gp']!='')
{?>
	<a href="<?php echo $general_settings['gp']; ?>"> <img src="<?php echo plugins_url('images/googleplus.png',__FILE__); ?>"></a>
   
<?php } ?> 






</div>


   </div>  
   <?php } ?> <!--/footer end -->
   
   
   
      </div>

  
    </div> <!-- /container -->
		
<?php 
$_GET['my_preview'] = isset($_GET['my_preview'])?$_GET['my_preview']:false;
	if(isset($_POST['notify'])) {
		
	    $email = $_POST['email'];
		$email = strtolower($email);
		
		$to = $notification_settings['email_address'];
        $subject = "Coming soon page";
		
		if($notification_settings['user_message']==''){
			$message = "Hey Some one with email id => ".$email." has subscribed to the Coming Soon page";
		}
		else{
			$message = str_replace('[email]',$email, $notification_settings['user_message']);
		}
			
		
        $from = $notification_settings['email_address'];
        $headers = "From:" . $from;
		
		$to1 = $email;
        $subject1 = "Coming soon page";
        $message1 = "Thanks for submitting your email. We will notify you once we launch.";
        $from1 = $notification_settings['email_address'];
        $headers1 = "From:" . $from1;
		
		
			if($email=='')
			{
				echo "<script>alert('Please enter email');
							location.href='';
					</script>";
			}
			else
			{
				$regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';
				if( isset($_GET['my_preview']) && $_GET['my_preview'] != 'true' ){
					if(preg_match($regex, $email))
					{
						if(wp_mail($to,$subject,$message,$headers))
						{
							if($notification_settings['message']=='')
							{
								$display='Thanks for submitting your email. We will notify you once we launch.';
							}
							else
							{
								$display = $notification_settings['message'];
							}
							
							echo "<script>alert('$display');
							location.href='';</script>";
						} 
						else 
						{
							echo 'Message could not be sent.';
							echo "<script>location.href='';</script>";
						}
						
						wp_mail($to1,$subject1,$message1,$headers1);
					} 
					else 
					{ 
						echo "<script>alert('Invalid email address Please enter valid email.');
								location.href='';
								</script>";
					}
				}
			}
	}
?>
	
  </body>
</html>
