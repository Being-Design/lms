<?php

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
	
	

?>
<div class="block ui-tabs-panel " id="option-ui-id-2" >	
	<?php $current_options = wp_parse_args( get_option( 'soon_page_desgin_settings', array() ),$design_page_setting );

	
		if(isset($_POST['easy-coming-soon_lite_settings_save_2'])) 
		{
			
			if ( !wp_verify_nonce($_POST['design_settings_page_save_nonce_field'],'design_settings_page_save_nonce_action' ) )
			{  
		      print __('Sorry, your nonce did not verify.','easy-coming-soon');	exit; 
			}
			
			
			
		    $current_options['background_color']=$_POST['background_color'];
		    $current_options['title_color']=$_POST['title_color'];
		    $current_options['title_font_size']=$_POST['title_font_size'];
		    $current_options['title_font_format']=$_POST['title_font_format'];
		    $current_options['description_font_size']=$_POST['description_font_size'];
		    $current_options['descrip_color']=$_POST['descrip_color'];
		    $current_options['description_font_format']=$_POST['description_font_format'];
		    $current_options['background_effect']=$_POST['background_effect'];
	
			update_option('soon_page_desgin_settings',$current_options);
		}	


		if(isset($_POST['easy-coming-soon_lite_settings_reset_2'])) 			
		{
			if ( !wp_verify_nonce($_POST['design_settings_page_save_nonce_field'],'design_settings_page_save_nonce_action' ) )
			{  
		      print __('Sorry, your nonce did not verify.','easy-coming-soon');	exit; 
			}
			
			$current_options['background_color']='#21759B';
			$current_options['title_color']='#ffffff';
			$current_options['descrip_color']='#ffffff';
			$current_options['title_font_size']='30';
			$current_options['title_font_format']='Arial Black, sans-serif';
			$current_options['description_font_size']='20';
			$current_options['description_font_format']='Verdana, Geneva, sans-serif';
			$current_options['background_effect']='0';
					
			update_option('soon_page_desgin_settings',$current_options);
		} 
