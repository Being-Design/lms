 // Sets all user cards equal height
 jQuery(document).ready(function(){
    jQuery('.pmagic').each(function(){  
        var highestBox = 0;
        jQuery(this).find('.pm-user-card').each(function(){
            if(jQuery(this).height() > highestBox){  
                highestBox = jQuery(this).height();  
            }
        })
        jQuery(this).find('.pm-user-card.pm50, .pm-user-card.pm33').height(highestBox);
    });
     jQuery(".pmagic").prepend("<a><a/>");
     var pmDomColor = jQuery(".pmagic").children("a").css('color');
      jQuery(".pm-color").css('color', pmDomColor);
      jQuery( ".pmagic .page-numbers .page-numbers.current" ).addClass( "pm-bg" ).css('background', pmDomColor); 
 });
 
jQuery(document).ready(function(){
    jQuery('#change-pic').on('click', function(e) {
        jQuery('#changePic').show();
        jQuery('#change-pic').hide();
    });

    jQuery('#photoimg').on('change', function() { 
        jQuery("#preview-avatar-profile").html('');
        jQuery("#preview-avatar-profile").html('<div><div class="pm-loader"></div></div>');
             var pmDomColor = jQuery(".pmagic").children("a").css('color');
     jQuery(".pm-loader").css('border-top-color', pmDomColor);
        jQuery('#avatar-edit-img').hide();
        jQuery("#cropimage").ajaxForm({
        target: '#preview-avatar-profile',
        success:    function() {
                        jQuery("input[name='remove_image']").hide();
                        jQuery(".modal-footer").show();
                        tw = jQuery('#truewidth').val();
                        th = jQuery('#trueheight').val();
                        x = 25/100*tw;
                        y = 25/100*th;
                        if(x+150>tw || y+150>th)
                        {
                            x = 0;
                            y = 0;
                        }
                        jQuery('.jcrop-holder div div img').css('visibility','hidden');   
                         jQuery('img#photo').Jcrop({
                            trueSize: [tw,th], 
                            aspectRatio: 1 / 1,
                            minSize:[150,150], 
                            setSelect:   [ x,y,150,150 ],
                            onSelect: updateCoords
                          });
                       
                jQuery('#image_name').val(jQuery('#photo').attr('file-name'));
                }
        }).submit();

    });
    
    jQuery('#btn-crop').on('click', function(e){
	    e.preventDefault();
	    params = {
	            targetUrl: pm_ajax_object.ajax_url,
                    action: 'pm_upload_image',
	            status: 'save',
	            x: jQuery('#x').val(),
	            y : jQuery('#y').val(),
	            w: jQuery('#w').val(),
	            h : jQuery('#h').val(),
                    fullpath:jQuery('#fullpath').val(),
                    user_id:jQuery('#user_id').val(),
                    user_meta:jQuery('#user_meta').val(),
                    attachment_id:jQuery('#attachment_id').val()
	        };
                
            jQuery.post(pm_ajax_object.ajax_url, params, function(response) {
                if(response)
                {
                    jQuery("#preview-avatar-profile").html(response);
                    location.reload(true);
                }	
            });		
	       
	    });
            
             jQuery('#btn-cancel').on('click', function(e){
	    e.preventDefault();
	    params = {
	            targetUrl: pm_ajax_object.ajax_url,
                    action: 'pm_upload_image',
	            status: 'cancel',
	            x: jQuery('#x').val(),
	            y : jQuery('#y').val(),
	            w: jQuery('#w').val(),
	            h : jQuery('#h').val(),
                    fullpath:jQuery('#fullpath').val(),
                    user_id:jQuery('#user_id').val(),
                    user_meta:jQuery('#user_meta').val(),
                    attachment_id:jQuery('#attachment_id').val()
	        };
                
            jQuery.post(pm_ajax_object.ajax_url, params, function(response) {
                if(response)
                {
                    //alert(response);
                    //jQuery("#preview-avatar-profile").html(response);
                    location.reload(true);
                }	
            });		
	       
	    });
    
});


