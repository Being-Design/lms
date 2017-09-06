<?php $dbhandler = new PM_DBhandler;
$pmrequests = new PM_request;
?>
<div class="pmagic">
  <div class="pm-group-container pm-dbfl">
      <div class="pm-top-heading pm-dbfl pm-border"><h4><?php _e('All Groups','profile-grid');?></h4></div>
   <div class="pm-all-group-container pm-dbfl">
    <?php
    foreach($groups as $group) 
    {
            $group_url  = $pmrequests->profile_magic_get_frontend_url('pm_group_page','');
            $group_url = add_query_arg( 'gid',$group->id, $group_url );
            $registration_url  = $pmrequests->profile_magic_get_frontend_url('pm_registration_page','');
            $registration_url = add_query_arg( 'gid',$group->id, $registration_url );
    ?>
        <div class="pm-group pm-difl pm-border pm-radius5 pm-bg-lt">
          <div class="pm-group-heading pm-dbfl pm-border-bt pm-pad10 pm-clip"><a href="<?php echo $group_url;?>"><?php echo $group->group_name;?></a></div>
          <div class="pm-group-info pm-dbfl">
            <div class="pm-group-logo pm-dbfl pm-bg pm-border-bt">
                <div class="pm-group-logo-img">
                <?php echo $pmrequests->profile_magic_get_group_icon($group); ?>
                </div>
                <div class="pm-group-bg">
                <?php echo $pmrequests->profile_magic_get_group_icon($group); ?>
                </div>
            </div>
            <?php
                            $groupdesc = '';
                            if(strlen($group->group_desc) > 150)
                            {
                                    $groupdesc = substr($group->group_desc, 0, 150);
                                    $groupdesc .= "...";
                            }
                            else
                            {
                                    $groupdesc = $group->group_desc;
                            }


                    ?>
            <div class="pm-group-desc pm-dbfl pm-pad10"><?php echo $groupdesc;?></div>

          </div>
          <?php if (!is_user_logged_in()):?> 
          <div class="pm-group-button pm-dbfl pm-pad10">
           <div class="pm-group-signup"><a href="<?php echo $registration_url;?>" class="pm_button"><button><?php _e('Sign Up','profile-grid');?></button></a></div>
            <?php if($pmrequests->profile_magic_check_paid_group($group->id)>0):?>
            <div class="pm_group_price">
              <?php if($dbhandler->get_global_option_value('pm_currency_position','before')=='before'):
                    echo $pmrequests->pm_get_currency_symbol().' '.$pmrequests->profile_magic_check_paid_group($group->id);
                else:
                    echo $pmrequests->profile_magic_check_paid_group($group->id).' '.$pmrequests->pm_get_currency_symbol();
                endif;
                ?>
            </div>
            <?php else: ?>
            <div class="pm_free_group"><?php _e('Free','profile-grid');?></div>
            <?php endif; ?>
            </div>
          <?php endif;?>
        </div>
        <?php	
    }
    ?>
</div>
<div class="pm_clear"></div>
<?php echo $pagination;?>
  </div>
</div>