?>
<style>
#more_faq,#remove_button{ text-decoration: none;}
</style>	
	<form method="post" action="#section_design">
		<div id="heading">
			<table style="width:100%;"><tr>
				<td><h2><?php _e('Design Settings','easy-coming-soon');?></h2></td>
				<td style="width:30%;">
					<div class="easy-coming-soon_lite_settings_loding" id="easy-coming-soon_loding_2_image"></div>
					<div class="easy-coming-soon_lite_settings_massage" id="easy-coming-soon_lite_settings_save_2_success" ><?php _e('Options data successfully Saved','easy-coming-soon');?></div>
					<div class="easy-coming-soon_lite_settings_massage" id="easy-coming-soon_lite_settings_save_2_reset" ><?php _e('Options data successfully reset','easy-coming-soon');?></div>
				</td>
				<td style="text-align:right;">
					<input class="button" type="submit" name="easy-coming-soon_lite_settings_reset_2" value="Restore Defaults">&nbsp;
					<input class="button button-primary button-large" type="submit" name="easy-coming-soon_lite_settings_save_2" value="Save Options">
				</td>
				</tr>
			</table>	
		</div>

		<div class="section">
			<h3><?php _e('Background Color','easy-coming-soon'); ?>  </h3>
			<input type="text" id="background_color" name="background_color" value="<?php if(!empty($current_options['background_color'])){ echo $current_options['background_color']; } ?>">
		</div>

		<div class="section">
			<h3><?php _e('Headline Font Color','easy-coming-soon'); ?>  </h3>
			<input type="text" id="title_color" name="title_color" value="<?php if(!empty($current_options['title_color'])){ echo $current_options['title_color']; } ?>">
		</div>

		<div class="section">
			<h3><?php _e('Headline Font Size','easy-coming-soon'); ?>  </h3>
			<select id="title_font_size" name="title_font_size">
				<?php for( $i=10; $i<=100; $i++ ) { ?>
				<option value="<?php echo $i; ?>" <?php if($i == $current_options['title_font_size']) { echo "selected"; } ?>><?php echo $i; ?></option>
				<?php } ?>
			</select>
			<span><?php echo 'px'; ?></span>
		</div>

		<div class="section">
			<h3><?php _e('Headline Font Style','easy-coming-soon'); ?>  </h3>
			<select name="title_font_format" id="title_font_format">
				<optgroup label="Select Fonts">
				<?php if(!empty($current_options['title_font_format'])) { ?>
				<option value="<?php echo $current_options['title_font_format']; ?>" selected="selected"><?php echo $current_options['title_font_format']; ?></option>
				<?php } ?>
                <option value="roboto">roboto</option>
                <option value="Arial, sans-serif">Arial</option>
                <option value="Verdana, Geneva, sans-serif">Verdana</option>
                <option value="Trebuchet MS, Tahoma, sans-serif">Trebuchet</option>
                <option value="Georgia, serif">Georgia</option>
                <option value="Times New Roman, serif">Times New Roman</option>
                <option value="Tahoma, Geneva, Verdana, sans-serif">Tahoma</option>
                <option value="Palatino, Palatino Linotype, serif">Palatino</option>
                <option value="Helvetica Neue, Helvetica, sans-serif">Helvetica*</option>
                <option value="Calibri, Candara, Segoe, Optima, sans-serif">Calibri*</option>
                <option value="Myriad Pro, Myriad, sans-serif">Myriad Pro*</option>
                <option value="Lucida Grande, Lucida Sans Unicode, Lucida Sans, sans-serif">Lucida</option>
                <option value="Arial Black, sans-serif">Arial Black</option>
                <option value="Gill Sans, Gill Sans MT, Calibri, sans-serif">Gill Sans*</option>
                <option value="Geneva, Tahoma, Verdana, sans-serif">Geneva*</option>
                <option value="Impact, Charcoal, sans-serif">Impact</option>
                <option value="Courier, Courier New, monospace">Courier</option>
                <option value="Abel">Abel</option>
				</optgroup>
            </select>
		</div>

		<div class="section">
			<h3><?php _e('Description Font Color','easy-coming-soon'); ?>  </h3>
			<input type="text" id="descrip_color" name="descrip_color" value="<?php if(!empty($current_options['descrip_color'])){ echo $current_options['descrip_color']; } ?>">
		</div>

		<div class="section">
			<h3><?php _e('Description Font Size','easy-coming-soon'); ?>  </h3>
			<select id="description_font_size" name="description_font_size">
				<?php for( $i=10; $i<=100; $i++ ) { ?>
				<option value="<?php echo $i; ?>" <?php if($i == $current_options['description_font_size']) { echo "selected"; } ?>><?php echo $i; ?></option>
				<?php } ?>
			</select>
			<span><?php echo 'px'; ?></span>
		</div>

		<div class="section">
			<h3><?php _e('Description Font Style','easy-coming-soon'); ?>  </h3>
			<select name="description_font_format" id="description_font_format">
				<optgroup label="Select Fonts">
				<?php if(!empty($current_options['description_font_format'])) { ?>
				<option value="<?php echo $current_options['description_font_format']; ?>" selected="selected"><?php echo $current_options['description_font_format']; ?></option>
				<?php } ?>
                <option value="roboto">roboto</option>
                <option value="Arial, sans-serif">Arial</option>
                <option value="Verdana, Geneva, sans-serif">Verdana</option>
                <option value="Trebuchet MS, Tahoma, sans-serif">Trebuchet</option>
                <option value="Georgia, serif">Georgia</option>
                <option value="Times New Roman, serif">Times New Roman</option>
                <option value="Tahoma, Geneva, Verdana, sans-serif">Tahoma</option>
                <option value="Palatino, Palatino Linotype, serif">Palatino</option>
                <option value="Helvetica Neue, Helvetica, sans-serif">Helvetica*</option>
                <option value="Calibri, Candara, Segoe, Optima, sans-serif">Calibri*</option>
                <option value="Myriad Pro, Myriad, sans-serif">Myriad Pro*</option>
                <option value="Lucida Grande, Lucida Sans Unicode, Lucida Sans, sans-serif">Lucida</option>
                <option value="Arial Black, sans-serif">Arial Black</option>
                <option value="Gill Sans, Gill Sans MT, Calibri, sans-serif">Gill Sans*</option>
                <option value="Geneva, Tahoma, Verdana, sans-serif">Geneva*</option>
                <option value="Impact, Charcoal, sans-serif">Impact</option>
                <option value="Courier, Courier New, monospace">Courier</option>
                <option value="Abel">Abel</option>
				</optgroup>
            </select>
		</div>

		<div class="section">
            <div class="element">
            	<h3><?php _e('Effect','easy-coming-soon'); ?></h3>
                <input type="radio" name="background_effect" value="0" id="background_effect"  <?php if($current_options['background_effect']=='0') echo 'checked' ?>/>&nbsp;<?php _e('None','easy-coming-soon'); ?><br>
                <input type="radio" name="background_effect" value="1" id="background_effect" <?php if($current_options['background_effect']=='1') echo 'checked' ?>/>&nbsp;<?php _e('Noise','easy-coming-soon'); ?>
            </div>
        </div>
				
		<div id="button_section">
			<input class="button button-primary button-large" type="submit" name="easy-coming-soon_lite_settings_save_2" value="<?php _e('Save Options','easy-coming-soon'); ?>">&nbsp;
			<input class="button" type="submit" name="easy-coming-soon_lite_settings_reset_2" value="<?php _e('Restore Defaults','easy-coming-soon'); ?>">
			
			<!-- adding Nonce - 27 th August -->
			<?php wp_nonce_field('design_settings_page_save_nonce_action','design_settings_page_save_nonce_field'); ?>
			
			
			
		</div>
	</form>
</div>
<script>
jQuery(document).ready(function(){
    jQuery('#background_color,#title_color,#descrip_color').wpColorPicker();
});
</script>