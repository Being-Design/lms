<?php
global $wpdb;
$limit =  isset($_GET['limit'])? intVal($_GET['limit']):25;
$listpage = isset($_GET['listpage'])? intVal($_GET['listpage']):0;
$offset = $listpage*$limit;

$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->usermeta WHERE meta_key=%s AND meta_value=%d", 'learndash_plus_level', $id ) );

$from = ($count > $offset)? $offset + 1:"0";
$to = ($offset + $limit > $count)? $count: $offset + $limit;

if($offset + $limit < $count)
$nextlistpage = $listpage + 1;
else
$nextlistpage = $listpage;

if($listpage > 0)
$prevlistpage = $listpage - 1;
else
$prevlistpage = 0;

$numberofpages = ceil($count/$limit);
//echo 'meta_key=learndash_plus_level&meta_value='.$id.'&count_total=true&orderby=nicename&offset='.$offset.'&number='.$limit;
$users = get_users('meta_key=learndash_plus_level&meta_value='.$id.'&count_total=true&orderby=nicename&offset='.$offset.'&number='.$limit);

/*
echo "<pre>";
print_r($users);
echo "</pre>";
*/
		$nexturl = get_admin_url(null, 'admin.php?page=learndash-plus-levels&id='.$id.'&listpage='.$nextlistpage.'&limit='.$limit);
		$prevurl = get_admin_url(null, 'admin.php?page=learndash-plus-levels&id='.$id.'&listpage='.$prevlistpage.'&limit='.$limit);

?>
<h2><?php _e('Users with this Access', 'learndash-plus') ?></h2>
<?php _e('Page', 'learndash-plus') ?> <select id='learndash_plus_page_select'>
<?php for($i = 0; $i < $numberofpages; $i ++) { ?>
	<option value="<?php echo get_admin_url(null, 'admin.php?page=learndash-plus-levels&id='.$id.'&listpage='.$i.'&limit='.$limit);?>"><?php echo $i + 1; ?></option>
<?php } ?>
</select> 
<input type="button" value="<?php _e('Go', 'learndash-plus') ?>" class="button-secondary action" onClick="window.location.href=document.getElementById('learndash_plus_page_select').value;" name="">
<br>
<?php 
	if($listpage > 0) echo "<a href='".$prevurl."'><< " . __('Prev', 'learndash-plus') . "</a> &nbsp;&nbsp;&nbsp;";
	if($nextlistpage != $listpage) echo "<a href='".$nexturl."'>" . __('Next', 'learndash-plus') . " >></a>"; 
?>
<br><br>
<?php _e('Showing', 'learndash-plus') ?> <b><?php echo $from; if($from != "0") { ?></b> <?php _e('to', 'learndash-plus') ?> <b><?php echo $to; } ?></b> <?php _e('out of total', 'learndash-plus') ?> <b><?php echo $count; ?></b>.<br>

<table cellspacing="0" class="wp-list-table widefat fixed learndash-plus-levels">
	<thead>
	<tr>
		<th style="" class="manage-column column-cb column" id="cb" scope="col" width="100px"><span><?php _e('User ID', 'learndash-plus') ?></span></th>
		
		<th style="" class="manage-column column-username " id="level" scope="col">
			<span><?php _e('Username', 'learndash-plus') ?></span>
		</th>
		
		<th style="" class="manage-column column-email " id="nextlevel" scope="col">
			<span><?php _e('Name', 'learndash-plus') ?></span>
		</th>
		
		<th style="" class="manage-column column-role" id="role" scope="col"><?php _e('Email', 'learndash-plus') ?></th>
	</tr>	
	</thead>

	<tfoot>
	<tr>
		<th style="" class="manage-column column-cb column" id="cb" scope="col" width="50px"><span><?php _e('User ID', 'learndash-plus') ?></span></th>
		
		<th style="" class="manage-column column-username " id="level" scope="col">
			<span><?php _e('Username', 'learndash-plus') ?></span>
		</th>
		
		<th style="" class="manage-column column-email " id="nextlevel" scope="col">
			<span><?php _e('Name', 'learndash-plus') ?></span>
		</th>
		
		<th style="" class="manage-column column-role" id="role" scope="col"><?php _e('Email', 'learndash-plus') ?></th>
	</tr>	
	</tfoot>

	<tbody class="list:membership-levels" id="the-list">
	<?php foreach($users as $user) { 
		$id = $user->ID;
		$name = $user->display_name;
		$username = $user->user_login;
		$email = $user->user_email;
		$editurl = get_admin_url(null, 'user-edit.php?user_id='.$id);
	?>
	<tr class="alternate" id="learndash-plus-level-1">
		<th class="column num" scope="row"><?php echo $id; ?></th>
				
		<td class="column"><a href="<?php echo $editurl; ?>"><?php echo $username; ?></a></td>
		
		<td class="column"><?php echo $name; ?></td>
		
		<td class="column"><?php echo $email; ?></td>
	</tr>
	<?php } ?>
	</tbody>
</table>
<br>
<?php _e('Showing', 'learndash-plus') ?> <b><?php echo $from; if($from != "0") { ?></b> <?php _e('to', 'learndash-plus') ?> <b><?php echo $to; } ?></b> <?php _e('out of total', 'learndash-plus') ?> <b><?php echo $count; ?></b>.<br>

<br>
<?php 
	if($listpage > 0)  echo "<a href='".$prevurl."'><< " . __('Prev', 'learndash-plus') . "</a> &nbsp;&nbsp;&nbsp;";
	if($nextlistpage != $listpage) echo "<a href='".$nexturl."'>" . __('Next', 'learndash-plus') . " >></a>"; 
?>
<br>
<?php _e('Page', 'learndash-plus') ?> <select id='learndash_plus_page_select'>
<?php for($i = 0; $i < $numberofpages; $i ++) { ?>
	<option value="<?php echo get_admin_url(null, 'admin.php?page=learndash-plus-levels&id='.$id.'&listpage='.$i.'&limit='.$limit);?>"><?php echo $i + 1; ?></option>
<?php } ?>
</select>
<input type="button" value="<?php _e('Go', 'learndash-plus') ?>" class="button-secondary action" onClick="window.location.href=document.getElementById('learndash_plus_page_select').value;" name="">
<br>
