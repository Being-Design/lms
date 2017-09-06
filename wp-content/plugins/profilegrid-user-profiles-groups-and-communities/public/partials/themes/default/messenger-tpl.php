<?php
$pmhtmlcreator = new PM_HTML_Creator($this->profile_magic, $this->version);
$pmrequests = new PM_request;
?>
<div class="pmagic"> 
    <!-----Operationsbar Starts----->
    <div class="pm-group-view pm-dbfl pm-border-bt">
        <div class="pm-section pm-dbfl" > 
            <div class="pm-section-nav-vertical pm-difl pm-border pm-radius5 pm-bg" id="thread_pane">
                <ul class="dbfl" id="threads_ul">

                </ul>
            </div>
            <form id="chat_message_form" onsubmit="pm_messenger_send_chat_message(event);">  
                <input type="text" id="receipent_field"  value="" placeholder="send to.." style="min-width: 550px;"/>
                <input type="hidden" id="receipent_field_rid" name="rid" value=""  />   
                <div id="message_display_area" class="pm-section-content pm-difl pm_full_width_profile"  style="min-height:400px;max-height:400px;max-width: 550px;overflow-y: scroll;">

                </div>

                <div class="pm-section-content pm-difl pm_full_width_profile"  style="max-width: 550px;float:right;">
                    <div id="typing_on"></div>
                    <input type="hidden" name="action" value='pm_messenger_send_new_message' /> 
                    <input type="hidden" id="thread_hidden_field" name="tid" value=""/>
                    <textarea id="messenger_textarea" name="content" style="width:80%;height:100px;"
                              data-emoji-input="unicode" data-emojiable="true"
                               form="chat_message_form" placeholder="Enter text here.." onkeypress=""></textarea> 
                    <input type="hidden" name="sid" value="" />      
                    <input type="submit" name="send" value="send"/>
                </div>
            </form>

        </div>
    </div>
</div>
