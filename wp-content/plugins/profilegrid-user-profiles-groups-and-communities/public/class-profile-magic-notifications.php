<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Profile_Magic
 * @subpackage Profile_Magic/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Profile_Magic
 * @subpackage Profile_Magic/public
 * @author     Your Name <email@example.com>
 */
class Profile_Magic_Notification {

    /*

     * NOTIFICATION STATUS
     * status = 1---------NEW NOTIFICATION
     * status = 2---------READ
     * status = 3---------DELETE
     * status = 4---------SENT but UNREAD
     * 
     * 
     *      */
    
    
        /**
         * The ID of this plugin.
         *
         * @since    1.0.0
         * @access   private
         * @var      string    $profile_magic    The ID of this plugin.
         */
        private $profile_magic;

        /**
         * The version of this plugin.
         *
         * @since    1.0.0
         * @access   private
         * @var      string    $version    The current version of this plugin.
         */
        private $version;

        /**
         * Initialize the class and set its properties.
         *
         * @since    1.0.0
         * @param      string    $profile_magic       The name of the plugin.
         * @param      string    $version    The version of this plugin.
         */

     
        
        public function pm_notification_heartbeat_received($response, $data){
            
            
            $data['pm_notify'] =array();
            if($data['pm_notify_status'] != 'ready')
                    return;
            $dbhandler = new PM_DBhandler;
            $current_uid = get_current_user_id();
            $notification = $dbhandler->get_all_result('NOTIFICATION','*', array('status'=>1,'rid'=>$current_uid),'results', $offset=0, $limit=false, $sort_by = 'timestamp', $descending=true);
            $data['unread_notif']= $this->pm_get_user_unread_notification_count($current_uid);
            
           //return $notification;
		
            if ( empty( $notification ) )
                {
                    return $data;
                }
            else
            {
             
                foreach ( $notification as $db_notification )
                {
                    // set id of each notification
                    $id = $db_notification->id;
                    $type = $db_notification->type;
                    switch($type)
                    {
                    case  'comment'             :  $data['pm_notify'][$id] = $this->pm_generate_comment_notice($db_notification,$id);
                                                    break;
                    case  'BlogPost'            :  $data['pm_notify'][$id] = $this->pm_generate_blog_post_notice($db_notification,$id);
                                                    break;
                    case   'FriendAdded'        : $data['pm_notify'][$id] = $this->pm_generate_friend_added_notice($db_notification, $id);
                                                    break;
                    case   'FriendRequest'      : $data['pm_notify'][$id] = $this->pm_generate_friend_request_notice($db_notification, $id);
                                                    break; 
                    }
                  
                 $this->pm_change_notification_status($id,4);
                    
                    }
                 return $data;
            }
           
           
        }

        public function pm_generate_notification_without_heartbeat($loadnum=1){
            

            $dbhandler = new PM_DBhandler;
            $current_uid = get_current_user_id();
            $loadnum = isset($loadnum) ? absint($loadnum) : 1;
            $limit = 15;
            $offset = ( $loadnum - 1 ) * $limit;
        
            $where =1;
            $additional  = " status in (1,2,4) AND rid= $current_uid ";
            $notification = $dbhandler->get_all_result('NOTIFICATION','*',$where,'results', $offset, $limit, $sort_by = 'timestamp', $descending=true,$additional);
            $count = 0;
		
            if ( empty( $notification ) )
            {
                    echo "Thats it for today. You are all caught up!";
            }
            else
            {

                    foreach ( $notification as $db_notification )
                    {
                    $count++;
                    $id = $db_notification->id;
                    $type = $db_notification->type;
                    switch($type)
                    {
                    case  'comment'             :  $data['pm_notify'][$id] = $this->pm_generate_comment_notice($db_notification,$id);
                                                    break;
                    case  'BlogPost'            :  $data['pm_notify'][$id] = $this->pm_generate_blog_post_notice($db_notification,$id);
                                                    break;
                    case   'FriendAdded'        : $data['pm_notify'][$id] = $this->pm_generate_friend_added_notice($db_notification, $id);
                                                    break;
                    case   'FriendRequest'      : $data['pm_notify'][$id] = $this->pm_generate_friend_request_notice($db_notification, $id);
                                                    break; 
                    default                     : $data = "no new notification";
                                                    break;
                    }
                        echo $data['pm_notify'][$id];
                    }  
                    if($count ==$limit){
                    $new_loadnum = $loadnum+1;
                    echo' <div id="pm_load_more_notif" class="pm-dbfl" onclick="pm_load_more_notification('.$new_loadnum.')" >Load More..</div>';
                    }
                
            }
         
            

            
        }
     
     
/*-----------------NOTIFICATION CREATION FUNCTIONS------------------*/  
        
           
        public function pm_add_comment_notification($comment_ID, $comment_approved){
            $dbhandler = new PM_DBhandler;
            $comment = get_comment( $comment_ID );
            $rid = get_post_field( 'post_author',$comment->comment_post_ID);
            $post_type = get_post_type($comment->comment_post_ID);
            if($post_type=='pg_groupwalls' || $post_type=='profilegrid_blogs' || $post_type=='attachment')
            {
                if($rid!=$comment->user_id):
                    $timestamp = current_time('mysql',true);
                    $title = get_the_title($comment->comment_post_ID);
                    $meta = array();
                    $meta['comment_id']=$comment->comment_ID;
                    $meta['posttype']=$post_type.'_'.$comment->comment_ID;
                    $meta= maybe_serialize($meta);
                    $data = array('type'=>'comment','sid'=>$comment->user_id,'rid'=>$rid,'timestamp'=>$timestamp,'description'=>$title,'status'=>1,'meta'=>$meta);
                    $arg = array('%s','%s','%s','%s','%s','%s','%s');
                    $gid = $dbhandler->insert_row('NOTIFICATION', $data,$arg);
                endif;
            }
        }
    
