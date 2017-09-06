<?php
class PM_HTML_Creator
{
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
	public function __construct( $profile_magic, $version ) {

		$this->profile_magic = $profile_magic;
		$this->version = $version;

	}

	public function get_custom_fields_html($gid,$fields,$col=1)
	{
                $dbhandler = new PM_DBhandler;
                $pm_customfields = new PM_Custom_Fields;
		$col = $dbhandler->get_global_option_value('pm_reg_form_cols',1);
		if($lastRec=count($fields))
		{
			echo '<div class="pmrow">';
			$i=0;
			foreach($fields as $field) 
			{
				if( $i!=0 && ($i % $col == 0) && ($i<$lastRec) ) echo '</div><div class="pmrow">';
				$pm_customfields->pm_get_custom_form_fields($field,'','profile-grid');
				$i++;
			}
			echo '</div>';
		}
	}
	
	public function get_custom_fields_html_multipage($gid,$fields,$col=1)
	{
                $dbhandler = new PM_DBhandler;
                $pm_customfields = new PM_Custom_Fields;
		$sections =  $dbhandler->get_all_result('SECTION','id,section_name',array('gid'=>$gid),'results',0,false,'ordering');
		 $j=0;
		 foreach($sections as $section)
		 {
		 	 $fields =  $dbhandler->get_all_result('FIELDS', $column = '*',array('associate_group'=>$gid,'associate_section'=>$section->id,'show_in_signup_form'=>1),'results',0,false, $sort_by = 'ordering');
			 
		 	 if(isset($fields) && !empty($fields))
			 {
				 echo '<fieldset id="fieldset_'.$section->id.'">';
				 echo '<legend>'.$section->section_name.'</legend>';
				 foreach($fields as $field)
				 {
					echo '<div class="pmrow">';
					$pm_customfields->pm_get_custom_form_fields($field,'','profile-grid');
					echo '</div>';	 
				 }
				 echo '<div class="all_errors" style="display:none;"></div></fieldset>';
				 
			 }
			 
        }
	}
	
	public function get_custom_fields_html_singlepage($gid,$fields,$col=1)
	{
                $dbhandler = new PM_DBhandler;
                $pm_customfields = new PM_Custom_Fields;
                $filled_section = $dbhandler->get_all_result('FIELDS','distinct(associate_section)',array('associate_group'=>$gid,'show_in_signup_form'=>1));
                foreach($filled_section as $sectionid)
                {
                    $sectionidarray[] =  $sectionid->associate_section;
                }
                
                $additional = "and id in(".implode(',',$sectionidarray).")";
		$sections =  $dbhandler->get_all_result('SECTION','id,section_name',array('gid'=>$gid),'results',0,false,'ordering',false,$additional);
		$count = count($sections);
		$j=1;
                foreach($sections as $section)
                {
                        $fields =  $dbhandler->get_all_result('FIELDS', $column = '*',array('associate_group'=>$gid,'associate_section'=>$section->id,'show_in_signup_form'=>1),'results',0,false, $sort_by = 'ordering');
                        
                        if(isset($fields) && !empty($fields))
                        {
                                echo '<fieldset id="fieldset_'.$section->id.'">';
                                echo '<legend>'.$section->section_name.'</legend>';
                                foreach($fields as $field)
                                {
                                       echo '<div class="pmrow">';
                                       $pm_customfields->pm_get_custom_form_fields($field,'','profile-grid');
                                       echo '</div>';	 
                                }
                                if($count == $j)
                                {
                                       do_action('profile_magic_custom_fields_html',$gid);
                                       do_action('profile_magic_show_captcha',$gid);
                                }
                                echo '<div class="all_errors" style="display:none;"></div></fieldset>';

                        }
                        
                        $j++;
                }
	}
	
	public function get_custom_login_form_html($fields,$col=1)
	{
                $pm_customfields = new PM_Custom_Fields;
		if($lastRec=count($fields))
		{
			echo '<div class="pmrow">';
			$i=0;
			foreach($fields as $field) 
			{
				if( $i!=0 && ($i % $col == 0) && ($i<$lastRec) ) echo '</div><div class="pmrow">';
				$pm_customfields->pm_get_custom_login_fields($field,$this->profile_magic);
				$i++;
			}
			echo '</div>';
		}
		
			
	}
	
