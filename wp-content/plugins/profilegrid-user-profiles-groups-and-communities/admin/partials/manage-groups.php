<?php
$dbhandler = new PM_DBhandler;
$pmrequests = new PM_request;
$textdomain = $this->profile_magic;
$path =  plugin_dir_url(__FILE__);
$identifier = 'GROUPS';
$pagenum = filter_input(INPUT_GET, 'pagenum');
$pagenum = isset($pagenum) ? absint($pagenum) : 1;
$limit = 11; // number of rows in page
$offset = ( $pagenum - 1 ) * $limit;
$totalgroups = $dbhandler->pm_count($identifier);
$groups =  $dbhandler->get_all_result($identifier,'*',1,'results',$offset,$limit,'id','desc');
$num_of_pages = ceil( $totalgroups/$limit);
$pagination = $dbhandler->pm_get_pagination($num_of_pages,$pagenum);
?>
<div class="pm_notification"></div>
<div class="pmagic"> 
  
  <!-----Operationsbar Starts----->
    <div class="pg-logo"><img src="<?php echo $path;?>images/profilegrid-logo.png"></div>
  <form name="pm_manage_groups" id="pm_manage_groups" action="admin.php?page=pm_add_group" method="post">
  <div class="operationsbar">
    <div class="pmtitle">
      <?php _e('Groups Manager','profile-grid');?>
    </div>
    <div class="icons"><a href="admin.php?page=pm_settings"> <img src="<?php echo $path;?>images/global-settings.png"></a> </div>
    <div class="nav">
      <ul>
        <li>
        <input type="submit" class="pm_add_new" id="pm_add_new" value="<?php _e('Add New','profile-grid');?>" />
        </li>
        <li class="pm_action_button"><input type="submit" class="pm_disabled" name="duplicate" id="duplicate" value="<?php _e('Duplicate','profile-grid');?>" disabled></li>
        <li class="pm_action_button"><input type="submit" class="pm_disabled" name="delete" id="delete" value="<?php _e('Delete','profile-grid');?>" disabled></li>
      </ul>
    </div>
  </div>
  
  <!-------Contentarea Starts----->
  
  <div class="pmagic-cards">
    <div class="pm-card">
      <div class="pm-new-form">
        <input type="text" name="group_name" id="group_name">
        <div class="errortext" id="group_error" style="display:none;"><?php _e('This is required field','profile-grid');?></div>
        <input type="hidden" name="group_id" id="group_id" value="" />
        <input type="hidden" name="associate_role" id="associate_role" value="subscriber">
        <?php wp_nonce_field('save_pm_add_group'); ?>
        <input type="submit" value="<?php _e('Create New Group','profile-grid');?>" name="submit_group" id="submit_group" onclick="return check_validation()" />
      </div>
    </div>
    <?php if(!empty($groups)):
    foreach($groups as $group):
	
	$meta_query_array = $pmrequests->pm_get_user_meta_query(array('gid'=>$group->id));
	$date_query = $pmrequests->pm_get_user_date_query(array('gid'=>$group->id));
	$users =  $dbhandler->pm_get_all_users('',$meta_query_array,'',0,3,'DESC','ID');
	$total_users = count($dbhandler->pm_get_all_users('',$meta_query_array,'','','','ASC','ID'));
	
	?>
    <div class="pm-card">
      <div class="cardtitle">
        <input type="checkbox" name="selected[]" value="<?php echo $group->id;?>" />
          <i class="fa fa-users" aria-hidden="true"></i>
       <?php echo $group->group_name;?>
       </div>
      <!---- <div class="carddescription">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi ut turpis ex.
</div> ---->
      <div class="pm-last-submission"> <b><?php _e("Members",'profile-grid');?></b></div>
      <?php foreach($users as $user):?>
      <div class="pm-last-submission"><a href="admin.php?page=pm_profile_view&id=<?php echo $user->ID; ?>"><?php echo get_avatar($user->user_email,16,'',false,array('class'=>'pm-user'));?> </a> <?php _e("At",'profile-grid');?> <?php echo $user->user_registered;?> </div>
      <?php endforeach;?>
      <?php if($total_users>3):?>
      <div class="pm-last-submission"> (...)<a href="admin.php?page=pm_user_manager&gid=<?php echo $group->id; ?>"> <?php _e("and",'profile-grid');?> <span class="card-submissions"><?php echo $total_users-3;?> </span> <?php _e("more",'profile-grid');?> </a> </div>
      <?php endif;?>
      <div class="pm-form-shortcode-row"><?php echo 'ID '.$group->id.'';?></div>
      <div class="pm-form-links">
        <div class="pm-form-row"><a href="admin.php?page=pm_add_group&id=<?php echo $group->id;?>"><?php _e('Settings','profile-grid');?></a></div>
        <div class="pm-form-row"><a href="admin.php?page=pm_profile_fields&gid=<?php echo $group->id;?>"><?php _e('Fields','profile-grid');?></a></div>
      </div>
    </div>
    <?php endforeach;?>
    <?php else: ?>
    <?php _e( 'You have not created any groups yet. Once you have created a new group, it will appear here.','profile-grid' ); ?>
    <?php endif;?>
    
    
  </div>
 <?php echo $pagination;?>
  
 </form>
    <?php $pmrequests->pm_five_star_review_banner();?>
</div>