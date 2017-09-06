<?php
$dbhandler = new PM_DBhandler;
$pmrequests = new PM_request;
$textdomain = $this->profile_magic;
$path = plugin_dir_url(__FILE__);
$gid = filter_input(INPUT_GET, 'gid');
$identifier = 'FIELDS';
$groups = $dbhandler->get_all_result('GROUPS', 'id,group_name');
$sections = $dbhandler->get_all_result('SECTION', 'id,section_name', array('gid' => $gid), 'results', 0, false, $sort_by = 'ordering');
//print_r($sections);die;
$fields = $dbhandler->get_all_result($identifier, $column = '*', array('associate_group' => $gid), 'results', 0, false, $sort_by = 'ordering');
$lastrow = $dbhandler->get_all_result('SECTION', 'id', 1, 'var', 0, 1, 'id', 'DESC');
$section_ordering = $lastrow + 1;
?>

<div class="pm-popup">
    <div class="pm-popup-header">
        <div class="pm-popup-title">
            <?php _e('Choose a field type', 'profile-grid'); ?>
        </div>
        <img class="pm-popup-close" src="<?php echo $path; ?>images/close-pm.png">
    </div>
   
        <div class="pm-field-selection">
        <div class="pm-popup-field-box">
            <div class="pm-popup-field-name" onClick="add_pm_field('first_name', '<?php echo $gid; ?>')">
                <i class="fa fa-user-circle-o" aria-hidden="true"></i>
                <?php _e('First Name', 'profile-grid'); ?>
            </div>
            <div class="pm-popup-field-details"><?php _e("This field is connected directly to WordPress’ User Profile First Name field.", 'profile-grid'); ?></div>
        </div>
        <div class="pm-popup-field-box">
            <div class="pm-popup-field-name" onClick="add_pm_field('last_name', '<?php echo $gid; ?>')">
                <i class="fa fa-user-circle-o" aria-hidden="true"></i>
                <?php _e('Last Name', 'profile-grid'); ?>
            </div>
            <div class="pm-popup-field-details"><?php _e("This field is connected directly to WordPress’ User Profile Last Name field.", 'profile-grid'); ?></div>
        </div>
        <div class="pm-popup-field-box">
            <div class="pm-popup-field-name" onClick="add_pm_field('user_name', '<?php echo $gid; ?>')">
                <i class="fa fa-user-circle-o" aria-hidden="true"></i>
                <?php _e('User Name', 'profile-grid'); ?>
            </div>
            <div class="pm-popup-field-details"><?php _e("If you want members to login using Username instead of email.", 'profile-grid'); ?></div>
        </div>
        <?php
        $check = $pmrequests->pm_check_field_exist($gid, 'user_email', true);
        if ($check === false):
            ?>
            <div class="pm-popup-field-box">
                <div class="pm-popup-field-name" onClick="add_pm_field('user_email', '<?php echo $gid; ?>')">
                    <?php _e('User Email', 'profile-grid'); ?>
                </div>
                <div class="pm-popup-field-details"><?php _e("Member's email. Only use <u>once</u> for each profile. For secondary email, use <i>Email</i> field type.", 'profile-grid'); ?></div>
            </div>
        <?php endif; ?>
        <div class="pm-popup-field-box">
            <div class="pm-popup-field-name" onClick="add_pm_field('user_url', '<?php echo $gid; ?>')">
                <i class="fa fa-globe" aria-hidden="true"></i>
                <?php _e('User URL', 'profile-grid'); ?>
            </div>
            <div class="pm-popup-field-details"><?php _e("Allows members to add website address to their profiles.", 'profile-grid'); ?></div>
        </div>
        <div class="pm-popup-field-box">
            <div class="pm-popup-field-name" onClick="add_pm_field('user_pass', '<?php echo $gid; ?>')">
                <i class="fa fa-key" aria-hidden="true"></i>
                <?php _e('Password', 'profile-grid'); ?>
            </div>
            <div class="pm-popup-field-details"><?php _e("Allows members to define their own password during registration.", 'profile-grid'); ?></div>
        </div>
        <div class="pm-popup-field-box">
            <div class="pm-popup-field-name" onClick="add_pm_field('confirm_pass', '<?php echo $gid; ?>')">
                <i class="fa fa-key" aria-hidden="true"></i>
                <?php _e('Confirm Password', 'profile-grid'); ?>
            </div>
            <div class="pm-popup-field-details"><?php _e("To be used in conjunction with <i>Password</i> field.", 'profile-grid'); ?></div>
        </div>
        <div class="pm-popup-field-box">
            <div class="pm-popup-field-name" onClick="add_pm_field('description', '<?php echo $gid; ?>')">
                <i class="fa fa-info-circle" aria-hidden="true"></i>
                <?php _e('Biographical Info', 'profile-grid'); ?>
            </div>
            <div class="pm-popup-field-details"><?php _e("Allows members to enter long text.", 'profile-grid'); ?></div>
        </div>
        <div class="pm-popup-field-box">
            <div class="pm-popup-field-name" onClick="add_pm_field('user_avatar', '<?php echo $gid; ?>')">
                <i class="fa fa-camera-retro" aria-hidden="true"></i>
                <?php _e('Profile Image', 'profile-grid'); ?>
            </div>
            <div class="pm-popup-field-details"><?php _e("Allows members to upload their Profile image during registration. This can be changed or removed later.", 'profile-grid'); ?></div>
        </div>

        <div class="pm-popup-field-box">
            <div class="pm-popup-field-name" onClick="add_pm_field('text', '<?php echo $gid; ?>')">
                <i class="fa fa-text-width" aria-hidden="true"></i>
                <?php _e('Text', 'profile-grid'); ?>
            </div>
            <div class="pm-popup-field-details"><?php _e("The common text field allowing user a single line of text.", 'profile-grid'); ?></div>
        </div>
        <div class="pm-popup-field-box">
            <div class="pm-popup-field-name" onClick="add_pm_field('select', '<?php echo $gid; ?>')">
                <i class="fa fa-chevron-circle-down" aria-hidden="true"></i>
                <?php _e('DropDown', 'profile-grid'); ?>
            </div>
            <div class="pm-popup-field-details"><?php _e("A dropdown with selection of predefined options. Members can only select one option.", 'profile-grid'); ?></div>
        </div>
        <div class="pm-popup-field-box">
            <div class="pm-popup-field-name" onClick="add_pm_field('radio', '<?php echo $gid; ?>')">
                <i class="fa fa-circle-o" aria-hidden="true"></i>
                <?php _e('Radio', 'profile-grid'); ?>
            </div>
            <div class="pm-popup-field-details"><?php _e("A radiobox selection with predefined options. Members can only select one option.", 'profile-grid'); ?></div>
        </div>
        <div class="pm-popup-field-box">
            <div class="pm-popup-field-name" onClick="add_pm_field('textarea', '<?php echo $gid; ?>')">
                <i class="fa fa-bars" aria-hidden="true"></i>
                <?php _e('Text Area', 'profile-grid'); ?>
            </div>
            <div class="pm-popup-field-details"><?php _e("A text area box with ability to add multiple lines of plain text.", 'profile-grid'); ?></div>
        </div>
        <div class="pm-popup-field-box">
            <div class="pm-popup-field-name" onClick="add_pm_field('checkbox', '<?php echo $gid; ?>')">
                <i class="fa fa-check-square-o" aria-hidden="true"></i>
                <?php _e('Checkbox', 'profile-grid'); ?>
            </div>
            <div class="pm-popup-field-details"><?php _e("A checkbox selection with predefined options. Members can select multiple options.", 'profile-grid'); ?></div>
        </div>
        <div class="pm-popup-field-box">
            <div class="pm-popup-field-name" onClick="add_pm_field('heading', '<?php echo $gid; ?>')">
                <i class="fa fa-header" aria-hidden="true"></i>
                <?php _e('Heading', 'profile-grid'); ?>
            </div>
            <div class="pm-popup-field-details"><?php _e("Large size read only text, useful for creating custom headings.", 'profile-grid'); ?></div>
        </div>
        <div class="pm-popup-field-box">
            <div class="pm-popup-field-name" onClick="add_pm_field('paragraph', '<?php echo $gid; ?>')">
                <i class="fa fa-paragraph" aria-hidden="true"></i>
                <?php _e('Paragraph', 'profile-grid'); ?>
            </div>
            <div class="pm-popup-field-details"><?php _e("This is a read only field which can be used to display formatted content inside the form. HTML is supported.", 'profile-grid'); ?></div>
        </div>
        <div class="pm-popup-field-box">
            <div class="pm-popup-field-name" onClick="add_pm_field('DatePicker', '<?php echo $gid; ?>')">
                <i class="fa fa-calendar" aria-hidden="true"></i>
                <?php _e('Date', 'profile-grid'); ?>
            </div>
            <div class="pm-popup-field-details"><?php _e("Allows users to pick a date from dropdown calendar or type it manually.", 'profile-grid'); ?></div>
        </div>
        <div class="pm-popup-field-box">
            <div class="pm-popup-field-name" onClick="add_pm_field('email', '<?php echo $gid; ?>')">
                <i class="fa fa-envelope" aria-hidden="true"></i>
                <?php _e('Email', 'profile-grid'); ?>
            </div>
            <div class="pm-popup-field-details"><?php _e("A secondary email field.", 'profile-grid'); ?></div>
        </div>
        <div class="pm-popup-field-box">
            <div class="pm-popup-field-name" onClick="add_pm_field('number', '<?php echo $gid; ?>')">
                <i class="fa fa-sort-numeric-desc" aria-hidden="true"></i>
                <?php _e('Number', 'profile-grid'); ?>
            </div>
            <div class="pm-popup-field-details"><?php _e("Allows user to input value in numbers.", 'profile-grid'); ?></div>
        </div>
        <div class="pm-popup-field-box">
            <div class="pm-popup-field-name" onClick="add_pm_field('country', '<?php echo $gid; ?>')">
                <i class="fa fa-flag" aria-hidden="true"></i>
                <?php _e('Country', 'profile-grid'); ?>
            </div>
            <div class="pm-popup-field-details"><?php _e("A drop down list of all countries appears to the user for selection.", 'profile-grid'); ?></div>
        </div>
        <div class="pm-popup-field-box">
            <div class="pm-popup-field-name" onClick="add_pm_field('timezone', '<?php echo $gid; ?>')">
                <i class="fa fa-clock-o" aria-hidden="true"></i>
                <?php _e('Timezone', 'profile-grid'); ?>
            </div>
            <div class="pm-popup-field-details"><?php _e("A drop down list of all time-zones appears to the user for selection.", 'profile-grid'); ?></div>
        </div>
        <div class="pm-popup-field-box">
            <div class="pm-popup-field-name" onClick="add_pm_field('term_checkbox', '<?php echo $gid; ?>')">
                <i class="fa fa-check" aria-hidden="true"></i>
                <?php _e('T&C Checkbox', 'profile-grid'); ?>
            </div>
            <div class="pm-popup-field-details"><?php _e("Useful for adding terms and conditions to the form. User must select the check box to continue with submission if you select “Is Required” below.", 'profile-grid'); ?></div>
        </div>
        <div class="pm-popup-field-box">
            <div class="pm-popup-field-name" onClick="add_pm_field('file', '<?php echo $gid; ?>')">
                <i class="fa fa-files-o" aria-hidden="true"></i>
                <?php _e('File', 'profile-grid'); ?>
            </div>
            <div class="pm-popup-field-details"><?php _e("Display a field to the user for attaching files from his/ her computer.", 'profile-grid'); ?></div>
        </div>
        <div class="pm-popup-field-box">
            <div class="pm-popup-field-name" onClick="add_pm_field('repeatable_text', '<?php echo $gid; ?>')">
                <i class="fa fa-repeat" aria-hidden="true"></i>
                <?php _e('Repeatable Text', 'profile-grid'); ?>
            </div>
            <div class="pm-popup-field-details"><?php _e("Allows user to add extra text field boxes to the form for submitting different values. Useful where a field requires multiple user input values.", 'profile-grid'); ?></div>
        </div>
        <div class="pm-popup-field-box">
            <div class="pm-popup-field-name" onClick="add_pm_field('mobile_number', '<?php echo $gid; ?>')">
                <i class="fa fa-mobile" aria-hidden="true"></i>
                <?php _e('Mobile Number', 'profile-grid'); ?>
            </div>
            <div class="pm-popup-field-details"><?php _e("Adds a Mobile number field.", 'profile-grid'); ?></div>
        </div>

        <div class="pm-popup-field-box">
            <div class="pm-popup-field-name" onClick="add_pm_field('phone_number', '<?php echo $gid; ?>')">
                <i class="fa fa-phone" aria-hidden="true"></i>
                <?php _e('Phone Number', 'profile-grid'); ?>
            </div>
            <div class="pm-popup-field-details"><?php _e("Adds a phone number field.", 'profile-grid'); ?></div>
        </div>

        <div class="pm-popup-field-box">
            <div class="pm-popup-field-name" onClick="add_pm_field('gender', '<?php echo $gid; ?>')">
                <i class="fa fa-venus-mars" aria-hidden="true"></i>
                <?php _e('Gender', 'profile-grid'); ?>
            </div>
            <div class="pm-popup-field-details"><?php _e("Gender/ Sex selection radio box.", 'profile-grid'); ?></div>
        </div>

        <div class="pm-popup-field-box">
            <div class="pm-popup-field-name" onClick="add_pm_field('language', '<?php echo $gid; ?>')">
                <i class="fa fa-language" aria-hidden="true"></i>
                <?php _e('Language', 'profile-grid'); ?>
            </div>
            <div class="pm-popup-field-details"><?php _e("Adds a drop down language selection field with common languages as options.", 'profile-grid'); ?></div>
        </div>

        <?php /*<div class="pm-popup-field-box">
            <div class="pm-popup-field-name" onClick="add_pm_field('birth_date', '<?php echo $gid; ?>')">
                <i class="fa fa-calendar" aria-hidden="true"></i>
                <?php _e('Birth Date', 'profile-grid'); ?>
            </div>
            <div class="pm-popup-field-details"><?php _e("Allows user to add extra text field boxes to the form for submitting different values. Useful where a field requires multiple user input values.", 'profile-grid'); ?></div>
        </div>*/ ?>

        <div class="pm-popup-field-box">
            <div class="pm-popup-field-name" onClick="add_pm_field('divider', '<?php echo $gid; ?>')">
                <i class="fa fa-minus" aria-hidden="true"></i>
                <?php _e('Divider', 'profile-grid'); ?>
            </div>
            <div class="pm-popup-field-details"><?php _e("Divider for separating fields.", 'profile-grid'); ?></div>
        </div>

        <div class="pm-popup-field-box">
            <div class="pm-popup-field-name" onClick="add_pm_field('spacing', '<?php echo $gid; ?>')">
                <i class="fa fa-minus" aria-hidden="true"></i>
                <?php _e('Spacing', 'profile-grid'); ?>
            </div>
            <div class="pm-popup-field-details"><?php _e("Useful for adding space between fields.", 'profile-grid'); ?></div>
        </div>

        <div class="pm-popup-field-box">
            <div class="pm-popup-field-name" onClick="add_pm_field('multi_dropdown', '<?php echo $gid; ?>')">
                <i class="fa fa-angle-double-down" aria-hidden="true"></i>
                <?php _e('Multi Dropdown', 'profile-grid'); ?>
            </div>
            <div class="pm-popup-field-details"><?php _e("A dropdown field with a twist. Users can now select more than one option.", 'profile-grid'); ?></div>
        </div>

        <div class="pm-popup-field-box">
            <div class="pm-popup-field-name" onClick="add_pm_field('facebook', '<?php echo $gid; ?>')">
                <i class="fa fa-facebook" aria-hidden="true"></i>
                <?php _e('Facebook', 'profile-grid'); ?>
            </div>
            <div class="pm-popup-field-details"><?php _e("A speciality URL field for asking Facebook Profile page.", 'profile-grid'); ?></div>
        </div>

        <div class="pm-popup-field-box">
            <div class="pm-popup-field-name" onClick="add_pm_field('twitter', '<?php echo $gid; ?>')">
                <i class="fa fa-twitter" aria-hidden="true"></i>
                <?php _e('Twitter', 'profile-grid'); ?>
            </div>
            <div class="pm-popup-field-details"><?php _e("A speciality URL field for asking Twitter Profile page.", 'profile-grid'); ?></div>
        </div>


        <div class="pm-popup-field-box">
            <div class="pm-popup-field-name" onClick="add_pm_field('google', '<?php echo $gid; ?>')">
                <i class="fa fa-google-plus" aria-hidden="true"></i>
                <?php _e('Google+', 'profile-grid'); ?>
            </div>
            <div class="pm-popup-field-details"><?php _e("A speciality URL field for asking Google+ Profile page.", 'profile-grid'); ?></div>
        </div>


        <div class="pm-popup-field-box">
            <div class="pm-popup-field-name" onClick="add_pm_field('linked_in', '<?php echo $gid; ?>')">
                <i class="fa fa-linkedin" aria-hidden="true"></i>
                <?php _e('Linked In', 'profile-grid'); ?>
            </div>
            <div class="pm-popup-field-details"><?php _e("A speciality URL field for asking Linkedin Profile page.", 'profile-grid'); ?></div>
        </div>


        <div class="pm-popup-field-box">
            <div class="pm-popup-field-name" onClick="add_pm_field('youtube', '<?php echo $gid; ?>')">
                <i class="fa fa-youtube" aria-hidden="true"></i>
                <?php _e('Youtube', 'profile-grid'); ?>
            </div>
            <div class="pm-popup-field-details"><?php _e("A speciality URL field for asking YouTube Channel or Video page.", 'profile-grid'); ?></div>
        </div>


        <div class="pm-popup-field-box">
            <div class="pm-popup-field-name" onClick="add_pm_field('instagram', '<?php echo $gid; ?>')">
                <i class="fa fa-instagram   " aria-hidden="true"></i>
                <?php _e('Instagram', 'profile-grid'); ?>
            </div>
            <div class="pm-popup-field-details"><?php _e("Asks User his/ her Instagram Profile.", 'profile-grid'); ?></div>
        </div>
            
            <?php do_action('pg_add_field_in_popup',$gid);?>
            
    </div>
