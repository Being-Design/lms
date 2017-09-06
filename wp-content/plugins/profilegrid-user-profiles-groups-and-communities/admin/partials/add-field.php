<?php
$dbhandler = new PM_DBhandler;
$pm_activator = new Profile_Magic_Activator;
$pmrequests = new PM_request;
$textdomain = $this->profile_magic;
$path = plugin_dir_url(__FILE__);
$id = filter_input(INPUT_GET, 'id');
$str = filter_input(INPUT_GET, 'type');
$gid = filter_input(INPUT_POST, 'gid');
if ($gid == false || $gid == NULL) {
    $gid = filter_input(INPUT_GET, 'gid');
}
$identifier = 'FIELDS';

if ($id == false || $id == NULL) {
    $id = 0;
    $lastrow = $dbhandler->pm_count($identifier);
    $lastrow = $dbhandler->get_all_result($identifier, 'field_id', 1, 'var', 0, 1, 'field_id', 'DESC');
    $ordering = $lastrow + 1;
} else {
    $row = $dbhandler->get_row($identifier, $id);

    if (!empty($row)) {
        if ($row->field_options != "")
            $field_options = maybe_unserialize($row->field_options);
        $ordering = $row->ordering;
        $str = $row->field_type;
        $gid = $row->associate_group;
    }
}
$groups = $dbhandler->get_all_result('GROUPS', 'id,group_name');
$sections = $dbhandler->get_all_result('SECTION', 'id,section_name', array('gid' => $gid));
//print_r($sections);

if (filter_input(INPUT_POST, 'submit_field')) {
    $field_type = filter_input(INPUT_POST, 'field_type');
    $check = $pmrequests->pm_check_field_exist($gid, 'user_email', true);
    if ($check == true && $field_type == 'user_email' && $str != 'user_email') {
        echo '<div class="error">' . __('you have already created a user email field.', 'profile-grid') . '</div>';
    } else {
        $retrieved_nonce = filter_input(INPUT_POST, '_wpnonce');
        if (!wp_verify_nonce($retrieved_nonce, 'save_pm_add_field'))
            die(__('Failed security check', 'profile-grid'));
        $fieldid = filter_input(INPUT_POST, 'field_id');
        $exclude = array("_wpnonce", "_wp_http_referer", "submit_field", "field_id");
        $post = $pmrequests->sanitize_request($_POST, $identifier, $exclude);
        $sectionname = $dbhandler->get_value('SECTION', 'section_name', $post['associate_section']);
        if ($field_type == 'user_email')
            $post['show_in_signup_form'] = 1;
        if (!isset($post['show_in_signup_form']))
            $post['show_in_signup_form'] = 0;
        if (!isset($post['is_required']))
            $post['is_required'] = 0;
        if (!isset($post['is_editable']))
            $post['is_editable'] = 0;
        if (!isset($post['display_on_profile']))
            $post['display_on_profile'] = 0;
        if (!isset($post['display_on_group']))
            $post['display_on_group'] = 0;
        if ($post != false) {
            foreach ($post as $key => $value) {
                $data[$key] = $value;
                $arg[] = $pm_activator->get_db_table_field_type($identifier, $key);
            }
        }
        if ($data['field_key'] == '') {
            if ($fieldid == 0) {
                $field_key_id = $data['ordering'];
            } else {
                $field_key_id = $fieldid;
            }
            $data['field_key'] = $pmrequests->get_field_key($data['field_type'], $field_key_id);
        } else {
            if ($pmrequests->get_default_key_type($data['field_type'])) {
                $data['field_key'] = $pmrequests->get_field_key($data['field_type'], $field_key_id);
            }
        }
        if ($fieldid == 0) {
            $dbhandler->insert_row($identifier, $data, $arg);
        } else {
            $dbhandler->update_row($identifier, 'field_id', $fieldid, $data, $arg, '%d');
        }

        wp_redirect('admin.php?page=pm_profile_fields&gid=' . $gid . '#' . sanitize_key($sectionname));
        exit;
    }
}

