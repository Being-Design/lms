<?php
    //must check that the user has the required capability 
    if (!current_user_can('manage_options'))
    {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }
	$addnewurl = get_admin_url(null, 'admin.php?page=learndash-plus-levels&id=0');
	$listurl = get_admin_url(null, 'admin.php?page=learndash-plus-levels');
?>
<div class=wrap>
<h2><?php _e('Access Levels', 'learndash-plus') ?> <a class="add-new-h2" href="<?php echo $addnewurl; ?>"><?php _e('Add New', 'learndash-plus') ?></a> <a class="add-new-h2" href="<?php echo $listurl; ?>"><?php _e('List All', 'learndash-plus') ?></a></h2><br><br>

<?php	
	if(!isset($_REQUEST["id"])) 
		include(dirname(__FILE__)."/levels_list.php");
	else
		include(dirname(__FILE__)."/levels_edit.php");
	
?>

</div>