</div>

    <div class="pm-curtains"></div>
    <div class="pmagic"> 

        <!-----Operationsbar Starts----->

        <div class="operationsbar">
            <div class="pmtitle">
                <?php _e('Custom Fields Manager', 'profile-grid'); ?>
            </div>
            <div class="icons"><a href="admin.php?page=pm_add_group&id=<?php echo $gid; ?>"><img src="<?php echo $path; ?>images/global-settings.png"></a></div>
            <div class="nav">
                <ul>
                    <li><a href="#" id="pm-field-selection-popup">
                            <i class="fa fa-plus" aria-hidden="true"></i>
                            <?php _e('New Field', 'profile-grid'); ?>
                        </a></li>
                    <li><a href="#new_section" onclick="pm_open_tab('new_section')">
                            <i class="fa fa-plus" aria-hidden="true"></i>
                            <?php _e('New Section', 'profile-grid'); ?>
                        </a></li>
                    <li class="pm-form-toggle">
                        <i class="fa fa-filter" aria-hidden="true"></i>
                        <?php _e('Filter by', 'profile-grid'); ?>
                        <select name="associate_group" id="associate_group" onChange="redirectpmform(this.value, 'pm_profile_fields')">
                            <option value="">
                                <?php _e('Select A Group', 'profile-grid'); ?>
                            </option>
                            <?php
                            foreach ($groups as $group) {
                                ?>
                                <option value="<?php echo $group->id; ?>" <?php if (!empty($gid)) selected($gid, $group->id); ?>><?php echo $group->group_name; ?></option>
                            <?php }
                            ?>
                        </select>
                    </li>
                </ul>
            </div>
        </div>
        <!--------Operationsbar Ends-----> 

        <!----Slab View---->

        <div class="pm-field-creator" id="sections">
            <ul class="pm-page-tabs-sidebar field-tabs pm_sortable_tabs">
                <?php
                if (isset($sections)):
                    foreach ($sections as $section):
                        ?>
                        <li class="pm-page-tab field-tabs-row" id="<?php echo $section->id; ?>">
                            <div class="pm-slab-drag-handle">&nbsp;</div>
                            <a href="#<?php echo sanitize_key($section->section_name); ?>" onclick="pm_open_tab('<?php echo sanitize_key($section->section_name); ?>')">
                        <?php _e($section->section_name, $textdomain); ?>
                            </a> </li>
    <?php endforeach;
