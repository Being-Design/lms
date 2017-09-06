<?php
$dbhandler = new PM_DBhandler;
$pmrequests = new PM_request;
$current_user = wp_get_current_user();
$uploads =  wp_upload_dir();
$post = isset($_POST) ? $_POST: array();
$filefield = $_FILES['coverimg'];
$allowed_ext ='jpg|jpeg|png|gif';
$targ_w = $targ_h = 150;
$jpeg_quality = 90;
 switch($post['cover_status']) {
  case 'cancel' :
      $delete = wp_delete_attachment( $post['attachment_id'],true );
      print_r($delete);
    die;
  break;
  
  case 'save' :
     //saveAvatarTmp();
     //echo 'test'.$post['fullpath'];die;
    $image = wp_get_image_editor( $post['fullpath'] );
       $image_attribute = wp_get_attachment_image_src($post['attachment_id'],'full');
      $basename = basename($post['fullpath']);
    if ( ! is_wp_error( $image ) ) {
        $image->crop( $post['x'], $post['y'], $post['w'], $post['h'], $post['w'], $post['h'], false );
        $image->resize( $post['w'], $post['h'], array($post['x'], $post['y']) );
        $image->save( $uploads['path']. '/'.$basename );
        update_user_meta($post['user_id'],$post['user_meta'],$post['attachment_id']);
        echo "<img id='coverphotofinal' file-name='".$basename."' src='".$image_attribute[0]."' class='preview'/>";
    }
    die;
  break;
  default:
        //changeAvatar();
    if($post['user_id']==$current_user->ID)
    {
        $attachment_id = $pmrequests->make_upload_and_get_attached_id($filefield,$allowed_ext,array($post['cover_minwidth'],300));
        if(is_numeric($attachment_id))
        {
            $image_attribute = wp_get_attachment_image_src($attachment_id,'full');

            echo "<img id='coverimage' file-name='". basename($image_attribute[0])."' src='".$image_attribute[0]."' class='preview'/>";
            echo "<input type='hidden' name='coverfullpath' id='coverfullpath' value='".$image_attribute[0]."' />";
            echo "<input type='hidden' name='covertruewidth' id='covertruewidth' value='".$image_attribute[1]."' />";
            echo "<input type='hidden' name='covertrueheight' id='covertrueheight' value='".$image_attribute[2]."' />";
            echo "<input type='hidden' name='cover_attachment_id' id='cover_attachment_id' value='".$attachment_id."' />";
         }
        else
        {
           echo '<p class="pm-popup-error" style="display:block;">'.$attachment_id.'</p>'; 
        }
        

    }
    die;

 }
?>