jQuery(document).ready(function(){
		
    jQuery('#change-cover-pic').on('click', function(e) {
        jQuery('#changeCoverPic').show();
        jQuery('#change-cover-pic').hide();
        jQuery('#cover_minwidth').val(jQuery('.pmagic').innerWidth());
    });
    
    jQuery('#coverimg').on('change', function() { 
        jQuery("#preview-cover-image").html('');
        jQuery("#preview-cover-image").html('<div><div class="pm-loader"></div></div>');
        var pmDomColor = jQuery(".pmagic").children("a").css('color');
        jQuery(".pm-loader").css('border-top-color', pmDomColor);
        jQuery("#cropcoverimage").ajaxForm({
        target: '#preview-cover-image',
        success:    function() { 
                        jQuery('#cover-edit-img').hide();
                        jQuery("input[name='remove_image']").hide();
                        jQuery(".modal-footer").show();
                        var profileArea = jQuery('.pmagic').innerWidth();
                        tw = jQuery('#covertruewidth').val();
                        th = jQuery('#covertrueheight').val();
                        x = 18/100*tw;
                        y = 18/100*th;
                        if(x+profileArea>tw || y+300>th)
                        {
                            x = 0;
                            y = 0;
                        }
                         jQuery('img#coverimage').Jcrop({
                            trueSize: [tw,th], 
                            minSize:[profileArea,300], 
                            setSelect:   [ x,y,profileArea,300 ],
                            aspectRatio: profileArea/300,
                            onSelect: updateCoverCoords
                          });
                }
        }).submit();

    });
    
    jQuery('#btn-cover-crop').on('click', function(e){
	    e.preventDefault();
	    params = {
	            targetUrl: pm_ajax_object.ajax_url,
                    action: 'pm_upload_cover_image',
	            cover_status: 'save',
	            x: jQuery('#cx').val(),
	            y : jQuery('#cy').val(),
	            w: jQuery('#cw').val(),
	            h : jQuery('#ch').val(),
                    fullpath:jQuery('#coverfullpath').val(),
                    user_id:jQuery('#user_id').val(),
                    user_meta:'pm_cover_image',
                    attachment_id:jQuery('#cover_attachment_id').val()
	        };
                
            jQuery.post(pm_ajax_object.ajax_url, params, function(response) {
                if(response)
                {
                    jQuery("#preview-cover-image").html(response);
                    location.reload(true);
                }	
            });		
	       
	    });
            
             jQuery('#btn-cover-cancel').on('click', function(e){
	    e.preventDefault();
	    params = {
	            targetUrl: pm_ajax_object.ajax_url,
                    action: 'pm_upload_cover_image',
	            cover_status: 'cancel',
	            x: jQuery('#cx').val(),
	            y : jQuery('#cy').val(),
	            w: jQuery('#cw').val(),
	            h : jQuery('#ch').val(),
                    fullpath:jQuery('#coverfullpath').val(),
                    user_id:jQuery('#user_id').val(),
                    user_meta:jQuery('#user_meta').val(),
                    attachment_id:jQuery('#cover_attachment_id').val()
	        };
                
            jQuery.post(pm_ajax_object.ajax_url, params, function(response) {
                if(response)
                {
                    //alert(response);
                    //jQuery("#preview-avatar-profile").html(response);
                    location.reload(true);
                }	
            });		
	       
	    });
    
});




function updateCoords(c)
{
  jQuery('#x').val(c.x);
  jQuery('#y').val(c.y);
  jQuery('#w').val(c.w);
  jQuery('#h').val(c.h);
};

function updateCoverCoords(c)
{
  jQuery('#cx').val(c.x);
  jQuery('#cy').val(c.y);
  jQuery('#cw').val(c.w);
  jQuery('#ch').val(c.h);
};

  function checkCoords()
  {
    if (parseInt(jQuery('#w').val())) return true;
    alert('Please select a crop region then press submit.');
    return false;
  };

function checkCoverCoords()
  {
    if (parseInt(jQuery('#cw').val())) return true;
    alert('Please select a crop region then press submit.');
    return false;
  };