endif; ?>
                <li><a href="#new_section"></a></li>
            </ul>
            <div class="pm-custom-fields-page">
                    <?php if (isset($sections)): foreach ($sections as $section): ?>
                        <div id="<?php echo sanitize_key($section->section_name); ?>" class="field-selector-pills">
                                <?php $fields = $dbhandler->get_all_result($identifier, $column = '*', array('associate_group' => $gid, 'associate_section' => $section->id), 'results', 0, false, $sort_by = 'ordering'); ?>
                            <div class="pmrow"> <a href="admin.php?page=pm_add_section&id=<?php echo $section->id; ?>#<?php echo sanitize_key($section->section_name); ?>"><i class="fa fa-pencil" aria-hidden="true"></i>Edit Section</a> <a href="admin.php?page=pm_add_section&action=delete&id=<?php echo $section->id; ?>&gid=<?php echo $gid; ?>"><i class="fa fa-trash" aria-hidden="true"></i>Delete Section</a> </div>
                            <ul class="pm_sortable_fields">
                                <?php
                                if (!empty($fields)):
                                    foreach ($fields as $field):
                                        ?>
                                        <li id="<?php echo $field->field_id; ?>">
                                            <div class="pm-custom-field-page-slab">
                                                <div class="pm-slab-drag-handle">&nbsp;</div>
                                                <div class="pm-slab-info"><?php echo $field->field_name; ?> <sup><?php echo $field->field_type; ?></sup></div>
                                                <div class="pm-slab-buttons"><a href="admin.php?page=pm_add_field&id=<?php echo $field->field_id; ?>#<?php echo sanitize_key($section->section_name); ?>"><i class="fa fa-pencil" aria-hidden="true"></i>Edit</a><a href="admin.php?page=pm_add_field&id=<?php echo $field->field_id; ?>&action=delete#<?php echo sanitize_key($section->section_name); ?>"><i class="fa fa-trash" aria-hidden="true"></i>Delete</a></div>
                                            </div>
                                        </li>
            <?php
            endforeach;
        else:
            ?>
                                    <li>
                                        <div class="pm-slab"><?php _e("You haven't created any Profile Fields for this Section yet.", 'profile-grid'); ?></div>
                                    </li>
                        <?php
                        endif;
                        ?>
                            </ul>
                        </div>
    <?php endforeach;
