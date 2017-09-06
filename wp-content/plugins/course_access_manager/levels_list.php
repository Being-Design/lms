<?php
$levels = learndash_plus_get_levels();
	if( isset($_GET[ "action" ]) &&  isset($_GET[ "accessid" ]) && $_GET[ "action" ] == 'delete_LearnDashMembershipLevels') {
        // Read their posted value
		
        $id   =  $_GET[ "accessid" ];
		$level_all = learndash_plus_get_levels();
		unset($level_all[$id]); //Delete the current level
		$learndash_plus_levels_options = get_option( 'learndash_plus_levels_options');
		$learndash_plus_levels_options['levels'] = $level_all;
		update_option('learndash_plus_levels_options', $learndash_plus_levels_options);
        // Put an level deleted message on the screen
		$url = get_admin_url(null, 'admin.php?page=learndash-plus-levels');
	?>
	<div class="updated"><p><strong><?php _e('Level Deleted', 'learndash-plus') ; ?></strong></p></div>
	<meta http-equiv="refresh" content="0; url=<?php echo $url; ?>"> 

	<?php
    }
?>
	
<table cellspacing="0" class="wp-list-table widefat fixed learndash-plus-levels">
	<thead>
	<tr>
		<th style="" class="manage-column column-cb column" id="cb" scope="col" width="50px"><span>ID</span></th>
		
		<th style="" class="manage-column column-username " id="level" scope="col">
			<span><?php _e('Access Level', 'learndash-plus') ?></span>
		</th>
		
		<th style="" class="manage-column column-email " id="nextlevel" scope="col">
			<span><?php _e('Next Level', 'learndash-plus') ?></span>
		</th>
		
		<th style="" class="manage-column column-email " id="nextlevel" scope="col">
			<span><?php _e('Delete Level', 'learndash-plus') ?></span>
		</th>
		
		<th style="" class="manage-column column-role" id="role" scope="col"><?php _e('Associated Role', 'learndash-plus') ?></th>
	</tr>	
	</thead>

	<tfoot>
	<tr>
		<th style="" class="manage-column column-cb column" id="cb" scope="col" width="50px"><span><?php _e('ID', 'learndash-plus') ?></span></th>
		
		<th style="" class="manage-column column-username " id="level" scope="col">
			<span><?php _e('Access Level', 'learndash-plus') ?></span>
		</th>
		
		<th style="" class="manage-column column-email " id="nextlevel" scope="col">
			<span><?php _e('Next Level', 'learndash-plus') ?></span>
		</th>
		
		<th style="" class="manage-column column-email " id="nextlevel" scope="col">
			<span><?php _e('Delete Level', 'learndash-plus') ?></span>
		</th>
		
		<th style="" class="manage-column column-role" id="role" scope="col"><?php _e('Associated Role', 'learndash-plus') ?></th>
	</tr>	
	</tfoot>

	<tbody class="list:membership-levels" id="the-list">
	<?php foreach($levels as $level) { 
		$id = $level['id'];
		$name = $level['name'];
		$role = $level['role'];
		
		$nextlevelid = $level['nextlevel'];
		if(isset($levels[$nextlevelid])) {
			$nextlevelname = $levels[$nextlevelid]['name'];
			$nextlevel = '<a href="'.get_admin_url(null, 'admin.php?page=learndash-plus-levels&id='.$nextlevelid).'">'.$nextlevelname.'</a>';
		}
		else
			$nextlevel = __("None", 'learndash-plus');
	?>
	<tr class="alternate" id="learndash-plus-level-1">
		<th class="column num" scope="row"><?php echo $id; ?></th>
				
		<td class="column"><a href="<?php echo get_admin_url(null, 'admin.php?page=learndash-plus-levels&id='.$id);?> "><?php echo $name; ?></a></td>
		
		<td class="column"><?php echo $nextlevel; ?></td>
		
		<td class="column"><a href="<?php echo get_admin_url(null, 'admin.php?page=learndash-plus-levels&action=delete_LearnDashMembershipLevels&accessid='.$id);?> "><?php _e('delete', 'learndash-plus') ?></td>
		
		<td class="column"><?php echo $role; ?></td>
	</tr>
	<?php } ?>
	</tbody>
</table>

