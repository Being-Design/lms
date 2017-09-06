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
<div class="block ui-tabs-panel active" id="option-ui-id-4" >
<?php $current_options = wp_parse_args( get_option( 'soon_page_settings', array() ), $general_default_settings );
	if(isset($_POST['easy-coming-soon_lite_settings_save_4']))
	{	
		if($_POST['easy-coming-soon_lite_settings_save_4'] == 1) 
		{
				if ( !wp_verify_nonce($_POST['template_settings_page_save_nonce_field'],'template_settings_page_save_nonce_action' ) )
			{  
		      print __('Sorry, your nonce did not verify.','easy-coming-soon');	exit; 
			}
			else  
			{	
				
				$current_options['status']=$_POST['status'];	
		
				update_option('soon_page_settings',$current_options);
			}
		}	
		if($_POST['easy-coming-soon_lite_settings_save_4'] == 2) 
		{
			
				if ( !wp_verify_nonce($_POST['template_settings_page_save_nonce_field'],'template_settings_page_save_nonce_action' ) )
			{  
		      print __('Sorry, your nonce did not verify.','easy-coming-soon');	exit; 
			}
			$current_options['status']='1';

			update_option('soon_page_settings',$current_options);
		}
	}  ?>
	<form method="post" id="easy-coming-soon_lite_theme_options_4">
		<div id="heading">
			<table style="width:100%;"><tr>
				<td><h2><?php _e('Templates Settings','easy-coming-soon');?></h2></td>
				<td style="width:30%;">
					<div class="easy-coming-soon_lite_settings_loding" id="easy-coming-soon_loding_4_image"></div>
					<div class="easy-coming-soon_lite_settings_massage" id="easy-coming-soon_lite_settings_save_4_success" ><?php _e('Options data successfully Saved','easy-coming-soon');?></div>
					<div class="easy-coming-soon_lite_settings_massage" id="easy-coming-soon_lite_settings_save_4_reset" ><?php _e('Options data successfully reset','easy-coming-soon');?></div>
				</td>
				<td style="text-align:right;">					
					<!--<input class="reset-button btn" type="button" name="reset" value="Restore Defaults" onclick="easy-coming-soon_option_data_reset('1');">
					<input class="button button-primary button-large" type="button" value="Save Options" onclick="easy-coming-soon_option_data_save('1')" >-->
			    </td>
				</tr>
			</table>			
		</div>	
		

		<table class="form-table">
        <tr>
		<th scope="row" class="page-title"><?php _e('Templates','webriti_soon_pro')?></th>
		<td></td>
		</tr>
 </table><br>	
 <table class="form-table">
 <tr>

 <td>

 <div class="template">
	
		<div class="template-screenshot">
			<img src="<?php echo plugin_dir_url(__FILE__)."images/screenshot-4.png";?>" alt="">
		</div>
	
	<h3 class="template-name">Default</h3>
	

	<div class="template-actions">

	
		
		<button class="btn btn-danger" disabled="disabled" id="template1_active" style="vertical-align:middle;">Activated</button>
	

	</div>

	
</div>
 <div class="template">
	
		<div class="template-screenshot">
			<a   href="http://easycomingsoon.com/"  target= "_new"><img src="<?php echo plugins_url('/images/screenshot-28.png',__FILE__);?>" alt=""></a>
		</div>
	
	<h3 class="template-name">Template1</h3>
	

	<div class="template-actions">

	
		<a class="btn btn-danger"  href="http://easycomingsoon.com/"  target= "_new" style="vertical-align:middle;"><?php printf(__('Available In Pro','easy-coming-soon')) ?></a>
		
	

	</div>

	
</div>


<div class="template">
	
		<div class="template-screenshot">
		<a   href="http://easycomingsoon.com/"  target= "_new">	<img src="<?php echo plugins_url('/images/screenshot-31.png',__FILE__);?>" alt=""></a>
		</div>
	
	<h3 class="template-name">Template2</h3>
	

	<div class="template-actions">

		
		<a class="btn btn-danger"  href="http://easycomingsoon.com/"  target= "_new" style="vertical-align:middle;"><?php printf(__('Available In Pro','easy-coming-soon')) ?></a>
		
		
	

	</div>

	
</div>

