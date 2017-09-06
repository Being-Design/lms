<?php
$dbhandler = new PM_DBhandler;
$pmrequests = new PM_request;
$pmemails = new PM_Emails;
$textdomain = $this->profile_magic;
$path =  plugin_dir_url(__FILE__); 

$pagenum = filter_input(INPUT_GET, 'pagenum');
$gid = filter_input(INPUT_GET, 'gid');
$field_identifier = 'FIELDS';
$group_identifier = 'GROUPS';
$current_user = wp_get_current_user();
$pagenum = isset($pagenum) ? absint($pagenum) : 1;
$limit = 10; // number of rows in page
$offset = ( $pagenum - 1 ) * $limit;
//if(filter_input(INPUT_GET,'result')=='Search'){$pagenum=1;}
if(filter_input(INPUT_GET,'deactivate'))
{
	$selected = filter_input(INPUT_GET, 'selected', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
	if(isset($selected)):
	foreach($selected as $uid)
	{
		update_user_meta( $uid,'rm_user_status','1');
		$gid = get_user_meta($uid,'pm_group',true);
		$pmemails->pm_send_group_based_notification($gid,$uid,'on_user_deactivate');
	}
	endif;
}

if(filter_input(INPUT_GET,'activate'))
{
	$selected = filter_input(INPUT_GET, 'selected', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
	if(isset($selected)):
	foreach($selected as $uid)
	{
		update_user_meta( $uid,'rm_user_status','0');
		$gid = get_user_meta($uid,'pm_group',true);
		$pmemails->pm_send_group_based_notification($gid,$uid,'on_user_activate');
	}
	endif;
}

if(filter_input(INPUT_GET,'delete'))
{
	$selected = filter_input(INPUT_GET, 'selected', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
	foreach($selected as $uid)
	{
		wp_delete_user( $uid );
	}
}

if(filter_input(INPUT_GET,'move'))
{
	$selected = filter_input(INPUT_GET, 'selected', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
	foreach($selected as $uid)
        {
            update_user_meta($uid, 'pm_group', $_GET['pm_group']);
        }
}


if(filter_input(INPUT_GET,'reset'))
{
	wp_redirect('admin.php?page=pm_user_manager');exit;
}

$meta_query_array = $pmrequests->pm_get_user_meta_query($_GET);
$date_query = $pmrequests->pm_get_user_date_query($_GET);

if(isset($_GET['search'])) $search = $_GET['search'];else $search = '';

$groups =  $dbhandler->get_all_result('GROUPS','id,group_name');
$users =  $dbhandler->pm_get_all_users($search,$meta_query_array,'',$offset,$limit,'ASC','ID',array(),$date_query);
$total_users = count($dbhandler->pm_get_all_users($search,$meta_query_array,'','','','ASC','ID',array(),$date_query));
$num_of_pages = ceil( $total_users/$limit);
$pagination = $dbhandler->pm_get_pagination($num_of_pages,$pagenum);
?>

<div class="pmagic"> 
  
  <!-----Operationsbar Starts----->
  <form name="user_manager" id="user_manager" action="" method="get">
  <input type="hidden" name="page" value="pm_user_manager" />
  <input type="hidden" id="pagenum" name="pagenum" value="1" />
  <div class="operationsbar">
    <div class="pmtitle">
      <?php _e('Users Profiles','profile-grid');?>
    </div>
   
    <div class="nav">
      <ul>
        <li><a href="user-new.php"><i class="fa fa-user-plus" aria-hidden="true"></i><?php _e("New User",'profile-grid');?></a></li>
        <li class="pm_action_button"><input type="submit" name="deactivate" value="<?php _e("Deactivate",'profile-grid');?>" /></li>
        <li class="pm_action_button"><input type="submit" name="activate" value="<?php _e("Activate",'profile-grid');?>" /></li>
        <li class="pm_action_button"><input type="submit" name="delete" value="<?php _e("Delete",'profile-grid');?>" onclick="return pg_confirm('<?php _e('You are going to delete selected users permanently. This action cannot be undone. Please confirm.','profile-grid');?>')" /></a></li>
        <li class="pm_action_button"><input type="button" name="move" value="<?php _e("Move",'profile-grid');?>" onclick="jQuery('.pm-popup').css('visibility', 'visible');" /></a></li>
        <li class="pm-form-toggle">
            <select name="gid" id="gid" onChange="jQuery('#pagenum').val(1);submit()">
            <option value=""><?php _e('Select A Group','profile-grid');?></option>
            <?php
                          foreach($groups as $group)
                          {?>
            <option value="<?php echo $group->id;?>" <?php if(!empty($gid))selected($gid,$group->id);?>><?php echo $group->group_name; ?></option>
            <?php }
                          ?>
          </select>
        </li>
      </ul>      
    </div>
  </div>
  <div class="pm-popup pm-move-to-group pm-popup-height-auto" >
       <div class="pm-popup-header">
            <div class="pm-popup-title">
                Move to group    </div>
                <img class="pm-popup-close" src="<?php echo $path;?>/images/close-pm.png">
       </div>
       
      <div class="pm-popup-field-name" style="padding:15px;" >
                <select name="pm_group" id="gid" >
                       <option value=""><?php _e('Select A Group','profile-grid');?></option>
                       <?php
                                     foreach($groups as $group)
                                     {?>
                       <option value="<?php echo $group->id;?>" <?php if(!empty($gid))selected($gid,$group->id);?>><?php echo $group->group_name; ?></option>
                       <?php }
                                     ?>
                     </select>
               <input type="submit" name="move" value="<?php _e("Move",'profile-grid');?>"style="padding-left:20px;"/>

               <p class="pm-warning"> <?php _e('You are changing the group of this user. All data associated with profile fields of old group will be hidden and the user will have to edit and fill profile fields associated with the new group.','profile-grid'); ?></p>
       
           </div>
    </div>
  <!--------Operationsbar Ends-----> 
  
  <!-------Contentarea Starts-----> 
  
  <!----Table Wrapper---->
  
  <div class="pmagic-table"> 
    
    <!----Sidebar---->
    
    <div class="sidebar">
      <div class="sb-filter"><?php _e("Search",'profile-grid');?>
        <input type="text" class="sb-search" name="search" id="search" value="<?php if(isset($_GET['search'])) echo $_GET['search'];?>">
      </div>
      <?php if(isset($_GET['search']) && $_GET['search']!=''):?>
      <div class="sb-search-keyword" id="search_keyword"><?php echo $_GET['search'];?> <span onclick="show_hide_search_text()">x</span></div>
      <?php endif;?>
      <div class="sb-filter"> <?php _e("Time",'profile-grid');?>
      <div class="filter-row">
          <input type="radio" class="sel_pm_user_time" name="time" value="all" onclick="pm_show_hide(this,'','datehtml')" <?php if(isset($_GET['time']) && $_GET['time']=='all') echo 'checked="checked"';?> >
          <?php _e("All",'profile-grid');?> </div>
        <div class="filter-row">
          <input type="radio" class="sel_pm_user_time" name="time" value="today" onclick="pm_show_hide(this,'','datehtml')" <?php if(isset($_GET['time']) && $_GET['time']=='today') echo 'checked="checked"';?> >
          <?php _e("Today",'profile-grid');?> </div>
        <div class="filter-row">
          <input type="radio" class="sel_pm_user_time" name="time" value="yesterday" onclick="pm_show_hide(this,'','datehtml')" <?php if(isset($_GET['time']) && $_GET['time']=='yesterday') echo 'checked="checked"';?>>
          <?php _e("Yesterday",'profile-grid');?> </div>
        <div class="filter-row">
          <input type="radio" class="sel_pm_user_time" name="time" value="this_week" onclick="pm_show_hide(this,'','datehtml')" <?php if(isset($_GET['time']) && $_GET['time']=='this_week') echo 'checked="checked"';?>>
          <?php _e("This Week",'profile-grid');?> </div>
        <div class="filter-row">
          <input type="radio" class="sel_pm_user_time" name="time" value="last_week" onclick="pm_show_hide(this,'','datehtml')" <?php if(isset($_GET['time']) && $_GET['time']=='last_week') echo 'checked="checked"';?>>
          <?php _e("Last Week",'profile-grid');?> </div>
        <div class="filter-row">
          <input type="radio" class="sel_pm_user_time" name="time" value="this_month" onclick="pm_show_hide(this,'','datehtml')" <?php if(isset($_GET['time']) && $_GET['time']=='this_month') echo 'checked="checked"';?>>
          <?php _e("This Month",'profile-grid');?> </div>
        <div class="filter-row">
          <input type="radio" class="sel_pm_user_time" name="time" value="this_year" onclick="pm_show_hide(this,'','datehtml')" <?php if(isset($_GET['time']) && $_GET['time']=='this_year') echo 'checked="checked"';?>>
          <?php _e("This Year",'profile-grid');?> </div>
        <div class="filter-row">
          <input type="radio" name="time" value="specific" onclick="pm_show_hide(this,'datehtml')" <?php if(isset($_GET['time']) && $_GET['time']=='specific') echo 'checked="checked"';?>>
          <?php _e("Specific Period",'profile-grid');?> </div>
          <div id="datehtml" style=" <?php if(isset($_GET['time']) && $_GET['time']=='specific') echo 'display:block'; else echo 'display:none;';?>">
        <div class="filter-row" id="">
       <?php _e("Start Date",'profile-grid');?>
          <input type="text" class="sb-search pm_calendar" name="start_date" value="<?php if(isset($_GET['start_date'])) echo $_GET['start_date'];?>">
        </div>
        <div class="filter-row">
        <?php _e("End Date",'profile-grid');?>
          <input type="text" class="sb-search pm_calendar" name="end_date" value="<?php if(isset($_GET['end_date'])) echo $_GET['end_date'];?>">
        </div>
        </div>
      	</div>
        
        
      <div class="sb-filter"> <?php _e("Status",'profile-grid');?>
       <div class="filter-row">
          <input type="radio" class="sel_pm_user_status" name="status" value="all" <?php if(isset($_GET['status']) && $_GET['status']=='all') echo 'checked="checked"';?>>
          <?php _e("All",'profile-grid');?> </div>
        <div class="filter-row">
          <input type="radio" class="sel_pm_user_status" name="status" value="0" <?php if(isset($_GET['status']) && $_GET['status']=='0') echo 'checked="checked"';?>>
          <?php _e("Active",'profile-grid');?> </div>
        <div class="filter-row">
          <input type="radio" class="sel_pm_user_status" name="status" value="1" <?php if(isset($_GET['status']) && $_GET['status']=='1') echo 'checked="checked"';?>>
          <?php _e("Inactive",'profile-grid');?> </div>
      </div>
      <div class="sb-filter"> <?php _e("Match Field",'profile-grid');?>
        <div class="filter-row">
        <?php
		$fields =  $dbhandler->get_all_result('FIELDS');
		echo '<select name="match_field" id="match_field" class="sb-search">';
		foreach($fields as $field)
		{
                    $exclude = array('file','user_avatar','heading','paragraph','confirm_pass','user_pass');
                    if (!in_array($field->field_type, $exclude))
                    {
			echo '<option value="'.$field->field_key.'">'.$field->field_name.'</option>';	
                    }
		}
		echo '</select>';
		?>
        </div>
        <div class="filter-row">
          <input type="text" class="sb-search" name="field_value" value="">
        </div>
        <div class="filter-row">
          <input type="submit" name="result" value="<?php _e("Search",'profile-grid');?>">
          <input type="submit" name="reset" value="<?php _e("Reset",'profile-grid');?>">
        </div>
      </div>
    </div>
    <table>
      <tr>
        <th>&nbsp;</th>
        <th><?php _e('Image','profile-grid');?></th>
        <th><?php _e('Display Name','profile-grid');?></th>
        <th><?php _e('User Email','profile-grid');?></th>
        <th><?php _e('Status','profile-grid');?></th>
        <th><?php _e('Action','profile-grid');?></th>
      </tr>
      <?php
                        if(!empty($users))
                        {
                            foreach($users as $entry)
                            {
                                    //print_r($entry);
                                    $avatar = get_avatar($entry->user_email, 30 );
                                    $userstatus = get_user_meta($entry->ID, 'rm_user_status', true );
                                    if ($entry->ID == $current_user->ID )
                                    {
                                            $class='pm_current_user';	
                                            $attr = 'disabled="disabled"';			
                                    }
                                    else
                                    {
                                            $attr = '';
                                            $class='';
                                    } ?>
          <tr class="<?php echo $class;?>">
            <td><input type="checkbox" name="selected[]" value="<?php echo $entry->ID; ?>" <?php echo $attr;?> /></td>
            <td><div class="tableimg"> <a href="admin.php?page=pm_profile_view&id=<?php echo $entry->ID;?>"><?php echo $avatar;?></a> </div></td>
            <td><?php echo $entry->display_name;?></td>
            <td><?php echo $entry->user_email;?></td>
            <td><?php echo ($pmrequests->profile_magic_get_user_field_value($entry->ID,'rm_user_status')==1)?__('Inactive','profile-grid'):__('Active','profile-grid');?></td>
            <td><a href="admin.php?page=pm_profile_view&id=<?php echo $entry->ID;?>"><i class="fa fa-eye" aria-hidden="true"></i><?php _e('View','profile-grid');?></a></td>
          </tr>
          <?php } 
                        }
                        else
                        {
                            echo '<tr><td></td><td>';
                             _e('No user matches your search.','profile-grid');
                            echo '<td><td></td><td></td><td></td><td></td></tr>';
                        }
?>
    </table>
  </div>
  <?php echo $pagination;?>
  </form>
</div>
