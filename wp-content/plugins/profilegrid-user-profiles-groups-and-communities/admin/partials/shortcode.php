<?php
$path =  plugin_dir_url(__FILE__);
$textdomain = $this->profile_magic;
?>
    <div class="pmagic">
    <div class="pg-scblock pg-scbg">
        <div class="pg-scblock pg-scpagetitle">
            <img src="<?php echo $path;?>images/pg-icon.png">
            <b><?php _e("ProfileGrid",'profile-grid');?></b> <span class="pg-blue"><?php _e("Shortcodes",'profile-grid');?></span></div> 
  
        <div class="pg-scsubblock">
            <div class="pg-scblock pg-sctitle"><?php _e("Registration Form as a Single Page",'profile-grid');?></div>
            <div class="pg-scblock"><span class="pg-code">[PM_Registration ID="x"]</span></div>
            <div class="pg-scblock"><img class="pg-scimg" src="<?php echo $path;?>images/sc-1.jpg"></div>
            <div class="pg-scblock pg-scdesc"><?php _e("Displays sign up form for a group as a single page. Sections will be separated into separate blocks. Replace <i>x</i> with the Group ID.",'profile-grid');?></div>
            </div>
        <div class="pg-scsubblock">
            <div class="pg-scblock pg-sctitle"><?php _e("Registration Form as Multi-Page",'profile-grid');?></div>
            <div class="pg-scblock"><span class="pg-code">[PM_Registration type="multipage" ID="x"]</span></div>
            <div class="pg-scblock"><img class="pg-scimg" src="<?php echo $path;?>images/sc-2.jpg"></div>
            <div class="pg-scblock pg-scdesc"><?php _e("Displays sign up form for a group as a multi-page. Sections will be separated into pages. Replace x with the Group ID.",'profile-grid');?></div>
            </div>
        <div class="pg-scsubblock">
            <div class="pg-scblock pg-sctitle"><?php _e("Single Group Page",'profile-grid');?></div>
            <div class="pg-scblock"><span class="pg-code">[PM_Group ID="x"]</span></div>
            <div class="pg-scblock"><img class="pg-scimg" src="<?php echo $path;?>images/sc-3.jpg"></div>
            <div class="pg-scblock pg-scdesc"><?php _e("Displays a Group with logo and description. Groups users are displayed below the Group Card. Replace x with the Group ID.",'profile-grid');?></div>
            </div>
   
       
        
   
        <div class="pg-scsubblock">
            <div class="pg-scblock pg-sctitle"><?php _e("Multi Group Page",'profile-grid');?></div>
            <div class="pg-scblock"><span class="pg-code">[PM_Groups]</span></div>
            <div class="pg-scblock"><img class="pg-scimg" src="<?php echo $path;?>images/sc-4.jpg"></div>
            <div class="pg-scblock pg-scdesc"><?php _e("Displays all the Groups with logo, description and Sign Up buttons. Visitors can choose a Group to join.",'profile-grid');?></div>
            </div>
        <div class="pg-scsubblock">
            <div class="pg-scblock pg-sctitle"><?php _e("Profile Page",'profile-grid');?></div>
            <div class="pg-scblock"><span class="pg-code">[PM_Profile]</span></div>
            <div class="pg-scblock"><img class="pg-scimg" src="<?php echo $path;?>images/sc-5.jpg"></div>
            <div class="pg-scblock pg-scdesc"><?php _e("Single profile page used for displaying logged in user's profile.",'profile-grid');?></div>
            </div>
        <div class="pg-scsubblock">
            <div class="pg-scblock pg-sctitle"><?php _e("Login Form",'profile-grid');?></div>
            <div class="pg-scblock"><span class="pg-code">[PM_Login]</span></div>
            <div class="pg-scblock"><img class="pg-scimg" src="<?php echo $path;?>images/sc-6.jpg"></div>
            <div class="pg-scblock pg-scdesc"><?php _e("A login form with Username/ Email and Password fields. Also has Forgot Password link.",'profile-grid');?></div>
            </div>
   
        

        <div class="pg-scsubblock">
            <div class="pg-scblock pg-sctitle"><?php _e("Password Retrieval Form",'profile-grid');?></div>
            <div class="pg-scblock"><span class="pg-code">[PM_Forget_Password]</span></div>
            <div class="pg-scblock"><img class="pg-scimg" src="<?php echo $path;?>images/sc-7.jpg"></div>
            <div class="pg-scblock pg-scdesc"><?php _e("A page where users can enter their email to reset their lost password.",'profile-grid');?></div>
            </div>
        <div class="pg-scsubblock">
            <div class="pg-scblock pg-sctitle"><?php _e("Password Reset Form",'profile-grid');?></div>
            <div class="pg-scblock"><span class="pg-code">[PM_Password_Reset_Form]</span></div>
            <div class="pg-scblock"><img class="pg-scimg" src="<?php echo $path;?>images/sc-8.jpg"></div>
            <div class="pg-scblock pg-scdesc"><?php _e("The page where users arrive after clicking on password reset link sent to them after using Password Retrieval Form.",'profile-grid');?></div>
            </div>
        <div class="pg-scsubblock">
            <div class="pg-scblock pg-sctitle"><?php _e("All Users Page",'profile-grid');?></div>
            <div class="pg-scblock"><span class="pg-code">[PM_Search]</span></div>
            <div class="pg-scblock"><img class="pg-scimg" src="<?php echo $path;?>images/sc-9.jpg"></div>
            <div class="pg-scblock pg-scdesc"><?php _e("Shows all users with profile image and username on a single page with search capabilities.",'profile-grid');?></div>
            </div>
      
   
            <div class="pg-scsubblock">
            <div class="pg-scblock pg-sctitle"><?php _e("Blog Submission Page",'profile-grid');?></div>
            <div class="pg-scblock"><span class="pg-code">[PM_Add_Blog]</span></div>
            <div class="pg-scblock"><img class="pg-scimg" src="<?php echo $path;?>images/sc-10.jpg"></div>
            <div class="pg-scblock pg-scdesc"><?php _e("Allows users to post blogs if User Blogs are turned on. Blogs will be visible on respective profile pages.",'profile-grid');?></div>
            </div>
   
        
        <?php do_action('profilegrid_shortcode_desc');?>
        </div>
    </div>