        public function pm_blog_post_published($meta_id, $post_id, $meta_key='', $meta_value='' ){
            $post = get_post($post_id);
            $author = $post->post_author; /* Post author ID. */
            $title = $post->post_title;
            $ID = $post_id;
            if($post->post_type=='profilegrid_blogs' && ($meta_key=='pm_content_access' || $meta_key=='pm_content_access_group') ):
                $dbhandler = new PM_DBhandler;
                $pmrequests = new PM_request;
                $pmfriends = new PM_Friends_Functions;
                $timestamp =current_time('mysql',true);
                $meta = array();
                $meta['post_id']=$ID;
                $meta= maybe_serialize($meta);
                $meta_query_array = array();
                $meta_query_array['relation'] = 'AND';
                switch(get_post_meta($ID,'pm_content_access',true))
                {
                    case 1:
                        $meta_query_array[] =array('key'=> 'pm_group'); 
                        $users =  $dbhandler->pm_get_all_users('',$meta_query_array);
                        break;
                    case 2:
                        if(get_post_meta($ID,'pm_content_access_group',true)!='all')
                        {
                            $gid = get_post_meta($ID,'pm_content_access_group',true);
                            $meta_query_array[] =array('key'=> 'pm_group','value'=> $gid,'compare' => '='); 
                            $users =  $dbhandler->pm_get_all_users('',$meta_query_array);   
                        }
                        else 
                        {
                            $meta_query_array[] =array('key'=> 'pm_group'); 
                            $users =  $dbhandler->pm_get_all_users('',$meta_query_array);
                        }
                        break;
                    case 3:
                            $myfriends = $pmfriends->profile_magic_my_friends($author);
                            $users =  $dbhandler->pm_get_all_users('',$meta_query_array,'','','','ASC','ID',array(),array(),$myfriends);
                        break;
                    case 4:
                            $users  = array();
                        break;
                    default:
                            $users  = array();
                        break;
                    
                }
                if(!empty($users))
                {
                    foreach($users as $user)
                    {  
                        if($user->ID!=$author)
                        {
                            $data = array('type'=>'BlogPost','sid'=>$author,'rid'=>$user->ID,'timestamp'=>$timestamp,'description'=>$title,'status'=>1,'meta'=>$meta);
                            $arg = array('%s','%s','%s','%s','%s','%s','%s');
                            $gid = $dbhandler->insert_row('NOTIFICATION', $data,$arg);
                        }
                    }
                }
            endif;
        }
 
        public function pm_friend_request_notification($rid,$sid){
            $dbhandler = new PM_DBhandler;
            //$rid = get_post_field( 'post_author',$comment->comment_post_ID);
            $timestamp = current_time('mysql',true);
            $meta = array();
            //add something to meta if you want
            $meta= maybe_serialize($meta);
            $data = array('type'=>'FriendRequest','sid'=>$sid,'rid'=>$rid,'timestamp'=>$timestamp,'description'=>'','status'=>1,'meta'=>$meta);
            $arg = array('%s','%s','%s','%s','%s','%s','%s');
            $gid = $dbhandler->insert_row('NOTIFICATION', $data,$arg);
       
        }
        