if (filter_input(INPUT_POST, 'delete')) {
    $selected = filter_input(INPUT_POST, 'selected', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

    foreach ($selected as $fid) {
        $dbhandler->remove_row($identifier, 'field_id', $fid, '%d');
    }

    wp_redirect('admin.php?page=pm_profile_fields&gid=' . $gid);
    exit;
}

if (filter_input(INPUT_POST, 'duplicate')) {
    $selected = filter_input(INPUT_POST, 'selected', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    foreach ($selected as $fid) {
        $data = $dbhandler->get_row($identifier, $fid, 'field_id', 'ARRAY_A');
        unset($data['field_id']);
        $field_key_id = $ordering;
        $data['field_key'] = $pmrequests->get_field_key($data['field_type'], $field_key_id);
        $dbhandler->insert_row($identifier, $data);
    }
    wp_redirect('admin.php?page=pm_profile_fields&gid=' . $gid);
    exit;
}

if (filter_input(INPUT_GET, 'action') == 'delete') {
    $dbhandler->remove_row($identifier, 'field_id', $id, '%d');
    wp_redirect('admin.php?page=pm_profile_fields&gid=' . $gid);
    exit;
}
?>

<div class="uimagic">
    <form name="pm_add_group" id="pm_add_field" method="post">
        <!-----Dialogue Box Starts----->
        <div class="content">
            <?php if ($id == 0): ?>
                <div class="uimheader">
                    <?php _e('New Field', 'profile-grid'); ?>
                </div>
            <?php else: ?>
                <div class="uimheader">
                    <?php _e('Edit Field', 'profile-grid'); ?>
                </div>
            <?php endif; ?>
            <div class="uimsubheader">
                <?php
                //Show subheadings or message or notice
                ?>
            </div>
            <div class="uimrow">
                <div class="uimfield">
                    <?php _e('Field Name', 'profile-grid'); ?>
                    <sup>*</sup></div>
                <div class="uiminput pm_required">
                    <input type="text" name="field_name" id="field_name" value="<?php if (!empty($row)) echo esc_attr($row->field_name); ?>" />
                    <div class="errortext"></div>
                </div>
                <div class="uimnote"><?php _e('Label of the field as it appears on forms and profiles. This does not apply to fields without labels such as Heading, Paragraph, Divider, Spacing.', 'profile-grid'); ?></div>
            </div>
            <div class="uimrow">
                <div class="uimfield">
                    <?php _e('Field Description:', 'profile-grid'); ?>
                </div>
                <div class="uiminput">
                    <textarea name="field_desc" id="field_desc"><?php if (!empty($row)) echo esc_attr($row->field_desc); ?>
                    </textarea>
                </div>
                <div class="uimnote"><?php _e('For your reference only. Not visible on front-end. Description can help you remember the purpose of the field.', 'profile-grid'); ?> </div>
            </div>

            <div class="uimrow">
                <div class="uimfield">
                    <?php _e('Field Type', 'profile-grid'); ?>
                </div>
                <div class="uiminput">
                    <select name="field_type" id="field_type" onChange="pm_show_hide_field_option(this.value, 'field_options_wrapper')">
                        <option value=""><?php _e('Select A Field', 'profile-grid'); ?></option>
                        <option value="heading" <?php if (isset($str) && $str == 'heading') echo 'selected'; ?>><?php _e('Heading', 'profile-grid'); ?></option>
                        <option value="paragraph" <?php if (isset($str) && $str == 'paragraph') echo 'selected'; ?>><?php _e('Paragraph', 'profile-grid'); ?></option>
                        <option value="text" <?php if (isset($str) && $str == 'text') echo 'selected'; ?>><?php _e('Text', 'profile-grid'); ?></option>
                        <option value="select" <?php if (isset($str) && $str == 'select') echo 'selected'; ?>><?php _e('Drop Down', 'profile-grid'); ?></option>
                        <option value="radio" <?php if (isset($str) && $str == 'radio') echo 'selected'; ?>><?php _e('Radio Button', 'profile-grid'); ?></option>
                        <option value="textarea" <?php if (isset($str) && $str == 'textarea') echo 'selected'; ?>><?php _e('Text Area', 'profile-grid'); ?></option>
                        <option value="checkbox" <?php if (isset($str) && $str == 'checkbox') echo 'selected'; ?>><?php _e('Check Box', 'profile-grid'); ?></option>
                        <option value="DatePicker" <?php if (isset($str) && $str == 'DatePicker') echo 'selected'; ?>><?php _e('Date', 'profile-grid'); ?></option>
                        <option value="email" <?php if (isset($str) && $str == 'email') echo 'selected'; ?>><?php _e('Email', 'profile-grid'); ?></option>
                        <option value="number" <?php if (isset($str) && $str == 'number') echo 'selected'; ?>><?php _e('Number', 'profile-grid'); ?></option>
                        <option value="country" <?php if (isset($str) && $str == 'country') echo 'selected'; ?>><?php _e('Country', 'profile-grid'); ?></option>
                        <option value="timezone" <?php if (isset($str) && $str == 'timezone') echo 'selected'; ?>><?php _e('Timezone', 'profile-grid'); ?></option>
                        <option value="term_checkbox" <?php if (isset($str) && $str == 'term_checkbox') echo 'selected'; ?>><?php _e('T&C Checkbox', 'profile-grid'); ?></option>
                        <option value="file" <?php if (isset($str) && $str == 'file') echo 'selected'; ?>><?php _e('File Upload', 'profile-grid'); ?></option>
           <!-- <option value="pricing" <?php if (isset($str) && $str == 'pricing') echo 'selected'; ?>><?php _e('Pricing', 'profile-grid'); ?></option> -->
                        <option value="repeatable_text" <?php if (isset($str) && $str == 'repeatable_text') echo 'selected'; ?>><?php _e('Repeatable Text', 'profile-grid'); ?></option>
                        <option value="first_name" <?php if (isset($str) && $str == 'first_name') echo 'selected'; ?>><?php _e('First Name', 'profile-grid'); ?></option>
                        <option value="last_name" <?php if (isset($str) && $str == 'last_name') echo 'selected'; ?>><?php _e('Last Name', 'profile-grid'); ?></option>
                        <option value="user_name" <?php if (isset($str) && $str == 'user_name') echo 'selected'; ?>><?php _e('User Name', 'profile-grid'); ?></option>
                        <?php
                        $check = $pmrequests->pm_check_field_exist($gid, 'user_email', true);
                        if ($check === false || $str == 'user_email'):
                            ?>
                            <option value="user_email" <?php if (isset($str) && $str == 'user_email') echo 'selected'; ?>><?php _e('User Email', 'profile-grid'); ?></option>
                        <?php endif; ?> 
                        <option value="user_url" <?php if (isset($str) && $str == 'user_url') echo 'selected'; ?>><?php _e('Website', 'profile-grid'); ?></option>
                        <option value="user_pass" <?php if (isset($str) && $str == 'user_pass') echo 'selected'; ?>><?php _e('Password', 'profile-grid'); ?></option>
                        <option value="confirm_pass" <?php if (isset($str) && $str == 'confirm_pass') echo 'selected'; ?>><?php _e('Confirm Password', 'profile-grid'); ?></option>
                        <option value="description" <?php if (isset($str) && $str == 'description') echo 'selected'; ?>><?php _e('Biographical Info', 'profile-grid'); ?></option>
                        <option value="user_avatar" <?php if (isset($str) && $str == 'user_avatar') echo 'selected'; ?>><?php _e('Profile Image', 'profile-grid'); ?></option>
                        <option value="mobile_number" <?php if (isset($str) && $str == 'mobile_number') echo 'selected'; ?>><?php _e('Mobile Number', 'profile-grid'); ?></option>
                        <option value="phone_number" <?php if (isset($str) && $str == 'phone_number') echo 'selected'; ?>><?php _e('Phone Number', 'profile-grid'); ?></option>
                        <option value="gender" <?php if (isset($str) && $str == 'gender') echo 'selected'; ?>><?php _e('Gender', 'profile-grid'); ?></option>
                        <option value="language" <?php if (isset($str) && $str == 'language') echo 'selected'; ?>><?php _e('Language', 'profile-grid'); ?></option>
                        <!--<option value="birth_date" <?php if (isset($str) && $str == 'birth_date') echo 'selected'; ?>><?php _e('Birth Date', 'profile-grid'); ?></option> -->
                        <option value="divider" <?php if (isset($str) && $str == 'divider') echo 'selected'; ?>><?php _e('Divider', 'profile-grid'); ?></option>
                        <option value="spacing" <?php if (isset($str) && $str == 'spacing') echo 'selected'; ?>><?php _e('Spacing', 'profile-grid'); ?></option>
                        <option value="multi_dropdown" <?php if (isset($str) && $str == 'multi_dropdown') echo 'selected'; ?>><?php _e('Multi Dropdown', 'profile-grid'); ?></option>
                        <option value="facebook" <?php if (isset($str) && $str == 'facebook') echo 'selected'; ?>><?php _e('Facebook', 'profile-grid'); ?></option>
                        <option value="twitter" <?php if (isset($str) && $str == 'twitter') echo 'selected'; ?>><?php _e('Twitter', 'profile-grid'); ?></option>
                        <option value="google" <?php if (isset($str) && $str == 'google') echo 'selected'; ?>><?php _e('Google+', 'profile-grid'); ?></option>
                        <option value="linked_in" <?php if (isset($str) && $str == 'linked_in') echo 'selected'; ?>><?php _e('Linked In', 'profile-grid'); ?></option>
                        <option value="youtube" <?php if (isset($str) && $str == 'youtube') echo 'selected'; ?>><?php _e('Youtube', 'profile-grid'); ?></option>
                        <option value="instagram" <?php if (isset($str) && $str == 'instagram') echo 'selected'; ?>><?php _e('Instagram', 'profile-grid'); ?></option>
                        <?php do_action('pg_add_field_in_dropdown', $str); ?>
                    </select>
                </div>
                <div class="uimnote"><?php _e('Change the type of this field. Please note, changing a type of an existing field with values may result in data loss. Use this carefully.', 'profile-grid'); ?></div>
            </div>

            <div class="childfieldsrow" id="field_options_wrapper" style=" <?php
            if (isset($str)) {
                echo 'display:block;';
            } else {
                echo 'display:none;';
            }
            ?>">

                <div class="uimrow" id="place_holder_text_html">
                    <div class="uimfield">
<?php _e('Placeholder text:', 'profile-grid'); ?>
                    </div>
                    <div class="uiminput">
                        <input type="text" name="field_options[place_holder_text]" id="place_holder_text" value="<?php if (!empty($field_options)) echo esc_attr($field_options['place_holder_text']); ?>" />
                    </div>
                    <div class="uimnote"><?php _e('Placeholder text appears inside input box as guidlines to the user. It disappears when the user clicks on the input box. For e.g. <i>Enter your age</i> inside a number input box.', 'profile-grid'); ?></div>
                </div>

                <div class="uimrow" id="css_class_attribute_html">
                    <div class="uimfield">
<?php _e('CSS Class Attribute:', 'profile-grid'); ?>
                    </div>
                    <div class="uiminput">
                        <input type="text" name="field_options[css_class_attribute]" id="css_class_attribute" value="<?php if (!empty($field_options)) echo esc_attr($field_options['css_class_attribute']); ?>" />
                    </div>
                    <div class="uimnote"><?php _e('If you know a bit of CSS, you can create and assign a custom class to this field. Just enter the name of the class as it appears in your stylesheet. For e.g. <i>my-input-class</i>', 'profile-grid'); ?></div>
                </div>

                <div class="uimrow" id="maximum_length_html">
                    <div class="uimfield">
<?php _e('Maximum Length:', 'profile-grid'); ?>
                    </div>
                    <div class="uiminput">
                        <input type="number" min="1" name="field_options[maximum_length]" id="maximum_length" value="<?php if (!empty($field_options)) echo esc_attr($field_options['maximum_length']); ?>" />
                    </div>
                    <div class="uimnote"><?php _e('Maximum lenght of the allowed field value in characters (count).', 'profile-grid'); ?></div>
                </div>

                <div class="uimrow" id="default_value_html">
                    <div class="uimfield">
<?php _e('Default Value:', 'profile-grid'); ?>
                    </div>
                    <div class="uiminput">
                        <input type="text" name="field_options[default_value]" id="default_value" value="<?php if (!empty($field_options)) echo esc_attr($field_options['default_value']); ?>" />
                    </div>
                    <div class="uimnote"><?php _e('Default values work for selection boxes where you want a value to be pre-selected when the form loads. You need to enter the value exactly as you have created in the options below.', 'profile-grid'); ?> </div>
                </div>

                <div class="uimrow" id="first_option_html">
                    <div class="uimfield">
<?php _e('First Option Value:', 'profile-grid'); ?>
                    </div>
                    <div class="uiminput">
                        <input type="text" name="field_options[first_option]" id="first_option" value="<?php if (!empty($field_options) && isset($field_options['first_option'])) echo esc_attr($field_options['first_option']); ?>" />
                    </div>
                    <div class="uimnote"><?php _e('Add options for your selection field below. Click on <i>Click to add option</i> to add an extra option to existing ones.', 'profile-grid'); ?></div>
                </div>

                <div class="uimrow" id="field_options_html">
                    <div class="uimfield">
<?php _e('Options:', 'profile-grid'); ?>
                    </div>
                    <div class="uiminput">
                        <textarea name="field_options[dropdown_option_value]" id="field_options"><?php if (!empty($field_options)) echo esc_attr($field_options['dropdown_option_value']); ?></textarea>
                        <div class="errortext"></div>
                    </div>
                    <div class="uimnote"><?php _e('Options for drop down list. Separate multiple values with a comma(,).', 'profile-grid'); ?></div>
                </div>

                <div class="uimrow" id="field_options_radio_html">
                    <div class="uimfield">
<?php _e('Options:', 'profile-grid'); ?>
                    </div>
                    <div class="uiminput">
                        <ul class="uimradio" id="radio_option_ul_li_field">
                            <?php
                            if (!empty($field_options) && !empty($field_options['radio_option_value'])) {

                                foreach ($field_options['radio_option_value'] as $optionvalue) {
                                    if ($optionvalue == 'chl_other')
                                        continue;
                                    ?>
                                    <li class="pm_radio_option_field">
                                        <span class="pm_handle"></span>
                                        <input type="text" name="field_options[radio_option_value][]" value="<?php if (!empty($optionvalue)) echo esc_attr($optionvalue); ?>">
                                        <span class="pm_remove_field" onClick="remove_pm_radio_option(this)">Delete</span>
                                    </li>
                                    <?php
                                }
                            }
                            else {
                                ?>
                                <li class="pm_radio_option_field">
                                    <span class="pm_handle"></span>
                                    <input type="text" name="field_options[radio_option_value][]" value="<?php if (!empty($optionvalue)) echo esc_attr($optionvalue); ?>">
                                    <span class="pm_remove_field" onClick="remove_pm_radio_option(this)">Delete</span>
                                </li>
                                <?php
                            }
                            ?>
                        </ul>

                        <ul class="uimradio" id="pm_radio_field_other_option_html">
                            <li><input type="text" value="" placeholder="Click to add option" class="pm_click_add_option" maxlength="0" onClick="add_pm_radio_option()" onKeyUp="add_pm_radio_option()"></li>
                            <?php if (!empty($field_options) && !empty($field_options['radio_option_value']) && in_array('chl_other', $field_options['radio_option_value'])): ?> 
                                <li class="pm_radio_option_field" style=" margin-top:12px;"><input type="text" name="optionvalue[]" id="optionvalue[]" value="Their answer" disabled><span class="removefield" onClick="remove_pm_radio_option(this)">Delete</span><input type="hidden" name="field_options[radio_option_value][]" id="field_options[radio_option_value][]" value="chl_other" /></li>
                            <?php else: ?>
                                <li class="pm_add_other_button" onClick="add_pm_other_option()"> or Add "Other"</li>
<?php endif; ?>
                        </ul>

                        <div class="errortext"></div>
                    </div>
                    <div class="uimnote"><?php _e('Add options for your selection field below. Click on <i>Click to add option</i> to add an extra option to existing ones.', 'profile-grid'); ?></div>
                </div>

                <div class="uimrow" id="paragraph_text_html">
                    <div class="uimfield">
<?php _e('Paragraph Text:', 'profile-grid'); ?>
                    </div>
                    <div class="uiminput">
                        <textarea name="field_options[paragraph_text]" id="paragraph_text"><?php if (!empty($field_options)) echo esc_attr($field_options['paragraph_text']); ?></textarea>
                    </div>
                    <div class="uimnote"><?php _e('The text you want to appear in this field. HTML is supported. Which means you can use HTML tags like <i>b,i, u, em, strong, h1, h2...</i> etc. to style your content.', 'profile-grid'); ?> </div>
                </div>

                <div class="uimrow" id="columns_html">
                    <div class="uimfield">
<?php _e('Columns:', 'profile-grid'); ?>
                    </div>
                    <div class="uiminput">
                        <input type="number" min="2" name="field_options[columns]" id="columns" value="<?php if (!empty($field_options)) echo esc_attr($field_options['columns']); ?>" />
                    </div>
                    <div class="uimnote"><?php _e('Number of columns (equavalent to character count) allowed in this field. Leave blank for no limit.', 'profile-grid'); ?></div>
                </div>

                <div class="uimrow" id="rows_html">
                    <div class="uimfield">
<?php _e('Rows:', 'profile-grid'); ?>
                    </div>
                    <div class="uiminput">
                        <input type="number" min="2" name="field_options[rows]" id="rows" value="<?php if (!empty($field_options)) echo esc_attr($field_options['rows']); ?>" />
                    </div>
                    <div class="uimnote"><?php _e('Number of rows (of text) allowed in this field. Leave blank for no limit.', 'profile-grid'); ?></div>
                </div>

                <div class="uimrow" id="term_and_condition_html">
                    <div class="uimfield">
<?php _e('Terms & Conditions:', 'profile-grid'); ?>
                    </div>
                    <div class="uiminput">
                        <textarea name="field_options[term_and_condition]" id="term_and_condition"><?php if (!empty($field_options)) echo esc_attr($field_options['term_and_condition']); ?></textarea>
                    </div>
                    <div class="uimnote"><?php _e('Paste the contents of your Terms and Conditions here. Users will be able to scroll through it and read in full before accepting them.', 'profile-grid'); ?></div>
                </div>

                <div class="uimrow" id="allowed_file_types_html">
                    <div class="uimfield">
<?php _e('Define allowed file types (file extensions):', 'profile-grid'); ?>
                    </div>
                    <div class="uiminput">
                        <textarea name="field_options[allowed_file_types]" id="allowed_file_types"><?php if (!empty($field_options)) echo esc_attr($field_options['allowed_file_types']); ?></textarea>
                    </div>
                    <div class="uimnote"><?php _e('Separate multiple values by â€œ|â€?. For example PDF|JPEG|XLS', 'profile-grid'); ?> </div>
                </div>

                <div class="uimrow" id="heading_text_html">
                    <div class="uimfield">
<?php _e('Heading Text:', 'profile-grid'); ?>
                    </div>
                    <div class="uiminput">
                        <input type="text" name="field_options[heading_text]" id="heading_text" value="<?php if (!empty($field_options)) echo esc_attr($field_options['heading_text']); ?>" />
                    </div>
                    <div class="uimnote"><?php _e('Text inside your heading. Remember, headings have large font sizes, therefore a very long text may appear odd. Best used to grab attention for something important. Combine it with <i>Paragraph</i> field type to add extra content.', 'profile-grid'); ?></div>
                </div>

                <div class="uimrow" id="heading_tag_html">
                    <div class="uimfield">
<?php _e('Heading Tag:', 'profile-grid'); ?>
                    </div>
                    <div class="uiminput">
                        <select name="field_options[heading_tag]" id="heading_tag">
                            <option value="h1" <?php if (!empty($field_options)) selected($field_options['heading_tag'], 'h1'); ?>><?php _e('Heading 1', 'profile-grid'); ?></option>
                            <option value="h2" <?php if (!empty($field_options)) selected($field_options['heading_tag'], 'h2'); ?>><?php _e('Heading 2', 'profile-grid'); ?></option>
                            <option value="h3" <?php if (!empty($field_options)) selected($field_options['heading_tag'], 'h3'); ?>><?php _e('Heading 3', 'profile-grid'); ?></option>
                            <option value="h4" <?php if (!empty($field_options)) selected($field_options['heading_tag'], 'h4'); ?>><?php _e('Heading 4', 'profile-grid'); ?></option>
                            <option value="h5" <?php if (!empty($field_options)) selected($field_options['heading_tag'], 'h5'); ?>><?php _e('Heading 5', 'profile-grid'); ?></option>
                            <option value="h6" <?php if (!empty($field_options)) selected($field_options['heading_tag'], 'h6'); ?>><?php _e('Heading 6', 'profile-grid'); ?></option>
                        </select>
                    </div>
                    <div class="uimnote"><?php _e('Text size decreases from H2 to H6. There maybe additional style applied to the heading text based on your theme CSS.', 'profile-grid'); ?></div>
                </div>

                <div class="uimrow" id="price_html">
                    <div class="uimfield">
<?php _e('Price:', 'profile-grid'); ?>
                    </div>
                    <div class="uiminput">
                        <input type="number" min="0" name="field_options[price]" id="price" value="<?php if (!empty($field_options)) echo esc_attr($field_options['price']); ?>" />
                    </div>
                    <div class="uimnote"><?php _e('Price - Obviously, only numbers accepted. A read only field.', 'profile-grid'); ?></div>
                </div>



            </div>

            <div class="uimrow" id="field_icon_div">
                <div class="uimfield">
<?php _e('Field Icon', 'profile-grid'); ?>
                </div>
                <div class="uiminput" id="icon_html">
                    <input id="field_icon" type="hidden" name="field_icon" class="icon_id" value="<?php if (!empty($row)) echo esc_attr($row->field_icon); ?>" />
                    <input id="field_icon_button" name="field_icon_button" class="button group_icon_button" type="button" value="Upload Icon" />
                    <?php
                    if (!empty($row) && $row->field_icon != 0) {
                        echo wp_get_attachment_link($row->field_icon, array(50, 50), false, true, false);
                    }
                    ?>
                    <img src="" width="50px" id="group_icon_img" style="display:none;" />
                    <?php
                    if (!empty($row) && $row->field_icon != 0) {
                        echo '<input type="button" name="remove_group_icon" id="remove_group_icon" class="remove_icon" value="Remove Icon" />';
                    }
                    ?>

                    <div class="errortext" id="icon_error"></div>
                </div>
                <div class="uimnote"> <?php _e('Jazz up your forms with field icons &#128247;. Icons appear at the beginning of the label. For best results use a square image. For e.g. <i>16px x 16px, 256px x 256px, 512px x 512px</i>...', 'profile-grid'); ?></div>
            </div>

            <div class="uimrow">
                <div class="uimfield">
<?php _e('Associate with Group', 'profile-grid'); ?>
                </div>
                <div class="uiminput pm_select_required">
                    <select name="associate_group" id="associate_group" onchange="pm_ajax_sections_dropdown(this.value)">
                        <option value="">Select A Group</option>
                        <?php
                        foreach ($groups as $group) {
                            ?>
                            <option value="<?php echo $group->id; ?>" <?php if (!empty($gid)) selected($gid, $group->id); ?>><?php echo $group->group_name; ?></option>
<?php }
?>
                    </select>
                    <div class="errortext"></div>
                </div>
                <div class="uimnote"><?php _e('Move this field to a different group. Use carefully, since this will remove the field from current group.', 'profile-grid'); ?></div>
            </div>

            <div class="uimrow">
                <div class="uimfield">
<?php _e('Associate with Section', 'profile-grid'); ?>
                </div>
                <div class="uiminput pm_select_required">
                    <select name="associate_section" id="associate_section">
                        <?php
                        foreach ($sections as $section) {
                            ?>
                            <option value="<?php echo $section->id; ?>" <?php if (!empty($row)) selected($row->associate_section, $section->id); ?>><?php echo $section->section_name; ?></option>
<?php }
?>
                    </select>
                    <div class="errortext"></div>
                </div>
                <div class="uimnote"><?php _e('If you have multiple Profile sections in this group, you can move the field to a different section here. By default, each new field is added to the first section. For e.g. moving a date field labelled <i>Date of Birth</i> from <i>Career</i> section to <i>About</i> section.', 'profile-grid'); ?></div>
            </div>


            <div class="uimrow" id="show_signup">
                <div class="uimfield">
<?php _e('Show in Sign-Up form', 'profile-grid'); ?>
                </div>
                <div class="uiminput">
                    <input name="show_in_signup_form" id="show_in_signup_form" type="checkbox"  class="pm_toggle" value="1" <?php
                    if (!empty($row)) {
                        checked($row->show_in_signup_form, 1);
                    } else {
                        echo 'checked';
                    }
                    ?> style="display:none;" onClick="pm_show_hide(this, 'signup_html')" />
                    <label for="show_in_signup_form"></label>
                </div>
                <div class="uimnote"><?php _e('The field will appear in the registration form when members sign up for this group. Alternatively, you can hide from the form, and users can then fill it up when editing their profiles later on.', 'profile-grid'); ?></div>
            </div>
            <div class="childfieldsrow" id="signup_html" style=" <?php
            if (!empty($row)) {
                if ($row->show_in_signup_form == '1') {
                    echo 'display:block;';
                } else {
                    echo 'display:none;';
                }
            } else {
                echo 'display:block;';
            }
            ?>">
                <div class="uimrow">
                    <div class="uimfield">
<?php _e('Required?', 'profile-grid'); ?>
                    </div>
                    <div class="uiminput">
                        <input name="is_required" id="is_required" type="checkbox"  class="pm_toggle" value="1" <?php if (!empty($row)) checked($row->is_required, 1); ?> style="display:none;" />
                        <label for="is_required"></label>
                    </div>
                    <div class="uimnote"> <?php _e('Make this field mandatory. Users will receive an error if they try to submit the form without filling up this field.', 'profile-grid'); ?></div>
                </div>
                <?php /* ?><div class="uimrow">
                  <div class="uimfield">
                  <?php _e( 'Can it be edited later?', 'profile-grid' ); ?>
                  </div>
                  <div class="uiminput">
                  <input name="is_editable" id="is_editable" type="checkbox"  class="pm_toggle" value="1" <?php if(!empty($row))checked($row->is_editable,1);?> style="display:none;" />
                  <label for="is_editable"></label>
                  </div>
                  <div class="uimnote"> For your reference only. Not visible on front-end. Description can help you remember the purpose of the form. </div>
                  </div><?php */ ?>
            </div>

            <div class="uimrow" id="displayonprofile">
                <div class="uimfield">
<?php _e('Display on Profile Page', 'profile-grid'); ?>
                </div>
                <div class="uiminput">
                    <input name="display_on_profile" id="display_on_profile" type="checkbox"  class="pm_toggle" value="1" <?php if (!empty($row)) checked($row->display_on_profile, 1); ?> style="display:none;"  onClick="pm_show_hide(this, 'displayprofilehtml')" />
                    <label for="display_on_profile"></label>
                </div>
                <div class="uimnote"><?php _e('Show this field on profile page of the members. It is totally possible to show a field in group registration form but hide from the profile page.', 'profile-grid'); ?></div>
            </div>
            <div class="childfieldsrow" id="displayprofilehtml" style=" <?php
                        if (!empty($row) && $row->display_on_profile == '1') {
                            echo 'display:block;';
                        } else {
                            echo 'display:none;';
                        }
                        ?>">
                <div class="uimrow">
                    <div class="uimfield">
<?php _e('Display on Group Page', 'profile-grid'); ?>
                    </div>
                    <div class="uiminput">
                        <input name="display_on_group" id="display_on_group" type="checkbox"  class="pm_toggle" value="1" <?php if (!empty($row)) checked($row->display_on_group, 1); ?> style="display:none;" />
                        <label for="display_on_group"></label>
                    </div>
                    <div class="uimnote"><?php _e('Group pages show snippet of member profiles. While user profile and cover images appear there by default, you can show specific fields in these snippets too. Also adding too many large fields may not look very pretty. Try to strike a balance between infromation and structure.', 'profile-grid'); ?></div>
                </div>

                <div class="uimrow" id="field_visibility_option">
                    <div class="uimfield">
                                <?php _e('Visible for:', 'profile-grid'); ?>
                    </div>
                    <div class="uiminput <?php
                                if (!empty($row) && $row->display_on_profile == '1') {
                                    echo 'pm_radio_required';
                                }
                                ?>">
                        <ul class="uimradio">
                            <li>
                                <input type="radio" name="visibility" id="visibility" value="1"  <?php if (!empty($row)) checked($row->visibility, 1); ?>>
<?php _e('Public', 'profile-grid'); ?>
                            </li>
                            <li>
                                <input type="radio" name="visibility" id="visibility" value="2"  <?php if (!empty($row)) checked($row->visibility, 2); ?>>
<?php _e('Registered', 'profile-grid'); ?>
                            </li>
                            <li>
                                <input type="radio" name="visibility" id="visibility" value="3"  <?php if (!empty($row)) checked($row->visibility, 3); ?>>
                    <?php _e('Only Group Leader', 'profile-grid'); ?>
                            </li>

                        </ul>
                        <div class="errortext"></div>
                    </div>
                    <div class="uimnote"><?php _e('Set visibility of the profile field.', 'profile-grid'); ?></div>
                </div>
            </div>
            <div class="uimrow" id="displayonsearch">
                <div class="uimfield">
                    <?php _e('Display in Advance Search', 'profile-grid'); ?>
                </div>
                <div class="uiminput">
                    <input name="field_options[display_on_search]" id="display_on_search" type="checkbox"  class="pm_toggle" value="1" <?php if (!empty($field_options['display_on_search'])) checked($field_options['display_on_search'], 1); ?> style="display:none;" />
                    <label for="display_on_search"></label>
                </div>
                <div class="uimnote"><?php _e('This will display the field in advance search above the members directory on front end. Selecting it during search will allow users to restrict keyword search to selected field(s).', 'profile-grid'); ?></div>
            </div>

            <div class="uimrow" id="dateofbirth">
                <div class="uimfield">
<?php _e('range of year dropdown', 'profile-grid'); ?>
                </div>
                <div class="uiminput">
                    <input name="field_options[set_dob_range]" id="set_dob_range" type="checkbox"  class="pm_toggle" value="1" <?php if (!empty($field_options['set_dob_range'])) checked($field_options['set_dob_range'], 1); ?> style="display:none;" onClick="pm_show_hide(this, 'dateofbirth_range')" />
                    <label for="set_dob_range"></label>
                </div>
                <div class="uimnote"><?php _e('This will Enable this to force selection of date of birth from a certain range.', 'profile-grid'); ?></div>



            </div>
            <div class="childfieldsrow" id="dateofbirth_range" style=" <?php
            if (!empty($row) && !empty($field_options['set_dob_range']) && $field_options['set_dob_range'] == 1) {
                echo 'display:block;';
            } else {
                echo 'display:none;';
            }
?>">
                <div class="uimrow">
                    <div class="uimrow">
                        <div class="uimfield">
<?php _e('Start Year', 'profile-grid'); ?>
                        </div>
                        <div class="uiminput">
                            <input type="text" class="pm_calendar" value="<?php if (!empty($field_options['max_dob'])) echo $field_options['max_dob']; ?>" id="max_dob" name="field_options[max_dob]" />
                            <label for="set_dob_max_range"></label>
                        </div>
                        <div class="uimnote"><?php _e('Maximum date of for the field.', 'profile-grid'); ?></div>          
                    </div>
                    <div class="uimrow">
                        <div class="uimfield">
<?php _e('End Year', 'profile-grid'); ?>
                        </div>
                        <div class="uiminput">
                            <input type="text" class="pm_calendar" value="<?php if (!empty($field_options['min_dob'])) echo $field_options['min_dob']; ?>" id="min_dob" name="field_options[min_dob]" />
                            <label for="set_dob_min_range"></label>
                        </div>
                        <div class="uimnote"><?php _e('Minimum date of for the field.', 'profile-grid'); ?></div>          
                    </div>
                </div>
            </div>


            <div class="uimrow" id="address_pane">

                <div class="uimfield">
<?php _e('Allow fields', 'profile-grid'); ?>
                </div>
                <div class="uiminput">
                    <ul class="uimradio">
                        <li>  
                            <input type="checkbox" name="field_options[address_line_1]" value="1" <?php if (!empty($field_options['address_line_1'])) echo 'checked'; ?>>Address line 1 
                        </li>
                        <li>
                            <input type="checkbox" name="field_options[address_line_2]" value="1" <?php if (!empty($field_options['address_line_2'])) echo 'checked'; ?>> Address line 2
                        </li>
                        <li>
                            <input type="checkbox" name="field_options[city]" value="1" <?php if (!empty($field_options['city'])) echo 'checked'; ?>> City   
                        </li>
                        <li>
                            <input type="checkbox" name="field_options[state]" value="1" <?php if (!empty($field_options['state'])) echo 'checked'; ?>> State
                        </li>
                         <li>
                            <input type="checkbox" name="field_options[country]" value="1" <?php if (!empty($field_options['country'])) echo 'checked'; ?>> Country
                        </li> 
                        <li>
                            <input type="checkbox" name="field_options[zip_code]" value="1" <?php if (!empty($field_options['zip_code'])) echo 'checked'; ?>> Zip Code
                        </li>
                    </ul>
                    <label for="address"></label>
                    <div class="errortext"></div>
                </div>
                <div class="uimnote"><?php _e('Select address fields which you want to show on frontend.', 'profile-grid'); ?></div>          

            </div>


<?php
if (!empty($row)) {
    $sectionname = $dbhandler->get_value('SECTION', 'section_name', $row->associate_section);
    $cancelurl = 'admin.php?page=pm_profile_fields&gid=' . $gid . '#' . sanitize_key($sectionname);
} else {
    $cancelurl = 'admin.php?page=pm_profile_fields&gid=' . $gid;
}
?>
            <div class="buttonarea"> <a href="<?php echo $cancelurl; ?>">
                    <div class="cancel">&#8592; &nbsp;
<?php _e('Cancel', 'profile-grid'); ?>
                    </div>
                </a>
                <input type="hidden" name="field_id" id="field_id" value="<?php echo $id; ?>" />
                <input type="hidden" name="field_key" id="field_key" value="<?php if (!empty($row)) echo esc_attr($row->field_key); ?>" />
                <input type="hidden" name="ordering" id="ordering" value="<?php echo $ordering; ?>" />
<?php wp_nonce_field('save_pm_add_field'); ?>
                <input type="submit" value="<?php _e('Save', 'profile-grid'); ?>" name="submit_field" id="submit_field" onClick="return add_field_validation()"  />
                <div class="all_error_text" style="display:none;"></div>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript">
    pm_show_hide_field_option('<?php echo $str; ?>', 'field_options_wrapper');
</script>