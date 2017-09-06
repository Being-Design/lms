<?php
$pm_u_search = filter_input(INPUT_GET,'pm_u_search');
$suggestions = $pmfriends->profile_magic_friends_suggestion($uid);
if(empty($suggestions)){
    echo '<p class="pm-no-result-found">'.__('No suggestions available for friends',$this->profile_magic).'</p>';
}else{
//print_r($suggestions);die;
$suggestions_users =  $dbhandler->pm_get_all_users($pm_u_search,$meta_query_array,'',$offset,$limit,'ASC','include',array(),$date_query,$suggestions);
$suggestions_total_users = count($dbhandler->pm_get_all_users($pm_u_search,$meta_query_array,'','','','ASC','include',array(),$date_query,$suggestions));
$num_of_suggestions_pages = ceil( $suggestions_total_users/$limit);
?>
<div class="pm_search_form">
<form name="pm_search_users" id="pm_search_users">
<input type="text" id="pm_u_search" name="pm_u_search" value="<?php echo $pm_u_search; ?>" />
<input type="hidden" id="pm_tab" name="pm_tab" value="2" />
<input type="submit" name="pm_u_search_button" id="pm_u_search_button" value="<?php _e('Search',$this->profile_magic);?>" />
</form>
</div>

<div class="pm-my-suggestions">
<?php $pmfriends->profile_magic_friends_result_html($suggestions_users,$uid);?>
</div>
<?php if($num_of_suggestions_pages>1):?>
<div class="pm-more-suggestion-result">
<button id="pm_load_more_suggestion" value="2" onclick="pm_load_more_suggestion(<?php echo $uid;?>,this.value,<?php echo $num_of_suggestions_pages;?>)"><?php _e('Load More Result',$this->profile_magic);?></button>
<div class="pm-loader-img-suggestion"><img src="<?php echo $path;?>images/ajax-loader.gif" width="" height=""/></div>
</div>
<?php endif;?>
<?php } ?>