	public function get_group_page_fields_html($uid,$gid,$group_leader,$imgsize='',$arg='')
	{
                $dbhandler = new PM_DBhandler;
                $pmrequests = new PM_request;
		$profile_url = get_permalink($dbhandler->get_global_option_value('pm_user_profile_page'));
		$slug = $pmrequests->pm_get_profile_slug_by_id($uid);
                $profile_url = add_query_arg( 'uid',$slug,$profile_url );
		if($uid == $group_leader)$class = 'pm-group-leader-large';else $class='';
		?>
        
        <div class="pm-user-card pm-difl pm-border pm-radius5">
            <div class="pm-user-card-cover pm-dbfl">
                <?php 
          echo $pmrequests->profile_magic_get_cover_image($uid,$imgsize,'',false,$arg);?></div>
          <div class="pm-user-image pm-dbfl pm-bg-lt <?php echo $class;?>"><?php echo get_avatar($uid,$imgsize,'',false,$arg);?></div>
            <div class="pm-user-description pm-dbfl pm-bg-lt">
                <div class="pm-user-card-title pm-dbfl pm-pad10 pm-bg-lt pm-border-bt pm-clip">
                <a href="<?php echo $profile_url ;?>"><?php echo $pmrequests->pm_get_display_name($uid);?> </a>
                </div>
                  <?php 
				  $exclude = "'user_name','user_avatar','user_pass','confirm_pass','paragraph','heading'";
				  $fields = $pmrequests->pm_get_frontend_user_meta($uid,$gid,$group_leader,'group','',$exclude);
                            $this->get_user_meta_fields_html($fields,$uid);
               ?> 
            </div>
          </div>         
      	<?php	
	}
	
	public function get_user_meta_fields_html($fields,$uid)
	{
                $pmrequests = new PM_request;
		if(isset($fields)): echo '<div class="pm-section-wrapper">';
			foreach($fields as $field):?>
			<?php $field_value =  $pmrequests->profile_magic_get_user_field_value($uid,$field->field_key,$field->field_type);
			$value = '';
                        if(!empty($field_value))
                        {
                            $field_value = maybe_unserialize($field_value);
                            if($field->field_type=='checkbox' || $field->field_type=='repeatable_text')
                            {
                               if(!is_array($field_value))$field_value = explode(',',$field_value);  
                            }
                            
                            
                            
                            if(is_array($field_value)):
                                if($field->field_type=='address')
                                {
                                    $options = maybe_unserialize($field->field_options);
                                    foreach($field_value as $key=>$fv)
                                    {
                                        if(!isset($options[$key]))
                                        {
                                            unset($field_value[$key]);
                                        }
                                    }

                                } 
                                
                                foreach($field_value as $val)
                                {
                                        if($val=='chl_other')continue;
                                        if($val!='')
                                        $value.= '<div class="pm-field-multiple-value pm-difl pm-radius5">'.$val.'</div>';	
                                }
                            else:
                                    $value = $field_value; 
                            endif;?>
			
		
			<div class="pm-user-description-row pm-dbfl pm-border">
                            <div class="pm-card-label pm-difl">
                            <?php if(isset($field) && $field->field_icon!=0):
                                    echo wp_get_attachment_image($field->field_icon,array(16,16),true,false);
                            endif; ?>
                            <?php echo $field->field_name; ?>
                            </div>
                            <?php 
                            switch($field->field_type)
                            {
                                case 'user_url':
                                case 'facebook':
                                case 'google':
                                case 'twitter':
                                case 'linked_in':
                                case 'youtube':
                                case 'instagram':
                                    ?> 
                                    <div class="pm-card-value pm-difl <?php echo 'pm_collapsable_'.$field->field_type; ?>"><a href="<?php echo $value;?>" target="_blank"><?php echo $value;?></a></div>
                                    <?php
                                    break;
                                default:
                                    ?>
                                    <div class="pm-card-value pm-difl <?php echo 'pm_collapsable_'.$field->field_type; ?>"><?php echo $value;?></div>
                                    <?php
                                    break;
                                    
                            }
                            ?>
                        </div>
			<?php
                        }
			endforeach;
                        
                        echo '</div>';
		endif;
	}
	
	public function pm_get_captcha_html()
	{
                $dbhandler = new PM_DBhandler;
		$publickey = $dbhandler->get_global_option_value('pm_recaptcha_site_key')
		?>
        <div class="pmrow">
        <div class="pm-col">
			<div class="pmfield"> </div>
			  <div class="pminput pm_recaptcha">
				<div class="g-recaptcha" data-sitekey="<?php echo $publickey; ?>"></div>
				<div class="errortext" style="display:none;"></div>
			  </div>
			</div>
            </div>
        <?php
	}
	