        public function pm_friend_added_notification($rid,$sid){
            $dbhandler = new PM_DBhandler;
            //$rid = get_post_field( 'post_author',$comment->comment_post_ID);
            $timestamp = current_time('mysql',true);
            $meta = array();
            //add something to meta if you want
            $meta= maybe_serialize($meta);
            $data = array('type'=>'FriendAdded','sid'=>$sid,'rid'=>$rid,'timestamp'=>$timestamp,'description'=>'','status'=>1,'meta'=>$meta);
            $arg = array('%s','%s','%s','%s','%s','%s','%s');
            $gid = $dbhandler->insert_row('NOTIFICATION', $data,$arg);
        }
        
    
/*    ----------NOTIFICATION DISPLAY FUNCTIONS-------------*/    
    
    
    
        public function pm_generate_blog_post_notice($db_notification,$id){
            $pmrequests = new PM_request;
            $current_uid = get_current_user_id();
            $notif=$db_notification;
            $notif_timestamp = human_time_diff(strtotime($notif->timestamp), current_time('timestamp',true));
            
            $meta = maybe_unserialize( $notif->meta);
            $post_id = $meta['post_id'];
            $permalink = get_permalink($post_id);
//            $receivers = maybe_unserialize( $notif->receivers);
//            $current_user_group = $pmrequests->profile_magic_get_user_field_value($current_uid,'pm_group');            
            $return ='';
           // if((in_array($current_user_group, $receivers['group']) || in_array($current_uid, $receivers['uid']) ) && !in_array($current_uid, $receivers['exclude']))
            
                
            $notif_sender_id = $notif->sid;
            $profile_url = $pmrequests->pm_get_user_profile_url($notif_sender_id);
            $sender_profile_url = $profile_url; 
            $status = $notif->status;
            if($status == 4)
            {
                $bold = '<b>';
                $bold_close = '</b>';
            }else{
                $bold = '';
                $bold_close = '';
            }
            $sender_avatar = get_avatar($notif_sender_id, 50, '', false, array('class' => 'pm-user-profile'));
            $sender_name =$pmrequests->pm_get_display_name($notif_sender_id);
            $description = $notif->description;

           $return ='  
            <div id="notif_'.$id.'" class="pm-notification  pm-group-blog-post-notice ">
            <div class="pm-notification-date">'.$notif_timestamp.' ago</div>
            <div class="pm-notification-card pm-dbfl">
             <div onClick="pm_delete_notification('.$id.')" class="pm-notification-close"><svg fill="#000000" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
       <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
       <path d="M0 0h24v24H0z" fill="none"/>
    </svg></div>
                <div class="pm-notification-title pm-pad10 ">'.$bold.'New Group Blog Post'.$bold_close.'</div>
                <div class="pm-notification-description-wrap pm-dbfl pm-pad10  ">
                    <div class="pm-notification-profile-image pm-difl">'.$sender_avatar.'</div>
                    <div class="pm-notification-description pm-difl">
                        <div class="pm-notification-user pm-color"><a href="'.$sender_profile_url.'">'.$sender_name.'</a></div>
                        <div class="pm-notification-user-activity">'.$description.'</div>
                    </div>
                </div>
                <div class="pm-notification-footer pm-dbfl"><div class="pm-notification-buttons"><a href="'.$permalink.'">View</a></div></div>
            </div>
        </div>';

  //          $this->pm_change_notification_status($id,2);
            return $return;
    
        }
        
        public function pm_generate_comment_notice($db_notification,$id){
            $pmrequests = new PM_request;
            $current_uid = get_current_user_id();
            $notif=$db_notification;
            $notif_timestamp = human_time_diff(strtotime($notif->timestamp), current_time('timestamp',true));
            $notif_sender_id = $notif->sid;
            $status = $notif->status;
            $profile_url = $pmrequests->pm_get_user_profile_url($notif_sender_id);
            $sender_profile_url = $profile_url;
          
            if($status == 4)
            {
                $bold = '<b>';
                $bold_close = '</b>';
            }else{
                $bold = '';
                $bold_close = '';
            }
            $sender_avatar = get_avatar($notif_sender_id, 50, '', false, array('class' => 'pm-user-profile'));
            $sender_name =$pmrequests->pm_get_display_name($notif_sender_id);
            $description = $notif->description;
            $meta = maybe_unserialize( $notif->meta);
            $comment_id = $meta['comment_id'];
            $comment_content = get_comment($comment_id);
            if(!empty($comment_content))
            {
                $link = get_comment_link( $comment_id);
                $permalink = '<a href="'.$link.'">View</a>';
            }
            else
            {
                $permalink = 'Deleted';
            }
            $return = '';
            $title = $this->pm_comment_notification_title($db_notification);
         $return='<div id="notif_'.$id.'" class="pm-notification pm-new-post-comment-notice ">
                 <div class="pm-notification-date">'.$notif_timestamp.' ago</div>
                <div class="pm-notification-card pm-dbfl">
                <div onClick="pm_delete_notification('.$id.')" class="pm-notification-close"><svg fill="#000000" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
       <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
       <path d="M0 0h24v24H0z" fill="none"/>
    </svg></div>   <div class="pm-notification-title pm-pad10 ">'.$bold.$title.$bold_close.'</div>
                    <div class="pm-notification-description-wrap pm-dbfl pm-pad10  ">
                        <div class="pm-notification-profile-image pm-difl">'.$sender_avatar.'</div>
                        <div class="pm-notification-description pm-difl">
                            <div class="pm-notification-user pm-color"><a href="'.$sender_profile_url.'">'.$sender_name.'</a></div>
                            <div class="pm-notification-user-activity">'.$description.'</div>
                        </div>
                    </div>
                    <div class="pm-notification-footer pm-dbfl"><div class="pm-notification-buttons">'.$permalink.'</div></div>
                </div>
            </div>';
//                     $this->pm_change_notification_status($id,2);
        return $return;
        }
        