<div class="template">
	
		<div class="template-screenshot">
		<a   href="http://easycomingsoon.com/"  target= "_new">	<img src="<?php echo plugins_url('/images/template3-new.png',__FILE__);?>" alt=""></a>
		</div>
	
	<h3 class="template-name">Template3</h3>
	

	<div class="template-actions">

		
		<a class="btn btn-danger"  href="http://easycomingsoon.com/"  target= "_new" style="vertical-align:middle;"><?php printf(__('Available In Pro','easy-coming-soon')) ?></a>
		
		
	

	</div>

	
</div>

<div class="template">
		<div class="template-screenshot">
		<a   href="http://easycomingsoon.com/"  target= "_new">	<img src="<?php echo plugins_url('/images/template4.png',__FILE__);?>" alt=""></a>
		</div>
	<h3 class="template-name">Template4</h3>
	<div class="template-actions">
		<a class="btn btn-danger"  href="http://easycomingsoon.com/"  target= "_new" style="vertical-align:middle;"><?php printf(__('Available In Pro','easy-coming-soon')) ?></a>
	</div>
</div>

<div class="template">
		<div class="template-screenshot">
		<a   href="http://easycomingsoon.com/"  target= "_new">	<img src="<?php echo plugins_url('/images/template5.png',__FILE__);?>" alt=""></a>
		</div>
	<h3 class="template-name">Template5 <span style="color: white;background: #1ab015;padding: 3px 10px;/* box-shadow: 0 0 5px 0 #000; */border-radius: 4px;">New</span></h3>
	<div class="template-actions">
		<a class="btn btn-danger"  href="http://easycomingsoon.com/"  target= "_new" style="vertical-align:middle;"><?php printf(__('Available In Pro','easy-coming-soon')) ?></a>
	</div>
</div>

</td>
</tr>

</table>

		<!--<div id="button_section">
			<input type="hidden" value="1" id="easy-coming-soon_lite_settings_save_4" name="easy-coming-soon_lite_settings_save_4" />
			<input class="reset-button btn" type="button" name="reset" value="Restore Defaults" onclick="easy-coming-soon_option_data_reset('4');">
			<input class="button button-primary" type="button" value="Save Options" onclick="easy-coming-soon_option_data_save('4')" >
		</div>-->
		
		
		
			
			<!-- added nonce August 27 -->
			
			
			<?php wp_nonce_field('template_settings_page_save_nonce_action','template_settings_page_save_nonce_field'); ?>
			
		
		
		
	</form>
</div>
<style>
.template{
	cursor: pointer;
	float: left;
	margin: 0 4% 4% 0;
	position: relative;
	width: 300px;
	border: 1px solid #dedede;
	-webkit-box-shadow: 0 1px 1px -1px rgba(0,0,0,.1);
	box-shadow: 0 1px 1px -1px rgba(0,0,0,.1);
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
}

.template-screenshot{
	display: block;
	overflow: hidden;
	position: relative;
	-webkit-transition: opacity .2s ease-in-out;
	transition: opacity .2s ease-in-out;
}
.template-screenshot:hover{
	display: block;
	overflow: hidden;
	position: relative;
	opacity:0.8;
	-webkit-transition: opacity .2s ease-in-out;
	transition: opacity .2s ease-in-out;
}
.template-screenshot img {


width: 100%;

}
.template-name{
	font-size: 15px;
	font-weight: 600;
	margin: 0;
	padding: 15px;
	-webkit-box-shadow: inset 0 1px 0 rgba(0,0,0,.1);
	box-shadow: inset 0 1px 0 rgba(0,0,0,.1);
	overflow: hidden;
	white-space: nowrap;
	text-overflow: ellipsis;
	background: #fff;
	background: rgba(255,255,255,.65);
}
.template-actions{
	opacity: 1;
	-webkit-transition: opacity .1s ease-in-out;
	transition: opacity .1s ease-in-out;
	position: absolute;
	bottom: 0;
	right: 0;
	height: 38px;
	padding: 9px 10px 0;
	background: rgba(244,244,244,.7);
	border-left: 1px solid rgba(0,0,0,.05);
}
#option-tree-settings-api #poststuff .template h3 {
  padding: 9px 15px !important;
  line-height: 35px;
}
</style>