	public function pm_field_captcha_error($errors)
	{
		?>
         <!--HTML for showing error when recaptcha does not matches-->
		<div class="errortext pm_captcha_error">
    	     <?php  _e( 'Sorry, you didn\'t enter the correct captcha code.','profile-grid' ); ?>
         </div>
		<?php	
	}
	
       public function pm_get_user_blog_posts($uid,$pagenum=1,$limit=10)
        {
            $dbhandler = new PM_DBhandler;
            $pmrequests = new PM_request;
            $displayname = $pmrequests->pm_get_display_name($uid);
            $offset = ( $pagenum - 1 ) * $limit;
            $args = array(
            'orderby'          => 'date',
            'order'            => 'DESC',
            'post_type'        => 'profilegrid_blogs',
            'author'	   => $uid,
            'post_status'      => 'publish',
            'posts_per_page' => -1
            );
            $total_posts = count(get_posts( $args ));
            //echo $total_posts;
            $args['posts_per_page'] = $limit;
            $args['offset']= $offset;
            $posts_array = get_posts( $args );
          // echo count($posts_array);
            $num_of_pages = ceil( $total_posts/$limit);
            //echo $num_of_pages;
            $pagination = $dbhandler->pm_get_pagination($num_of_pages,$pagenum); 
            if($pagenum<=$num_of_pages)
            {
                           
                $path =  plugins_url( '../public/partials/images/default-featured.jpg', __FILE__ );

                $query = new WP_Query( $args );

                while ( $query->have_posts() ) : $query->the_post();
                $comments_count = wp_count_comments();

                    ?>
                    <div class="pm-blog-post-wrap pm-dbfl">
                        <div class="pm-blog-img-wrap pm-difl">
                            <div class="pm-blog-img pm-difl">
                                <?php if ( has_post_thumbnail() ) {
                                the_post_thumbnail('post-thumbnail');
                                } else { ?>
                                <img src="<?php echo $path;?>" alt="<?php the_title(); ?>" class="pm-user" />
                                <?php } ?>
                            </div>
                            <div class="pm-blog-status pm-difl">
                                <span class="pm-blog-time "><?php printf( _x( '%s ago', '%s = human-readable time difference', 'profile-grid' ), human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) ); ?></span>
                                <span class="pm-blog-comment"><?php comments_number( 'no Comment', '1 Comment', '% Comments' );?></span>
                            </div>
                        </div>

                        <div class="pm-blog-desc-wrap pm-difl">
                            <div class="pm-blog-title">
                                <a href="<?php the_permalink(); ?>"><span><?php the_title();?></span></a>
                            </div>
                            <div class="pm-blog-desc">
                             <?php the_excerpt();?>
                            </div>
                        </div>
                    </div>
                    <?php
                endwhile;
                 if($pagenum<$num_of_pages):
                ?>
                    <div class="pg-load-more-container pm-dbfl">
                        <div class="pm-loader" style="display:none;"></div>
                        <input type="hidden" id="pg_next_blog_page" value="<?php echo $pagenum + 1; ?>" />
                        <input type="submit" class="pm-load-more-blogs" onclick ="load_more_pg_blogs('<?php echo $uid;?>')" value="<?php _e('Load More','profile-grid'); ?>" />
                    </div>
                <?php
                endif;
                
            }
            else
            {
                
                $current_user = wp_get_current_user();
                if($uid == $current_user->ID)
                {
                    echo "<div class='pg-blog-notice'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> ";
                     _e("You have not written any blog posts yet. Once you do, they will appear here.",'profile-grid');
                    echo "</div>";
                }
                else
                {
                    echo "<div class='pg-blog-notice'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> ";
                     _e("Sorry, $displayname has not made any blog posts yet.",'profile-grid');
                    echo "</div>";
                }
               
            }
        }
        
        public function pm_get_user_messenger($receiver_uid){
            $dbhandler = new PM_DBhandler;
            $pmrequests = new PM_request;
            $textdomain = $this->profile_magic;
            $permalink = get_permalink();
            $current_user = wp_get_current_user();
            $pmmessenger = new PM_Messenger();
            $return=$pmmessenger->pm_messenger_show_threads('');
            $message_display="";
            if($receiver_uid!=''){
            $receiver_user = $pmmessenger->pm_messenger_show_thread_user($receiver_uid);
            $tid = $pmrequests->get_thread_id($receiver_uid, $current_user->ID);
            if($tid!=false){
            $message_display = $pmmessenger->pm_messenger_show_messages($tid, '', $loadnum=1,0);
            $return=$pmmessenger->pm_messenger_show_threads($tid);
            }
            }
            ?>
         
          <div class="pm-group-view pm-border-bt">
        <div class="pm-section pm-dbfl" > 
            <div class="pm-section-nav-vertical pm-difl " id="thread_pane">
                <div class="dbfl pm-new-message-area"><button title="Click here to compose the message" id="new_message_btn" onclick="create_new_message()"><i class="fa fa-plus" aria-hidden="true"></i> New Message</button></div>
                <ul class="dbfl" id="threads_ul">
                    <?php echo $return; ?>
                </ul>
            </div>
            <div class="pm-blog-desc-wrap pm-difl pm-section-content pm-message-thread-section">
                <div id="pm-msg-overlay" class="pm-msg-overlay  <?php if(($return=="You have no conversations yet.")&& !isset($receiver_user)) echo "pm-overlay-show" ?>"> </div>
                <form id="chat_message_form" onsubmit="pm_messenger_send_chat_message(event);">  
                <div  class="pm-user-display-area pm-dbfl ">
                    <div class="pm-user-send-to pm-difl">To</div>
                    <div class="pm-user-send-box pm-difl">   
                    <input type="text" id="receipent_field"  value="<?php if(isset($receiver_user)) echo "@".$receiver_user['name']; ?>" placeholder="@Username" style="min-width: 100%;" onblur="pm_get_rid_by_uname(this.value)"/>
                    <input type="hidden" id="receipent_field_rid" name="rid" value="<?php if(isset($receiver_user)) echo $receiver_user['uid']; ?>"  />   
                    </div></div>
                
                <div id="pm-autocomplete"></div>
                <div id="pm-username-error" class="pm-dbfl"></div>
                <div id="message_display_area" class="pm-section-content pm-difl pm_full_width_profile"  style="min-height:200px;max-height:200px;max-width: 550px;overflow-y:auto;">
                    <?php echo $message_display;?>
                <?php   $path =  plugins_url( '../public/partials/images/typing_image.gif', __FILE__ );?>
               
                </div>
                    
                <div id="typing_on"  class="pm-user-description-row pm-dbfl pm-border"><div class="pm-typing-inner"><img height="9px" width="40px" src="<?php echo $path; ?>"/></div></div>
             
                <div class="pm-dbfl pm-chat-messenger-box">
                      <input type="hidden" name="action" value='pm_messenger_send_new_message' /> 
                    <input type="hidden" id="thread_hidden_field" name="tid" value=""/>
                    <div class="emoji-container">
                        <div class="pm-messenger-user-profile-pic"><?php $avatar =get_avatar($current_user->ID, 50, '', false, array('class' => 'pm-user-profile'));
                            echo $avatar;
                        ?></div>
                    <textarea id="messenger_textarea" data-emojiable="true"  name="content" style="min-width: 100%;height:100px;"
                        
                               form="chat_message_form" placeholder="Type your message.." ></textarea> 
                    <input type="hidden" disabled  maxlength="4" size="4" value="1000" id="counter">
                    <input type="hidden" name="sid" value="" />   
                    <div class="pm-messenger-button">
                        <label>
                          <input id="send_msg_btn" type="submit" name="send" value="send"/>
                    <svg width="100%" height="100%" viewBox="0 0 512 512" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" style="fill:#ccc">
    <g transform="matrix(1.05995e-15,17.3103,-17.3103,1.05995e-15,22248.8,-22939.9)">
        <path d="M1340,1256C1340,1256 1350.4,1279.2 1352.6,1284.1C1352.68,1284.28 1352.65,1284.49 1352.53,1284.65C1352.41,1284.81 1352.22,1284.89 1352.02,1284.86C1349.73,1284.54 1344.07,1283.75 1342.5,1283.53C1342.26,1283.5 1342.07,1283.3 1342.04,1283.06C1341.71,1280.61 1340,1268 1340,1268C1340,1268 1338.33,1280.61 1338.01,1283.06C1337.98,1283.31 1337.79,1283.5 1337.54,1283.53C1335.97,1283.75 1330.28,1284.54 1327.98,1284.86C1327.78,1284.89 1327.58,1284.81 1327.46,1284.65C1327.35,1284.49 1327.32,1284.28 1327.4,1284.1C1329.6,1279.2 1340,1256 1340,1256Z"/>
    </g>
    </svg>
                        </label>      
                    </div>
                </div>
                    </div>
            </form>
                
               

        </div>
        </div> </div> 
                
            <?php
        }
        
        public function pm_get_notification_html($uid)
        {?>
         
         <!-----PM Notification----->
         
         <div id="pm_notification_view_area" class="pm-notification-view-area"> 
         
             <?php 
             $pm_notification = new Profile_Magic_Notification;
             $pm_notification->pm_generate_notification_without_heartbeat();
             ?>
         
         </div>
         
         
         
         
         
         
         <?php
        }
        
        public function pm_get_friends_action_bar_html($u1,$view)
        {
            switch ($view) {
                case 1:
                    ?>
                    <div class="pm-friend-action-bar pm-dbfl">
                        <button class="pm-difr pm-delete" onclick="pm_multiple_friends_remove('<?php echo $u1;?>')"><?php _e('Remove','profile-grid');?></button>
                    </div>
                    <?php
                    break;
                case 2:
                    ?>
                    <div class="pm-friend-action-bar pm-dbfl">
                        <button class="pm-difr pm-delete" onclick="pm_multiple_friends_request_delete('<?php echo $u1;?>')"><?php _e('Delete','profile-grid');?></button>
                        <button class="pm-difr " onclick="pm_multiple_friends_request_accept('<?php echo $u1;?>')"><?php _e('Accept','profile-grid');?></button>
                    </div>
                    <?php
                    break;
                case 3:
                    ?>
                    <div class="pm-friend-action-bar pm-dbfl">
                        <button class="pm-difr pm-delete" onclick="pm_multiple_friends_request_cancel('<?php echo $u1;?>')"><?php _e('Cancel','profile-grid');?></button>
                    </div>
                    <?php
                    break;

                default:
                    break;
            }
        }
        
        public function pm_get_my_friends_html($uid,$pagenum,$pm_f_search,$limit,$view=1)
        {
            $pmfriends = new PM_Friends_Functions;
            $pmrequests = new PM_request;
            $dbhandler = new PM_DBhandler;  
            $identifier = 'FRIENDS';
            $path =  plugin_dir_url(__FILE__);
            $current_user = wp_get_current_user();
            $pagenum = isset($pagenum) ? absint($pagenum) : 1;
            $offset = ( $pagenum - 1 ) * $limit;
            $meta_query_array = $pmrequests->pm_get_user_meta_query($_GET);
            $date_query = $pmrequests->pm_get_user_date_query($_GET);
            switch($view)
            {
                case 1:
                     $myfriends = $pmfriends->profile_magic_my_friends($uid);
                    if($uid== get_current_user_id())
                    {
                        $error = __('<div class="pg-friend-error-notice">You don\'t have any friends yet. Why don\'t you add one now?</div> ','profile-grid');
                    }
                    else
                    {
                        $display_name = $pmrequests->pm_get_display_name($uid);
                        $error = __($display_name.'  <span class="pg-friend-error-notice pg-no-friends">does not have any friends yet.</span> ','profile-grid');     
                    }
                     break;
                case 2:
                     $myfriends = $pmfriends->profile_magic_my_friends_requests($uid);
                     $error = __('<div class="pg-friend-error-notice">No friend requests waiting for response</div>','profile-grid');
                    break;
                case 3:
                     $myfriends = $pmfriends->profile_magic_my_friends_requests($uid,1);
                    $error = __('<div class="pg-friend-error-notice">No pending friend requests.</div> ','profile-grid');
                    break;
            }
            $u1 = $pmrequests->pm_encrypt_decrypt_pass('encrypt',$uid);
            if(isset($myfriends) && !empty($myfriends))
            {
                    $my_friends_users =  $dbhandler->pm_get_all_users($pm_f_search,$meta_query_array,'',$offset,$limit,'ASC','ID',array(),$date_query,$myfriends);
                    $my_friends_total_users = count($dbhandler->pm_get_all_users($pm_f_search,$meta_query_array,'','','','ASC','ID',array(),$date_query,$myfriends));
                    $num_of_friends_pages = ceil( $my_friends_total_users/$limit);
                    $pagination = $dbhandler->pm_get_pagination($num_of_friends_pages,$pagenum);
            }

            ?>
            <?php if(isset($myfriends) && !empty($myfriends)): 
            if($current_user->ID==$uid){$this->pm_get_friends_action_bar_html($u1,$view);}
            ?>
            
                <div class="pm-my-friends">
                <?php $pmfriends->profile_magic_friends_result_html($my_friends_users,$uid,$view);?>
                </div>
                <?php else : ?>
                <div class="pm-my-friends">
                <?php echo $error; ?>
                </div>
                <?php endif;?>
                <?php if(isset($myfriends) && !empty($myfriends) && $num_of_friends_pages>1):
                echo $pagination;
                endif;
        }
    }
?>
