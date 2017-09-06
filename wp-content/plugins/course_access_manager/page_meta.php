<?php
/* Define the custom box */

add_action( 'add_meta_boxes', 'learndash_plus_add_custom_box' );

// backwards compatible (before WP 3.0)
// add_action( 'admin_init', 'learndash_plus_add_custom_box', 1 );

/* Do something with the data entered */
add_action( 'save_post', 'learndash_plus_save_postdata' );

function learndash_plus_add_custom_box() {
    $post_types = get_post_types();
	foreach ($post_types as $post_type) {
        add_meta_box(
            'learndash_plus',
            __( 'Course Access', 'learndash-plus' ),
            'learndash_plus_page_box',
            $post_type
        );
    }
}



/* Prints the box content */
function learndash_plus_page_box( $post ) {

	// Use nonce for verification
	wp_nonce_field( plugin_basename( __FILE__ ), 'learndash_plus_nonce' );

	// The actual fields for data entry
	// Use get_post_meta to retrieve an existing value from the database and use the value for the form

	$data = get_post_meta( $post->ID, $key = '_learndash_plus', $single = true );

	$protect_checked = !empty($data['protect'])? "CHECKED":"";
	$showlevels = !empty($data['protect'])? "display:block;":"display:none;";
	
	$selectedlevels = !empty($data['selectedlevels'])? $data['selectedlevels']:array();

	$levels = learndash_plus_get_levels();
	
	?>
	<script>
	function ld_mem_showhide()
	{
		var protect = document.getElementById('learndash_plus_protect').checked;
		if(protect)
		document.getElementById('learndash_plus_levels').style.display = "block";
		else
		document.getElementById('learndash_plus_levels').style.display = "none";
	}
	</script>
	<label for="learndash_plus_protect">
       <b><?php echo _e("Enable protection for this post ", 'learndash-plus' ); ?> </b> 
	</label>
	<input type="checkbox" id="learndash_plus_protect" name="learndash_plus_protect" <?php echo $protect_checked; ?> onClick="ld_mem_showhide();"/>
	<br><br>
	<div id="learndash_plus_levels" style="<?php echo $showlevels; ?>">
	<label for="learndash_plus_levels">
       <b><?php echo _e("Allow access to ", 'learndash-plus' ); ?> </b> 
	</label>
	<br><br>
		<div style=' margin-left: 10px;'>
		<?php foreach($levels as $level) { 
			$checked = in_array($level['id'],$selectedlevels)? 'checked="checked"':"";
		?>
		<input type="checkbox" name="learndash_plus_levels[<?php echo $level['id']; ?>]" <?php echo $checked; ?> /> <?php echo $level['name']; ?><br>
		<?php } ?>
		</div>
	</div>
	<?php 
}

/* When the post is saved, saves our custom data */
function learndash_plus_save_postdata( $post_id ) {
  // verify if this is an auto save routine. 
  // If it is our form has not been submitted, so we dont want to do anything
  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
      return;

  // verify this came from the our screen and with proper authorization,
  // because save_post can be triggered at other times

  if ( !isset( $_POST['learndash_plus_nonce'] ) || !wp_verify_nonce( $_POST['learndash_plus_nonce'], plugin_basename( __FILE__ ) ) )
      return;

  
  // Check permissions
  if ( 'page' == $_POST['post_type'] ) 
  {
    if ( !current_user_can( 'edit_page', $post_id ) )
        return;
  }
  else
  {
    if ( !current_user_can( 'edit_post', $post_id ) )
        return;
  }

  // OK, we're authenticated: we need to find and save the data

  //sanitize user input
  //$mydata = sanitize_text_field( $_POST['learndash_plus_protect'] );
  $protect = isset( $_POST['learndash_plus_protect'] );
  $mlevels = isset($_POST['learndash_plus_levels'])? $_POST['learndash_plus_levels']:array();
  $selectedlevels = array();
  foreach($mlevels as $k => $v)
	$selectedlevels[] = $k;
	
  $data = array('protect' =>  $protect, 'selectedlevels' => $selectedlevels);
  
  add_post_meta($post_id, '_learndash_plus', $data, true) or
  update_post_meta($post_id, '_learndash_plus', $data);
}