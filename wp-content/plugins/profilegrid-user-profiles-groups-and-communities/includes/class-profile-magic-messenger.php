<?php
class PM_Messenger {
    public function pm_messenger_show_thread_user($uid) {
        $dbhandler = new PM_DBhandler;
        $pmrequests = new PM_request;
        $user = get_userdata($uid);
        $user_info['name'] = $user->user_login;
        $user_info['uid'] = $uid;
       
        return $user_info;
    
    }
    public function pm_messenger_show_threads($tid) {
        $dbhandler = new PM_DBhandler;
        $pmrequests = new PM_request;
        $current_user = wp_get_current_user();
        $uid = $current_user->ID;
       if(isset($tid)){
        $active_tid = $tid;
       }else{
           $active_tid='';
       }
        $threads = $pmrequests->pm_get_user_all_threads($uid);
        if(!empty($threads))
        $threads = $pmrequests->pm_filter_deleted_threads($threads);
        $unread_thread_count = 0;
        $return = "";
        $count = 1;
        if (!empty($threads)) {
            foreach ($threads as $thread) {
                $active_class="";
                $active = "false";
                if ($uid == $thread->s_id) {
                    $other_uid = $thread->r_id;
                } else {
                    $other_uid = $thread->s_id;
                }
                $tid = $thread->t_id;
                $profile_url = $pmrequests->pm_get_user_profile_url($other_uid);
                $user_info['profile_url'] = $profile_url;
                $other_user_info['avatar'] = get_avatar($other_uid, 50, '', false, array('class' => 'pm-user-profile'));
                $other_user_info['name'] = $pmrequests->pm_get_display_name($other_uid);
                $message = $pmrequests->get_message_of_thread($tid, '1');
                $thread_timestamp = human_time_diff(strtotime($thread->timestamp), current_time('timestamp',true));
                if($thread_timestamp=='1 min'){
                    $thread_timestamp='just now';
                }else{
                    $thread_timestamp = $thread_timestamp.' ago';
                }
                $thread_status = $thread->status;
                if ($thread_status == $uid) {
                    $unread_thread_count++;
                    $unread_message_count = $pmrequests->get_unread_msg_count($tid);
                    $unread_visual = "<b audio=\"on\">$unread_message_count</b>";
                } else {
                    $unread_visual = "";
                }
                if ($active_tid != '') {
                    if ($tid == $active_tid) {
                        $active = "true";
                        $active_class="pm-thread-active";
                    } else {
                        $active_class="";
                        $active = "false";
                    }
                } else {
                    if ($count == 1) {
                        $active = "true";
                        $count++;
                    } else {
                        $active = "false";
                    }
                }
                //  echo '<pre>'.print_r($message).'</pre>';die;
                $login_status = ($pmrequests->pm_get_user_online_status($other_uid)==1 ?'pm-online':'pm-offline');
                
                $return .= "<li $active_tid active=\"$active\" id=\"t_id_$tid\" "
                        . "uid=\"$other_uid\" "
                        . "t_status=\"$thread_status\"  "
                        . "class=\"pm-dbfl pm-border-bt pm-pad10 $active_class\"  "
                        . " >"
                        . "<div onClick=\"show_message_pane($tid)\" class=\"pm-msg-conversation-listitem\"><div class=\"pm-conversations-container\">"
                        . "<div class=\"pm-conversation-box-user pm-difl\"> "
                        . $other_user_info['avatar']
                        . "</div>"
                        ."<div class=\"pm-msg-user pm-difl\">"
                        . "<div class=\"pm-thread-user pm-difl\">"
                        . $other_user_info['name']
                        ."</div>"
                        . "<div class=\"pm-conversation-time\">"
                        . $thread_timestamp
                        ."</div>"
                        . "</div>"
                            
                            
                        . "<div class=\"pm-user-status $login_status pm-difl\"></div>"
                        . "<div id=\"unread_msg_count\" class=\"pm-user-conversations-counter pm-difl\">$unread_visual</div>"
                        
                        . "</div>"
                        . "</div>"
                        . "<div class=\"messagedelete_thread \"><a onClick=\"pm_messenger_delete_thread($tid)\">".'<svg width="100%" height="100%" viewBox="0 0 48 48" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" style="fill: #e21f1f;"><path d="M24,4c-11.05,0 -20,8.95 -20,20c0,11.05 8.95,20 20,20c11.05,0 20,-8.95 20,-20c0,-11.05 -8.95,-20 -20,-20Zm10,22l-20,0l0,-4l20,0l0,4Z" /></svg>'."</a></div>"
                        . "</li>";
//                $return .= "<div id=\"t_id_$tid\" uid=\"$other_uid\" t_status=\" $thread_status\" class=\"pm-user-description-row pm-dbfl pm-border \" onClick=\"show_message_pane(this)\"> ". 
//                $other_user_info['avatar']. "<br/><div class=\"pm-card-label pm-difl\" ><b>".$other_user_info['name']."</b></div>"
//                . "<br><div class=\"pm-card-value pm-difl\">".$last_message."</div>"
//                . "<br><div><b>$thread_timestamp</b><br>&nbspLast Message Status :$last_message_status</div><br><b>Unread Msg Count : $unread_message_count</b><br>Thread Status : $thread_status</div>";
//            
            }
        } else {
            $return = "You have no conversations yet.";
        }
        return $return;
    
    }
    public function pm_messenger_send_new_message($rid,$content) {
        $dbhandler = new PM_DBhandler;
        $pmrequests = new PM_request;
        $current_user = wp_get_current_user();
        
            $rid = $rid;
            $sid = $current_user->ID;
            $content = $content;
            $is_msg_sent = $pmrequests->pm_create_message($sid, $rid, $content);
            $tid=$pmrequests->get_thread_id($sid, $rid);
            $message = $pmrequests->get_message_of_thread($tid,1);
            $message_id=$message[0]->m_id;
              // echo '<pre>'.print_r($message).'</pre>';die;
            if ($is_msg_sent) {
                $last_message = nl2br(wp_kses($content));
                $align = "pm_msg_rf";
                $user_info['avatar'] = get_avatar($sid, 50, '', false, array('class' => 'pm-user-profile'));
                $return .= "<div id=\"$message_id\" class=\"$align  pm-sending-msg\" > " .
                        $user_info['avatar']
                        . "<div class=\"pm-user-description-row pm-dbfl pm-border\">" . $last_message . "</div>Sending</div>";
                
            } else {
                $return =  "not sent";
            }
      return $return;
       
    }
    public function pm_messenger_show_messages($tid,$t_status,$loadnum,$last_mid,$timezone=0) {
        $dbhandler = new PM_DBhandler;
        $pmrequests = new PM_request;
        $current_user = wp_get_current_user();
        $cur_uid = $current_user->ID;
        $t_status = isset($t_status) ? $t_status : 0;
        $loadnum = isset($loadnum) ? absint($loadnum) : 1;
        $last_mid = isset($last_mid)? $last_mid:0;
        $limit = 15; // number of rows in page
        $time_conversion = isset($timezone)?$timezone*60:0;
        $offset = ( $loadnum - 1 ) * $limit;
        $descending = true;
        $messages = $pmrequests->get_message_of_thread($tid, $limit, $offset, $descending);
       if(!empty($messages)){
        $messages = $pmrequests->pm_filter_deleted_message($messages, $tid);
        $messages = array_reverse($messages);
       }
        if ($t_status == $cur_uid) {
            $pmrequests->update_message_status_to_read($tid);
            $pmrequests->pm_update_thread_status($tid, 0);
        }
        $return = "";
        if (!empty($messages)) {
            if (sizeof($messages) == $limit) {
                $return .= "<button   id=\"load_more_message\" pagenum=\"$loadnum\" >Load More</button>";
            }
            foreach ($messages as $message)
            {
                if($message->m_id>=$last_mid)
                {
                    $uid = $message->s_id;
                    $read_status = "";
                    if ($uid == $cur_uid) {
                        $align = "pm_msg_rf";
                        if ($message->status == 1) {
                            $read_status = "read";
                        } else {
                            $read_status = "unread";
                        }
                    } else {
                        $align = "pm_msg_lf";
                    }
                    $last_message = nl2br($message->content);
                    $date = mysql2date('d M,g:i A',date('Y-m-d H:i:s',(strtotime($message->timestamp)) - $time_conversion));
                    $msg_timestamp = human_time_diff(strtotime($message->timestamp), current_time('timestamp'));
                    if($msg_timestamp=='1 min')
                    $msg_timestamp='just now';
                    $other_user_info['avatar'] = get_avatar($uid, 50, '', false, array('class' => 'pm-user-profile'));
                    $return .= "<div id=\"$message->m_id\" class=\"$align\" > " .
                            $other_user_info['avatar']
                            . "<div class=\"pm-user-description-row pm-dbfl pm-border\">" . $last_message . "</div><div class=\"pm-message-thread-time\">$date</div></div>";
                }
                
            }
        }
        return $return;
        
    }
    public function pm_get_messenger_notification($timestamp,$activity,$tid) {
        $dbhandler = new PM_DBhandler;
        $pmrequests = new PM_request;
        $current_user = wp_get_current_user();
        $uid = $current_user->ID;
        set_time_limit(0);
        if($tid!=''&& $activity!=''){
            $pmrequests->update_typing_timestamp($tid, $activity);
        }
        $last_typing_ajax_call = strtotime(current_time('mysql'));
        $last_ajax_call = $timestamp!='' ? (int) ($timestamp) : null;
  
            $flag = 0;
            $threads = $pmrequests->pm_get_user_all_threads($uid);
            if (!empty($threads)) {
                $last_change_time = $threads[0]->timestamp;
                $last_change_in_thread = strtotime($last_change_time);
                
                if($tid!=''){
                $typing_timestamp = $pmrequests->get_typing_timestamp($tid);
                $last_change_in_typing = strtotime($typing_timestamp);
                }
                   
                   

                  if ($last_change_in_thread > $last_ajax_call && ( $last_ajax_call != null||$tid=='') ) {
                   if($tid=='')
                     return json_encode(array());
                      $data = true;
                    $result = array(
                        'activity' => $activity,
                        'data_changed' => $data,
                        'typing_timestamp' => $last_change_in_typing,
                        'timestamp' => $last_change_in_thread
                    );
                     $json = json_encode($result);
                     return $json;
                }

                $data2 = false;
                if($tid!=''){
                $activity = $pmrequests->get_typing_status($tid);
                }else{
                    $activity='nottyping';
                }
                $result = array(
                        'activity' => $activity,
                        'data_changed' => $data2,
                        'typing_timestamp' => $last_change_in_typing,
                        'timestamp' => $last_change_in_thread,
                        'timexxx'=>$timestamp,
                        'last_ajax'=>$last_ajax_call
                    );
                  
                            $json = json_encode($result);
                             return $json;
                     
                
                
            }
          
                $result=array();
                $json = json_encode($result);
                return $json;
            
      
    }
    public function pm_messenger_delete_threads($tid) {
        $dbhandler = new PM_DBhandler;
        $pmrequests = new PM_request;
        $current_user = wp_get_current_user();
        $uid = $current_user->ID;
        $pmrequests->update_message_status_to_read($tid);
        $delete_thread = $pmrequests->delete_thread($tid);
        if ($delete_thread > 0) {
            return "true";
        } else {
            return "false";
        }
      
    }
    public function pm_messenger_notification_extra_data() {
        $dbhandler = new PM_DBhandler;
        $pmrequests = new PM_request;
        $current_user = wp_get_current_user();
        $uid = $current_user->ID;
        $threads = $pmrequests->pm_get_user_all_threads($uid);
        $threads = $pmrequests->pm_filter_deleted_threads($threads);
        $extra_notification_data = array();
        $thread_count = 0;
        if (!empty($threads)) {
            foreach ($threads as $thread) {
                $thread_status = $thread->status;
                if ($thread_status == $uid) {
                    $thread_count++;
                }
            }
        }
        $extra_notification_data['unread_threads'] = $thread_count;
        return json_encode($extra_notification_data);
       
    }
    
    public function pm_get_message_url($uid)
    {
        $messenger_url = '';
        $pmrequests = new PM_request;
	$dbhandler = new PM_DBhandler;
        $current_user = wp_get_current_user();
        //$cur_user_gid = $pmrequests->profile_magic_get_user_field_value($current_user->ID,'pm_group');
        if($uid !=$current_user->ID && $dbhandler->get_global_option_value('pm_enable_private_messaging','1')==1 ):
            if(is_user_logged_in())
            {
                $messenger_url =  $pmrequests->profile_magic_get_frontend_url('pm_user_profile_page','');
                $messenger_url = add_query_arg('#pg-messages','',$messenger_url);    
                $messenger_url = add_query_arg('rid',$uid,$messenger_url);
            }
            else
            {
                $messenger_url = $pmrequests->profile_magic_get_frontend_url('pm_user_login_page',site_url('/wp-login.php'));
                $messenger_url = add_query_arg( 'errors','loginrequired', $messenger_url );
            }
        endif;
            
        return $messenger_url;
    }
}