        public function pm_comment_notification_title($notification)
        {
            $meta = maybe_unserialize($notification->meta);
            
            $comment_id = $meta['comment_id'];
            if(isset($meta['posttype']))
            {
                $posttype = str_replace('_'.$comment_id,'',$meta['posttype']);
            }
            else
            {
                $posttype = '';
            }
            switch($posttype)
            {
                case 'pg_groupwalls':
                    $title = 'New Comment on Wall';
                    break;
                case 'profilegrid_blogs':
                    $title = 'New Comment on Blog';
                    break;
                case 'attachment':
                    $title = 'New Comment on Photo';
                    break;
                default:
                    $title = 'New Comment';
                    break;
            }
            
            return  apply_filters( 'pm_comment_notification_title',$title,$notification);
        }

        public function pm_generate_friend_request_notice($db_notification,$id){
           
            $pmrequests = new PM_request;
            $pmfriends = new PM_Friends_Functions;
            $current_uid = get_current_user_id();
            $notif=$db_notification;
            $notif_timestamp = human_time_diff(strtotime($notif->timestamp), current_time('timestamp',true));
            $notif_sender_id = $notif->sid;
            $profile_url = $pmrequests->pm_get_user_profile_url($notif_sender_id);
            $sender_profile_url = $profile_url; 
            $status = $notif->status;
            if($status == 4)
            {
                $bold = '<b>';
                $bold_close = '</b>';
            }else{
                $bold = '';
                $bold_close = '';
            }
            $sender_avatar = get_avatar($notif_sender_id, 50, '', false, array('class' => 'pm-user-profile'));
            $sender_name =$pmrequests->pm_get_display_name($notif_sender_id);
            $no_of_friends = $pmfriends->pm_count_my_friends($notif_sender_id);
            $description = $no_of_friends." Friends";
            $u2 = $pmrequests->pm_encrypt_decrypt_pass('encrypt',$notif_sender_id);
            $u1 = $pmrequests->pm_encrypt_decrypt_pass('encrypt',$current_uid);
            $return="";
            $return = '   <div id="notif_'.$id.'" class="pm-notification pm-friend-request-notice ">
                          <div class="pm-notification-date">'.$notif_timestamp.' ago</div>
                                    <div class="pm-notification-card pm-dbfl">
                             <div onClick="pm_delete_notification('.$id.')" class="pm-notification-close"><svg fill="#000000" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
       <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
       <path d="M0 0h24v24H0z" fill="none"/>
    </svg></div>     <div class="pm-notification-title pm-pad10 ">'.$bold.'New Friend Request'.$bold_close.'</div>
                                    <div class="pm-notification-description-wrap pm-dbfl pm-pad10  ">
                                        <div class="pm-notification-profile-image pm-difl">'.$sender_avatar.'</div>
                                        <div class="pm-notification-description pm-difl">
                                            <div class="pm-notification-user pm-color"><a href="'.$sender_profile_url.'">'.$sender_name.'</a></div>
                                            <div class="pm-notification-user-activity">'.$description.'</div>
                                        </div>
                                    </div>
                                    <div class="pm-notification-footer pm-dbfl"><div class="pm-notification-buttons"><a  onClick="pm_confirm_request_from_notification(\''.$u1.'\',\''.$u2.'\',this,'.$id.')">Accept</a><a onClick="pm_reject_friend_request_from_notification(\''.$u1.'\',\''.$u2.'\',this,'.$id.')">Delete</a></div></div>
                                </div>
                            </div>';
            return $return;
    }
          
