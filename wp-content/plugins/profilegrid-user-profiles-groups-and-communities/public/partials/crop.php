<?php
$dbhandler = new PM_DBhandler;
$pmrequests = new PM_request;
$current_user = wp_get_current_user();
$uploads =  wp_upload_dir();
$post = isset($_POST) ? $_POST: array();
$filefield = $_FILES['photoimg'];
$allowed_ext ='jpg|jpeg|png|gif';
$targ_w = $targ_h = 150;
$jpeg_quality = 90;
 switch($post['status']) {
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
        if($post['user_meta']=='pm_user_avatar')
        {
            $image_attribute = wp_get_attachment_image_src($post['attachment_id'],array(150,150));
            $basename = basename($image_attribute[0]);
        }
        
        $image->save( $uploads['path']. '/'.$basename );
        update_user_meta($post['user_id'],$post['user_meta'],$post['attachment_id']);
        echo "<img id='photofinal' file-name='".$basename."' src='".$image_attribute[0]."' class='preview'/>";
    }
    die;
  break;
  default:
        //changeAvatar();
    if($post['user_id']==$current_user->ID)
    {
        $attachment_id = $pmrequests->make_upload_and_get_attached_id($filefield,$allowed_ext,array(150,150));
        if(is_numeric($attachment_id))
        {
        $image_attribute = wp_get_attachment_image_src($attachment_id,'full');
        
        echo "<img id='photo' file-name='". basename($image_attribute[0])."' src='".$image_attribute[0]."' class='preview'/>";
        echo "<input type='hidden' name='fullpath' id='fullpath' value='".$image_attribute[0]."' />";
        echo "<input type='hidden' name='truewidth' id='truewidth' value='".$image_attribute[1]."' />";
        echo "<input type='hidden' name='trueheight' id='trueheight' value='".$image_attribute[2]."' />";
        echo "<input type='hidden' name='attachment_id' id='attachment_id' value='".$attachment_id."' />";
        }
        else
        {
            echo '<p class="pm-popup-error" style="display:block;">'.$attachment_id.'</p>';
        }
        
        //echo $attachment_id;

    }
    die;

 }
?>
