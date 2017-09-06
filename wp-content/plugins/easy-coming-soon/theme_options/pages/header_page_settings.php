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
	
?>
<div class="block ui-tabs-panel active" id="option-ui-id-1" >
<?php $current_options = wp_parse_args( get_option( 'soon_page_settings', array() ), $general_default_settings );
		
		if(isset($_POST['easy-coming-soon_lite_settings_save_1'])) 
		{
			
				if ( empty($_POST) || !wp_verify_nonce($_POST['general_settings_page_save_nonce_field'],'general_settings_page_save_nonce_action' ) )
			{  
		      print __('Sorry, your nonce did not verify.','easy-coming-soon');	exit; 
			}

				$current_options['status'] = $_POST['status'];	
				$current_options['background'] = $_POST['background'];
				$current_options['title'] = stripcslashes($_POST['title']);
				$current_options['descrip'] = stripcslashes($_POST['descrip']);
				$current_options['google_code'] = stripslashes($_POST['google_code']);
				$current_options['fb'] = $_POST['fb'];
				$current_options['twit'] = $_POST['twit'];
				$current_options['gp'] = $_POST['gp'];
				
				
				update_option('soon_page_settings',$current_options);
		}

		if(isset($_POST['easy-coming-soon_lite_settings_reset_1'])) 
		{
			$current_options['status']='1';
			$current_options['background']='';
			$current_options['title']='';
			$current_options['descrip']='';
			$current_options['google_code']='';
			$current_options['fb']='';
			$current_options['twit']='';
			$current_options['gp']='';

			update_option('soon_page_settings',$current_options);
		}
	?>
	<form method="post" action="#section_general">
		<div id="heading">
			<table style="width:100%;"><tr>
				<td><h2><?php _e('General Settings','easy-coming-soon');?></h2></td>
				<td style="width:30%;">
					<div class="easy-coming-soon_lite_settings_loding" id="easy-coming-soon_loding_1_image"></div>
					<div class="easy-coming-soon_lite_settings_massage" id="easy-coming-soon_lite_settings_save_1_success" ><?php _e('Options data successfully Saved','easy-coming-soon');?></div>
					<div class="easy-coming-soon_lite_settings_massage" id="easy-coming-soon_lite_settings_save_1_reset" ><?php _e('Options data successfully reset','easy-coming-soon');?></div>
				</td>
				<td style="text-align:right;">					
					<input class="button" type="submit" name="easy-coming-soon_lite_settings_reset_1" 
					value="<?php _e('Restore Defaults','easy-coming-soon'); ?>" >&nbsp;
					<input class="button button-primary button-large" type="submit" name="easy-coming-soon_lite_settings_save_1" value="<?php _e('Save Options','easy-coming-soon'); ?>">
				</td>
				</tr>
			</table>			
		</div>	
		
		
		<div class="section">
            <div class="element">
            	<h3><?php _e('Select status','easy-coming-soon') ?></h3>
                <input type="radio" name="status" value="0" id="status"  <?php if($current_options['status']=='0') echo 'checked' ?>/>&nbsp;<?php _e('Disabled','easy-coming-soon') ?><br>
                <input type="radio" name="status" value="1" id="status" <?php if($current_options['status']=='1') echo 'checked' ?>/>&nbsp;<?php _e('Enable Coming Soon Mode','easy-coming-soon') ?>
            </div>
        </div>

        <div class="section">
            <div class="element">
            	<h3><?php _e('Background image','easy-coming-soon') ?></h3>
                <input type="text" value="<?php if(!empty($current_options['background'])){ echo $current_options['background']; } ?>" id="background" name="background" size="36">
                <input type="button" value="Upload Background Image" class="button-primary upload-primary" style="margin-left:10px;height:30px">
            </div>
            <br/>
            <?php if(!empty($current_options['background'])) { ?>
            <div><img style="display: inline; width:200px; border:1px solid #3894BD;" src="<?php echo $current_options['background']; ?>"></div>
        	<?php } ?>
        </div>

        <div class="section">
            <div class="element">
            	<h3><?php _e('Headline','easy-coming-soon') ?></h3>
                <input type="text" id="title" name="title"  value="<?php if(!empty($current_options['title'])){ echo $current_options['title']; } ?>"/>
            </div>
        </div>	

        <div class="section">
            <div class="element">
            	<h3><?php _e('Description','easy-coming-soon') ?></h3>
                <textarea id="descrip" name="descrip" rows="5"><?php if(!empty($current_options['descrip'])){ echo $current_options['descrip']; } ?></textarea>
            </div>
        </div>

        <div class="section">
            <div class="element">
            	<h3><?php _e('Google Analytics Code','easy-coming-soon') ?></h3>
                <textarea id="google_code" name="google_code" rows="5"><?php if(!empty($current_options['google_code'])){ echo $current_options['google_code']; } ?></textarea>
            </div>
        </div>

        <div class="section">
            <div class="element">
            	<h3><?php _e('Facebook URL','easy-coming-soon') ?></h3>
                <input type="text" id="fb" name="fb" placeholder="<?php _e('Facebook URL','easy-coming-soon'); ?>" value="<?php if(!empty($current_options['fb'])){ echo $current_options['fb']; } ?>"/>
            </div>
        </div>

        <div class="section">
            <div class="element">
            	<h3><?php _e('Twitter URL','easy-coming-soon') ?></h3>
                <input type="text" id="twit" name="twit" placeholder="<?php _e('Twitter URL','easy-coming-soon') ?>"  value="<?php if(!empty($current_options['twit'])){ echo $current_options['twit']; } ?>"/>
            </div>
        </div>

        <div class="section">
            <div class="element">
            	<h3><?php _e('GooglePlus URL','easy-coming-soon') ?></h3>
                <input type="text" id="gp" name="gp" placeholder="<?php _e('GooglePlus URL','easy-coming-soon') ?>"  value="<?php if(!empty($current_options['gp'])){ echo $current_options['gp']; } ?>"/>
            </div>
        </div>

		<div id="button_section">
			<input class="button button-primary" type="submit" name="easy-coming-soon_lite_settings_save_1"value="<?php _e('Save Options','easy-coming-soon'); ?>">&nbsp;
			<input class="button" type="submit" name="easy-coming-soon_lite_settings_reset_1" value="<?php _e('Restore Defaults','easy-coming-soon'); ?>">
		</div>
		
		<!-- Nonce Add - 27 August -->
		
		<?php wp_nonce_field('general_settings_page_save_nonce_action','general_settings_page_save_nonce_field'); ?>
	</form>
</div>