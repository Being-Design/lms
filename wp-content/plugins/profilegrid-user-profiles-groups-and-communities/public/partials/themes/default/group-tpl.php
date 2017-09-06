<?php
$pmrequests = new PM_request;
$options = maybe_unserialize($row->group_options);
if(!empty($options) && isset($options['is_hide_group_card']) && $options['is_hide_group_card']==1)
{
    $show_group_card =0;
}else 
{ $show_group_card =1;}
?>
<div class="pmagic">
    <?php if($show_group_card==1):?>
       <div class="pm-group-card-box pm-dbfl pm-border-bt">
         <div class="pm-group-card pm-dbfl pm-border pm-bg pm-radius5">
            <div class="pm-group-title pm-dbfl pm-bg-lt pm-pad10 pm-border-bt">
            <i class="fa fa-users" aria-hidden="true"></i>
			<?php echo $row->group_name;?>
            <?php 
			$profile_url = $pmrequests->profile_magic_get_frontend_url('pm_user_profile_page','');
                         $slug = $pmrequests->pm_get_profile_slug_by_id($group_leader);
			$profile_url = add_query_arg( 'uid',$slug,$profile_url );
			if(is_user_logged_in() && $group_leader==$current_user->ID):
			$edit_group = $pmrequests->profile_magic_get_frontend_url('pm_group_page','');
			$edit_group = add_query_arg( 'gid',$gid,$edit_group );
			$edit_group = add_query_arg( 'edit','1',$edit_group );
			?>
           <div class="pm-edit-group"><a href="<?php echo $edit_group;?>" class="pm_button"><?php _e('Edit','profile-grid');?></a></div>
            <?php endif;?>
            </div>
             <div class="pm-group-image pm-difl pm-border">
                  <?php echo $pmrequests->profile_magic_get_group_icon($row); ?>
             </div>
             <div class="pm-group-description pm-difl pm-bg pm-pad10 pm-border">
         
         		<?php if(isset($group_leader) && $group_leader!=false && $pagenum==1):?>
                <div class="pm-card-row pm-dbfl">
                    <div class="pm-card-label pm-difl">Leader</div>
                    <div class="pm-card-value pm-difl pm-group-leader-small pm-difl">
                         <a href="<?php echo $profile_url ;?>"><?php echo $pmrequests->pm_get_display_name($group_leader);?></a>
                <?php echo get_avatar($group_leader,16,'',false,array('class'=>'pm-infl'));?>
            
                        </div>
                 </div>
        		<?php endif; ?>
         
         
                 
                 
                 <div class="pm-card-row pm-dbfl">
                    <div class="pm-card-label pm-difl"><?php _e('Members','profile-grid');?></div>
                    <div class="pm-card-value pm-difl"><?php echo $total_users?></div>
                 </div>
                 
                 <?php /*?><div class="pm-card-row pm-blfl">
                    <div class="pm-card-label pm-infl">Formed</div>
                    <div class="pm-card-value pm-infl">24th July, 1144</div>
                 </div><?php */?>
                 
                  <div class="pm-card-row pm-dbfl">
                    <div class="pm-card-label pm-difl"><?php _e('Details','profile-grid');?></div>
                    <div class="pm-card-value pm-difl"><?php echo $row->group_desc;?></div>
                 </div>
                 <?php do_action('profile_magic_show_group_fields_option',$options);?>
             </div>
           </div>
           
           

        </div>
    
     <?php endif;?>
    <?php if(has_action('profile_magic_group_photos_tab')):?>
    <div id="pg_group_tabs" class="pm-section-nav-horizental pm-dbfl">
        <ul class="pm-difl pm-profile-tab-wrap pm-border-bt">	
        <li class="pm-profile-tab pm-pad10"><a class="pm-dbfl" href="#pg_members"><?php _e('Group Members','profile-grid');?></a></li>
                <?php do_action( 'profile_magic_group_photos_tab',$current_user->ID,$gid);?>
        </ul>
<?php do_action( 'profile_magic_group_photos_tab_content',$current_user->ID,$gid);?>
      <div id="pg_members" class="pm-dbfl">
<?php
        $pmhtmlcreator = new PM_HTML_Creator($this->profile_magic,$this->version);
        if(!empty($users))
        {
            foreach($users as $user) 
            {

                     $pmhtmlcreator->get_group_page_fields_html($user->ID,$gid,$group_leader,150,array('class'=>'user-profile-image'));
            }
        }
        else
        {
            _e('No User Profile is registered in this Group','profile-grid');
        }
	
	echo '<div class="pm_clear"></div>'.$pagination;
	

?>
          </div>
      </div>
    <?php else: ?>
        <div id="pg_members" class="pm-dbfl">
<?php
        $pmhtmlcreator = new PM_HTML_Creator($this->profile_magic,$this->version);
        if(!empty($users))
        {
            foreach($users as $user) 
            {

                     $pmhtmlcreator->get_group_page_fields_html($user->ID,$gid,$group_leader,150,array('class'=>'user-profile-image'));
            }
        }
        else
        {
            _e('No User Profile is registered in this Group','profile-grid');
        }
	
	echo '<div class="pm_clear"></div>'.$pagination;
	

?>
          </div>
   <?php endif;?>
            
   
    </div>