endif; ?>
                <div id="new_section" class="field-selector-pills">
                    <div class="uimagic pm-section-form">
                        <form name="pm_add_section" id="pm_add_section" method="post" action="admin.php?page=pm_add_section">
                            <!-----Dialogue Box Starts----->
                            <div class="content">
                                <div class="uimrow">
                                    <div class="uimfield">
<?php _e('Section Name', 'profile-grid'); ?>
                                        <sup>*</sup></div>
                                    <div class="uiminput pm_required">
                                        <input type="text" name="section_name" id="section_name" value="<?php if (!empty($row)) echo esc_attr($row->section_name); ?>" />
                                        <div class="errortext"></div>
                                    </div>
                                    <div class="uimnote"><?php _e("Name of your Group.", 'profile-grid'); ?></div>
                                </div>
                                <div class="uimrow">
                                    <div class="uimfield"> </div>
                                    <div class="uiminput">
                                        <input type="hidden" name="gid" id="gid" value="<?php echo $gid; ?>" />
                                        <input type="hidden" name="section_id" id="section_id" value="0" />
                                        <input type="hidden" name="ordering" id="ordering" value="<?php echo $section_ordering; ?>" />
<?php wp_nonce_field('save_pm_add_section'); ?>
                                        <input type="submit" value="<?php _e('Save', 'profile-grid'); ?>" name="submit_section" id="submit_section" onClick="return add_section_validation()"  />
                                        <div class="all_error_text" style="display:none;"></div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php if ($pmrequests->pm_check_field_exist($gid, 'user_pass', true) == false): ?>
        <div class="pm-notice">
            <div class="pm-notice-text"><?php _e('There’s no password field in your sign up form. You can auto-generated and email password to registering users. To do that please add password field to Account Activation email template associated with this Group.', 'profile-grid'); ?></div>
        </div>
<?php endif; ?>

    
<!-- Wrap PopUP Field Box  -->
<script>
jQuery(document).ready(function($) {
    var a = jQuery('.pm-popup .pm-field-selection .pm-popup-field-box');
for( var i = 0; i < a.length; i+=2 ) {
    a.slice(i, i+2).wrapAll('<div class="pm-popup-field-box-wrap"></div>');
}
});

</script>
