<?php
$dbhandler = new PM_DBhandler;
$textdomain = $this->profile_magic;
$path =  plugin_dir_url(__FILE__); 
$identifier = 'EMAIL_TMPL';
$pagenum = filter_input(INPUT_GET, 'pagenum');
$pagenum = isset($pagenum) ? absint($pagenum) : 1;
$limit = 20; // number of rows in page
$offset = ( $pagenum - 1 ) * $limit;
$i = 1 + $offset;
$totalemails = $dbhandler->pm_count($identifier);
$emails =  $dbhandler->get_all_result($identifier,'*',1,'results',$offset,$limit,'id');
$num_of_pages = ceil( $totalemails/$limit);
$pagination = $dbhandler->pm_get_pagination($num_of_pages,$pagenum);
if(filter_input(INPUT_GET,'delete'))
{
	$selected = filter_input(INPUT_GET, 'selected', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
	foreach($selected as $tid)
	{
		$dbhandler->remove_row($identifier,'id',$tid,'%d');
	}
	wp_redirect('admin.php?page=pm_email_templates');exit;
}

?>

<div class="pmagic"> 
  
  <!-----Operationsbar Starts----->
  <form name="email_manager" id="email_manager" action="" method="get">
    <input type="hidden" name="page" value="pm_email_templates" />
    <input type="hidden" name="pagenum" value="<?php echo $pagenum;?>" />
    <div class="operationsbar">
      <div class="pmtitle">
        <?php _e('Email Templates','profile-grid');?>
      </div>
      <div class="nav">
        <ul>
          <li><a href="admin.php?page=pm_add_email_template">
              <i class="fa fa-plus" aria-hidden="true"></i>
            <?php _e('New Template','profile-grid');?>
            </a></li>
          <li><a>
            <input type="submit" name="delete" value="Delete" />
            </a></li>
        </ul>
      </div>
    </div>
    <!--------Operationsbar Ends-----> 
    
    <!-------Contentarea Starts-----> 
    
    <!----Table Wrapper---->
    <?php if(isset($emails) && !empty($emails)):?>
    <div class="pmagic-table"> 
      
      <!----Sidebar---->
      
      <table class="pg-email-list">
        <tr>
          <th>&nbsp;</th>
            <th>&nbsp;</th>
          <th><?php _e('SR','profile-grid');?></th>
          <th><?php _e('Name','profile-grid');?></th>
          <th><?php _e('Subject','profile-grid');?></th>
          <th><?php _e('Action','profile-grid');?></th>
        </tr>
        <?php
	 	
			foreach($emails as $email)
			{
				?>
        <tr>
          <td><input type="checkbox" name="selected[]" value="<?php echo $email->id; ?>" /></td>
          <td><i class="fa fa-envelope" aria-hidden="true"></i></td>
          <td><?php echo $i;?></td>
          <td><?php echo $email->tmpl_name;?></td>
          <td><?php echo $email->email_subject;?></td>
          <td><a href="admin.php?page=pm_add_email_template&id=<?php echo $email->id;?>">
<!--              <i class="fa fa-eye" aria-hidden="true"></i>-->
            <?php _e('Edit','profile-grid');?>
            </a></td>
        </tr>
        <?php $i++; }?>
      </table>
    </div>
    
    <?php echo $pagination;?>
    <?php else:?>
	<div class="pm_message"><?php _e('You haven’t created any email templates yet. Why don’t you go ahead and create one now!','profile-grid');?></div>
	<?php endif;?>
  </form>
</div>