jQuery(document).ready(function(){
    pm_advance_user_search('');
});

 jQuery(document).ready(function() {
        //hook into heartbeat-send
         //   console.log('Is Rasdaseady');
        jQuery(document).on('heartbeat-send', function(e, data) {
             //    console.log('Is Ready');
                data['pm_notify_status'] = 'ready';	//need some data to kick off AJAX call
        });

        //hook into heartbeat-tick: client looks in data var for natifications
        jQuery(document).on('heartbeat-tick.profilegrid_tick', function(e, data) {
         //  console.log(data['unread_notif']);
              var uid = jQuery('#pm-uid').val();
           //   console.log('bhai'+ uid);
                    pm_get_my_friends(1,uid);
                    pm_get_friend_requests(1,uid);
                    pm_get_friend_requests_sent(1,uid);
                    pm_update_counter(uid);	
           var val=jQuery("#notification_tab").attr('aria-selected');
            if(val=== 'true'){
              //  console.log("active notification tab");
               if(jQuery("#unread_notification_count").val()!='')
                pm_read_all_notification();
            }else{
             //   console.log("inactive notification tab");
            }
           if(data['unread_notif']!=0)
           {
                jQuery("#unread_notification_count").addClass("thread-count-show"); 
                jQuery("#unread_notification_count").html(data['unread_notif']);   
                   
            }else
            {
                jQuery("#unread_notification_count").html('');   
                jQuery("#unread_notification_count").removeClass("thread-count-show");     
            }
    
           
                if(!data['pm_notify']){
              //  console.log("no data ");     
                return;
                    }
                jQuery.each( data['pm_notify'], function( index, notification ) {
               //         console.log(index);
                        if ( index != 'blabla' ){
                            if(notification!=''){
                   //             console.log("bhai working");
                                jQuery("#pm_notification_view_area").prepend(notification);
                            }
                        }
               //console.log(notification);			
        } ) ;
        });

        //hook into heartbeat-error: in case of error, let's log some stuff
        jQuery(document).on('heartbeat-error', function(e, jqXHR, textStatus, error) {
           //     console.log('BEGIN ERROR');
            //    console.log(textStatus);
            //    console.log(error);			
            //    console.log('END ERROR');			
        });


});

 function pm_delete_notification(id){
    var data = {action: 'pm_delete_notification', 'id': id};
    jQuery.post(pm_ajax_object.ajax_url, data, function (response) {
        if(response)
        {
         //   console.log("Delete successful");
            jQuery("#notif_"+id).fadeOut(300,function(){jQuery(this).remove();});
        }
    });
}

function pm_load_more_notification(loadnum){
      jQuery("#pm_load_more_notif").remove();
      var data = {action: 'pm_load_more_notification','loadnum':loadnum};
       jQuery.post(pm_ajax_object.ajax_url, data, function (response) {
        if(response)
        {
            jQuery('#pm_notification_view_area').append(response);
        }
    });
  
}

function pm_read_all_notification(){
      var data = {action: 'pm_read_all_notification'};
       jQuery.post(pm_ajax_object.ajax_url, data, function (response) {
        if(response)
        {
         //   jQuery('#pm_notification_view_area').append(response);
        }
    });
   
}
function read_notification(){
    jQuery("#unread_notification_count").html('');   
    jQuery("#unread_notification_count").removeClass("thread-count-show");  
    refresh_notification();
    pm_read_all_notification();
}

function refresh_notification(){
  //  console.log("refreshing notification");
     var data = {action: 'pm_refresh_notification'};
       jQuery.post(pm_ajax_object.ajax_url, data, function (response) {
        if(response)
        {
            jQuery('#pm_notification_view_area').html('');
            jQuery('#pm_notification_view_area').append(response);
        }
    });
}

function pm_get_dom_color()
{
    var pmDomColor = jQuery(".pmagic").children("a").css('color');
      jQuery(".pm-color").css('color', pmDomColor);
      return pmDomColor;
      
}