        public function pm_generate_friend_added_notice($db_notification,$id){
            $pmrequests = new PM_request;
            $pmmessenger = new PM_Messenger;
            $pmfriends = new PM_Friends_Functions;
            $current_uid = get_current_user_id();
            $notif=$db_notification;
            $notif_timestamp = human_time_diff(strtotime($notif->timestamp), current_time('timestamp',true));
            $notif_sender_id = $notif->sid;
            $status = $notif->status;
            $profile_url = $pmrequests->pm_get_user_profile_url($notif_sender_id);
            $sender_profile_url = $profile_url; 

            if($status == 4)
            {
                $bold = '<b>';
                $bold_close = '</b>';
            }else{
                $bold = '';
                $bold_close = '';
            }
            $sender_avatar = get_avatar($notif_sender_id, 50, '', false, array('class' => 'pm-user-profile'));
            $sender_name =$pmrequests->pm_get_display_name($notif_sender_id);
            $no_of_friends = $pmfriends->pm_count_my_friends($notif_sender_id);
            $description = $no_of_friends." Friends";
            $send_msg_link = $pmmessenger->pm_get_message_url($notif_sender_id);
            $return="";
            $return ='   <div id="notif_'.$id.'" class="pm-notification pm-new-friend-added-notice ">
                        <div class="pm-notification-date">'.$notif_timestamp.' ago</div>
                        <div class="pm-notification-card pm-dbfl">
                   <div onClick="pm_delete_notification('.$id.')" class="pm-notification-close"><svg fill="#000000" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
       <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
       <path d="M0 0h24v24H0z" fill="none"/>
    </svg></div>        <div class="pm-notification-title pm-pad10 ">'.$bold.'New Friend Added'.$bold_close.'</div>
                            <div class="pm-notification-description-wrap pm-dbfl pm-pad10  ">
                                <div class="pm-notification-profile-image pm-difl">'.$sender_avatar.'</div>
                                <div class="pm-notification-description pm-difl">
                                    <div class="pm-notification-user pm-color"><a href="'.$sender_profile_url.'">'.$sender_name.'</a></div>
                                    <div class="pm-notification-user-activity">'.$description.'</div>
                                </div>
                            </div>
                            <div class="pm-notification-footer pm-dbfl"><div class="pm-notification-buttons"><a href="'.$send_msg_link.'">Message</a></div></div>
                        </div>
                    </div>';
            return $return;
       }
       

       
/*----------------EXTRA NOTIFICATION FUNCTIONS------------------*/
     
       
        
        public function pm_change_notification_status($notif_id,$status=2){
            $dbhandler = new PM_DBhandler;
            $current_uid = get_current_user_id();
            $updated=$dbhandler->update_row('NOTIFICATION', 'id',$notif_id,array('status'=>$status));
            }
        
        public function pm_get_all_users_with_gid($gid){
        $pmrequests =new PM_request;
        $dbhandler = new PM_DBhandler;
        $meta_query_array = $pmrequests->pm_get_user_meta_query(array('gid'=>$gid));
	$users =  $dbhandler->pm_get_all_users('',$meta_query_array,'',0,'','DESC','ID');
        
        return $users;
        }
      
        public function pm_delete_notification($id){
        $dbhandler = new PM_DBhandler;
        $identifier = 'NOTIFICATION';
        $return= $dbhandler->remove_row($identifier,'id',$id);

        return $return;
        }
        
        public function pm_get_user_unread_notification_count($uid) {
            if ($uid) {
                $dbhandler = new PM_DBhandler;
                $identifier = 'NOTIFICATION';
                $where = 1;
                $additional = ' rid = '.$uid.' AND status  in (1,4)';
                $unread = $dbhandler->get_all_result($identifier, $column = '*', $where, 'results', 0, false, $sort_by = 'timestamp', true,$additional);
                if(!empty($unread)){
                $unread_notif =count($unread);
                }else{
                $unread_notif =0;    
                }
                return $unread_notif;
            }
    }
    
    public function pm_mark_all_notification_as_read($uid){
          if ($uid)
            {
                $dbhandler = new PM_DBhandler;
                $identifier = 'NOTIFICATION';
                $where = 1;
                $additional = ' rid = '.$uid.' AND status = 4';
                $unread = $dbhandler->get_all_result($identifier, $column = '*', $where, 'results', 0, false, $sort_by = 'timestamp', true,$additional);
             if(!empty($unread)){
                foreach($unread as $notification)
                {
                    $updated=$dbhandler->update_row('NOTIFICATION', 'id',$notification->id,array('status'=>'2'));
                }
             }
            }
    }

       
       
       
    }
