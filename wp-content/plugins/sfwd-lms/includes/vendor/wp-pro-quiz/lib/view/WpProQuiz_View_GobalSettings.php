<?php
class WpProQuiz_View_GobalSettings extends WpProQuiz_View_View {
	
	public function show() {
?>		
<div class="wrap wpProQuiz_globalSettings">
	<h2 style="margin-bottom: 10px;"><?php echo sprintf( _x('%s Options', 'Quiz Options', 'wp-pro-quiz'), LearnDash_Custom_Label::get_label( 'quiz' ) ); ?></h2>
	
	<a class="button-secondary" style="display:none" href="admin.php?page=ldAdvQuiz"><?php _e('back to overview', 'wp-pro-quiz'); ?></a>
	
	<div class="wpProQuiz_tab_wrapper" style="padding: 10px 0px;">
		<a class="button-primary" href="#" data-tab="#globalContent"><?php echo sprintf( _x('%s Options', 'Quiz Options', 'wp-pro-quiz'), LearnDash_Custom_Label::get_label( 'quiz' ) ); ?></a>
		<a class="button-secondary" href="#" data-tab="#emailSettingsTab"><?php _e('E-Mail settings', 'wp-pro-quiz'); ?></a>
		<a class="button-secondary" href="#" data-tab="#problemContent"><?php _e('Settings in case of problems', 'wp-pro-quiz'); ?></a>
	</div>
	
	<form method="post">
		<div id="poststuff">
			<div id="globalContent">
				
				<?php $this->globalSettings(); ?>
				
			</div>
			<div id="emailSettingsTab" style="display: none;">
				<?php $this->emailSettingsTab(); ?>
			</div>
			<div class="postbox" id="problemContent" style="display: none;">
				<?php $this->problemSettings(); ?>
			</div>
			<input type="submit" name="submit" class="button-primary" id="wpProQuiz_save" value="<?php _e('Save', 'wp-pro-quiz'); ?>">
		</div>
	</form>
</div>
		
<?php 	
	}
	
	private function globalSettings() {

?>
		<div class="postbox">
			<h3 class="hndle"><?php _e('Global settings', 'wp-pro-quiz'); ?></h3>
			<div class="wrap">
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row">
								<?php _e('Leaderboard time format', 'wp-pro-quiz'); ?>
							</th>
							<td>
								<fieldset>
									<legend class="screen-reader-text">
										<span><?php _e('Leaderboard time format', 'wp-pro-quiz'); ?></span>
									</legend>
									<label>
										<input type="radio" name="toplist_date_format" value="d.m.Y H:i" <?php $this->checked($this->toplistDataFormat, 'd.m.Y H:i'); ?>> 06.11.2010 12:50
									</label> <br>
									<label>
										<input type="radio" name="toplist_date_format" value="Y/m/d g:i A" <?php $this->checked($this->toplistDataFormat, 'Y/m/d g:i A'); ?>> 2010/11/06 12:50 AM
									</label> <br>
									<label>
										<input type="radio" name="toplist_date_format" value="Y/m/d \a\t g:i A" <?php $this->checked($this->toplistDataFormat, 'Y/m/d \a\t g:i A'); ?>> 2010/11/06 at 12:50 AM
									</label> <br>
									<label>
										<input type="radio" name="toplist_date_format" value="Y/m/d \a\t g:ia" <?php $this->checked($this->toplistDataFormat, 'Y/m/d \a\t g:ia'); ?>> 2010/11/06 at 12:50am
									</label> <br>
									<label>
										<input type="radio" name="toplist_date_format" value="F j, Y g:i a" <?php $this->checked($this->toplistDataFormat, 'F j, Y g:i a'); ?>> November 6, 2010 12:50 am
									</label> <br>
									<label>
										<input type="radio" name="toplist_date_format" value="M j, Y @ G:i" <?php $this->checked($this->toplistDataFormat, 'M j, Y @ G:i'); ?>> Nov 6, 2010 @ 0:50
									</label> <br>
									<label>
										<input type="radio" name="toplist_date_format" value="custom" <?php echo in_array($this->toplistDataFormat, array('d.m.Y H:i', 'Y/m/d g:i A', 'Y/m/d \a\t g:i A', 'Y/m/d \a\t g:ia', 'F j, Y g:i a', 'M j, Y @ G:i')) ? '' : 'checked="checked"'; ?> >
										<?php _e('Custom', 'wp-pro-quiz'); ?>:
										<input class="medium-text" name="toplist_date_format_custom" style="width: 100px;" value="<?php echo $this->toplistDataFormat; ?>">
									</label>
									<p>
										<a href="http://codex.wordpress.org/Formatting_Date_and_Time" target="_blank"><?php _e('Documentation on date and time formatting', 'wp-pro-quiz'); ?></a>
									</p>
								</fieldset>
							</td>
						</tr>
						
						<tr>
							<th scope="row">
								<?php _e('Statistic time format', 'wp-pro-quiz'); ?>
							</th>
							<td>
								<fieldset>
									<legend class="screen-reader-text">
										<span><?php _e('Statistic time format', 'wp-pro-quiz'); ?></span>
									</legend>
									
									<label>
										<?php _e('Select example:', 'wp-pro-quiz'); ?>
										<select id="statistic_time_format_select">
											<option value="0"></option>
											<option value="d.m.Y H:i"> 06.11.2010 12:50</option>
											<option value="Y/m/d g:i A"> 2010/11/06 12:50 AM</option>
											<option value="Y/m/d \a\t g:i A"> 2010/11/06 at 12:50 AM</option>
											<option value="Y/m/d \a\t g:ia"> 2010/11/06 at 12:50am</option>
											<option value="F j, Y g:i a"> November 6, 2010 12:50 am</option>
											<option value="M j, Y @ G:i"> Nov 6, 2010 @ 0:50</option>
										</select>
									</label>
									<div style="margin-top: 10px;">
										<label>
											<?php _e('Time format:', 'wp-pro-quiz'); ?>:
											<input class="medium-text" name="statisticTimeFormat" value="<?php echo $this->statisticTimeFormat; ?>">
										</label>
										<p>
											<a href="http://codex.wordpress.org/Formatting_Date_and_Time" target="_blank"><?php _e('Documentation on date and time formatting', 'wp-pro-quiz'); ?></a>
										</p>
									</div>
								</fieldset>
							</td>
						</tr>
						<?php if (count($this->category)) { ?>
						<tr>
							<th scope="row">
								<?php _e('Question Category management', 'wp-pro-quiz'); ?>
							</th>
							<td>
								<fieldset>
									<legend class="screen-reader-text">
										<span><?php _e('Question Category management', 'wp-pro-quiz'); ?></span>
									</legend>
									<select name="category">
										<option value=""><?php _e('Select Question Category', 'wp-pro-quiz'); ?></option>
										<?php foreach($this->category as $cat) { 
											echo '<option value="'.$cat->getCategoryId().'">'.$cat->getCategoryName().'</option>';
										} ?>
									</select>
									<div style="padding-top: 5px;">
										<input type="text" value="" name="categoryEditText" class="regular-text" />
									</div>
									<div style="padding-top: 5px;">
										<input type="button" title="<?php _e('Delete selected Question Category', 'wp-pro-quiz') ?>" value="<?php _e('Delete', 'wp-pro-quiz'); ?>" name="categoryDelete" class="button-secondary">
										<input type="button" title="<?php _e('Save changed to selected Question Category', 'wp-pro-quiz') ?>" value="<?php _e('Save Changes', 'wp-pro-quiz'); ?>" name="categoryEdit" class="button-secondary">
										<div class="categorySpinner spinner"></div>
										<span class="categoryEditUpdate" style="display:none"><?php _e('Question Category Saved', 'wp-pro-quiz') ?></span>
										<span class="categoryDeleteUpdate" style="display:none"><?php _e('Question Category Deleted', 'wp-pro-quiz') ?></span>
									</div>
								</fieldset>
							</td>
						</tr>
						<?php } ?>
						<?php if (count($this->templateQuiz)) { ?>
						<tr>
							<th scope="row">
								<?php echo sprintf( _x('%s template management', 'Quiz template management', 'wp-pro-quiz'), LearnDash_Custom_Label::get_label( 'quiz' )); ?>
							</th>
							<td>
								<fieldset>
									<legend class="screen-reader-text">
										<span><?php echo sprintf( _x('%s template management', 'Quiz template management', 'wp-pro-quiz'), LearnDash_Custom_Label::get_label( 'quiz' )); ?></span>
									</legend>
									<select name="templateQuiz">
										<option value=""><?php echo sprintf( _x('Select %s template', 'Select Quiz template', 'wp-pro-quiz'), LearnDash_Custom_Label::get_label( 'quiz' )); ?></option>
										<?php foreach($this->templateQuiz as $templateQuiz) { 
											echo '<option value="'.$templateQuiz->getTemplateId().'">'.esc_html($templateQuiz->getName()).'</option>';
											
										} ?>
									</select>
									<div style="padding-top: 5px;">
										<input type="text" value="" name="templateQuizEditText" class="regular-text" />
									</div>
									<div style="padding-top: 5px;">
										<input type="button" title="<?php echo sprintf( _x('Delete selected %s template', 'Delete selected Quiz template', 'wp-pro-quiz'), LearnDash_Custom_Label::get_label( 'quiz' )) ?>" value="<?php _e('Delete', 'wp-pro-quiz'); ?>" name="templateQuizDelete" class="button-secondary">
										<input type="button" title="<?php echo sprintf( _x('Save changed to selected %s template', 'Save changed to selected Quiz template', 'wp-pro-quiz'), LearnDash_Custom_Label::get_label( 'quiz' ) ) ?>" value="<?php _e('Save Changes', 'wp-pro-quiz'); ?>" name="templateQuizEdit" class="button-secondary">
										<div class="templateQuizSpinner spinner"></div>
										<span class="templateQuizEditUpdate" style="display:none"><?php echo sprintf( _x('%s template Saved', 'Quiz template Saved', 'wp-pro-quiz'), LearnDash_Custom_Label::get_label( 'quiz' ) ) ?></span>
										<span class="templateQuizDeleteUpdate" style="display:none"><?php echo sprintf( _x('%s template Deleted', 'Quiz template Deleted', 'wp-pro-quiz'), LearnDash_Custom_Label::get_label( 'quiz' ) ) ?></span>
										
									</div>
								</fieldset>
							</td>
						</tr>
						<?php } ?>
						<?php if (count($this->templateQuestion)) { ?>
						<tr>
							<th scope="row">
								<?php _e('Question template management', 'wp-pro-quiz'); ?>
							</th>
							<td>
								<fieldset>
									<legend class="screen-reader-text">
										<span><?php _e('Question template management', 'wp-pro-quiz'); ?></span>
									</legend>
									<select name="templateQuestion">
										<option value=""><?php _e('Select Question template', 'wp-pro-quiz'); ?></option>
										
										<?php foreach($this->templateQuestion as $templateQuestion) { 
											echo '<option value="'.$templateQuestion->getTemplateId().'">'.esc_html($templateQuestion->getName()).'</option>';
											
										} ?>
									</select>
									<div style="padding-top: 5px;">
										<input type="text" value="" name="templateQuestionEditText" class="regular-text" />
									</div>
									<div style="padding-top: 5px;">
										<input type="button" title="<?php _e('Delete selected Question template', 'wp-pro-quiz') ?>" value="<?php _e('Delete', 'wp-pro-quiz'); ?>" name="templateQuestionDelete" class="button-secondary">
										<input type="button" title="<?php _e('Save changed to selected Question template', 'wp-pro-quiz') ?>" value="<?php _e('Save Changes', 'wp-pro-quiz'); ?>" name="templateQuestionEdit" class="button-secondary"> 
										<div class="templateQuestionSpinner spinner"></div>
										<span class="templateQuestionEditUpdate" style="display:none"><?php _e('Question template Saved', 'wp-pro-quiz') ?></span>
										<span class="templateQuestionDeleteUpdate" style="display:none"><?php _e('Question template Deleted', 'wp-pro-quiz') ?></span>
										
									</div>
								</fieldset>
							</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>

<?php
	}
	private function emailSettings() {
?>
		<div class="postbox" id="adminEmailSettings">
			<h3 class="hndle"><?php _e('Admin e-mail settings', 'wp-pro-quiz'); ?></h3>
			<div class="wrap">
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row">
								<?php _e('To:', 'wp-pro-quiz'); ?>
							</th>
							<td>
								<label>
									<input type="text" name="email[to]" value="<?php echo $this->email['to']; ?>" class="regular-text">
								</label>
								<p class="description">
									<?php _e('Separate multiple email addresses with a comma, e.g. wp@test.com, test@test.com', 'wp-pro-quiz'); ?>
								</p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<?php _e('From Name:', 'wp-pro-quiz'); ?>
							</th>
							<td>
								<label>
									<input type="text" name="email[from_name]" value="<?php echo (isset($this->email['from_name']))? $this->email['from_name'] : ''; ?>" class="regular-text">
								</label>
 								<p class="description"><?php echo __('This is the email name of the sender. If not provided will default to the system email name.', 'wp-pro-quiz' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<?php _e('From Email:', 'wp-pro-quiz'); ?>
							</th>
							<td>
								<?php 
								if ( ( !empty( $this->email['from'] ) ) && ( !is_email( $this->email['from'] ) ) ) {
									?><p class="ld-error error-message"><?php _e('The value entered is not a valid email address', 'learndash'); ?></p><?php
								} 
								?>
								<label>
									<input type="text" name="email[from]" value="<?php echo (isset($this->email['from'])) ? $this->email['from'] : ''; ?>" class="regular-text">
								</label>
 								<p class="description">
									<?php echo sprintf( __('This is the email address of the sender. If not provided the admin email <strong>(%s)</strong> will be used.', 'wp-pro-quiz'), get_option('admin_email') ); ?>
 								</p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<?php _e('Subject:', 'wp-pro-quiz'); ?>
							</th>
							<td>
								<label>
									<input type="text" name="email[subject]" value="<?php echo $this->email['subject']; ?>" class="regular-text">
								</label>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<?php _e('HTML', 'wp-pro-quiz'); ?>
							</th>
							<td>
								<label>
									<input type="checkbox" name="email[html]" value="1" <?php $this->checked(isset($this->email['html']) ? $this->email['html'] : false); ?>> <?php _e('Activate', 'wp-pro-quiz'); ?>
								</label>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<?php _e('Message body:', 'wp-pro-quiz'); ?>
							</th>
							<td>
								<?php
									wp_editor($this->email['message'], 'adminEmailEditor', array('textarea_rows' => 20, 'textarea_name' => 'email[message]'));
								?>
								
								<div>
									<h4><?php _e('Allowed variables', 'wp-pro-quiz'); ?>:</h4>
									<ul>
										<li><span>$userId</span> - <?php _e('User-ID', 'wp-pro-quiz'); ?></li>
										<li><span>$username</span> - <?php _e('Username', 'wp-pro-quiz'); ?></li>
										<li><span>$quizname</span> - <?php _e('Quiz-Name', 'wp-pro-quiz'); ?></li>
										<li><span>$result</span> - <?php _e('Result in precent', 'wp-pro-quiz'); ?></li>
										<li><span>$points</span> - <?php _e('Reached points', 'wp-pro-quiz'); ?></li>
										<li><span>$ip</span> - <?php _e('IP-address of the user', 'wp-pro-quiz'); ?></li>
										<li><span>$categories</span> - <?php _e('Category-Overview', 'wp-pro-quiz'); ?></li>
									</ul>	
								</div>
								
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	
<?php 
	}
	
	private function userEmailSettings() {
?>
		<div class="postbox" id="userEmailSettings" style="display: none;">
			<h3 class="hndle"><?php _e('User e-mail settings', 'wp-pro-quiz'); ?></h3>
			<div class="wrap">
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row">
								<?php _e('From Name:', 'wp-pro-quiz'); ?>
							</th>
							<td>
								<label>
									<input type="text" name="userEmail[from_name]" value="<?php echo (isset($this->userEmail['from_name'])) ? $this->userEmail['from_name'] : ''; ?>" class="regular-text">
								</label>
 								<p class="description">
									<?php echo __('This is the email name of the sender. If not provided will default to the system email name.', 'wp-pro-quiz' ); ?>
 								</p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<?php _e('From Email:', 'wp-pro-quiz'); ?>
							</th>
							<td>
								<?php 
								if ( ( !empty( $this->email['from'] ) ) && ( !is_email( $this->userEmail['from'] ) ) ) {
									?><p class="ld-error error-message"><?php _e('The value entered is not a valid email address', 'learndash'); ?></p><?php
								} 
								?>
								<label>
									<input type="text" name="userEmail[from]" value="<?php echo (isset($this->userEmail['from'])) ? $this->userEmail['from'] : ''; ?>" class="regular-text">
								</label>
 								<p class="description">
									<?php echo sprintf( __('This is the email address of the sender. If not provided the admin email <strong>(%s)</strong> will be used.', 'wp-pro-quiz'), get_option('admin_email') ); ?>
 								</p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<?php _e('Subject:', 'wp-pro-quiz'); ?>
							</th>
							<td>
								<label>
									<input type="text" name="userEmail[subject]" value="<?php echo $this->userEmail['subject']; ?>" class="regular-text">
								</label>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<?php _e('HTML', 'wp-pro-quiz'); ?>
							</th>
							<td>
								<label>
									<input type="checkbox" name="userEmail[html]" value="1" <?php $this->checked($this->userEmail['html']); ?>> <?php _e('Activate', 'wp-pro-quiz'); ?>
								</label>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<?php _e('Message body:', 'wp-pro-quiz'); ?>
							</th>
							<td>
								<?php
									wp_editor($this->userEmail['message'], 'userEmailEditor', array('textarea_rows' => 20, 'textarea_name' => 'userEmail[message]'));
								?>
								
								<div>
									<h4><?php _e('Allowed variables', 'wp-pro-quiz'); ?>:</h4>
									<ul>
										<li><span>$userId</span> - <?php _e('User-ID', 'wp-pro-quiz'); ?></li>
										<li><span>$username</span> - <?php _e('Username', 'wp-pro-quiz'); ?></li>
										<li><span>$quizname</span> - <?php _e('Quiz-Name', 'wp-pro-quiz'); ?></li>
										<li><span>$result</span> - <?php _e('Result in precent', 'wp-pro-quiz'); ?></li>
										<li><span>$points</span> - <?php _e('Reached points', 'wp-pro-quiz'); ?></li>
										<li><span>$ip</span> - <?php _e('IP-address of the user', 'wp-pro-quiz'); ?></li>
										<li><span>$categories</span> - <?php _e('Category-Overview', 'wp-pro-quiz'); ?></li>
									</ul>	
								</div>
								
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
<?php 
	}
	
	private function problemSettings() {
		if($this->isRaw) {
			$rawSystem = __('to activate', 'wp-pro-quiz');
		} else {
			$rawSystem = __('not to activate', 'wp-pro-quiz');
		}

		?>
		
		<div class="updated" id="problemInfo" style="display: none;">
			<h3><?php _e('Please note', 'wp-pro-quiz'); ?></h3>
			<p>
				<?php _e('These settings should only be set in cases of problems with LD Advanced Quiz.', 'wp-pro-quiz'); ?>
			</p>
		</div>
		
		<h3 class="hndle"><?php _e('Settings in case of problems', 'wp-pro-quiz'); ?></h3>
		<div class="wrap">
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row">
							<?php _e('Automatically add [raw] shortcode', 'wp-pro-quiz'); ?>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span><?php _e('Automatically add [raw] shortcode', 'wp-pro-quiz'); ?></span>
								</legend>
								<label>
									<input type="checkbox" value="1" name="addRawShortcode" <?php echo $this->settings->isAddRawShortcode() ? 'checked="checked"' : '' ?> >
									<?php _e('Activate', 'wp-pro-quiz'); ?> <span class="description">( <?php printf(__('It is recommended %s this option on your system.', 'wp-pro-quiz'), '<span style=" font-weight: bold;">'.$rawSystem.'</span>'); ?> )</span>
								</label>
								<p class="description">
									<?php _e('If this option is activated, a [raw] shortcode is automatically set around LDAdvQuiz shortcode ( [LDAdvQuiz X] ) into [raw] [LDAdvQuiz X] [/raw]', 'wp-pro-quiz'); ?>
								</p>
								<p class="description">
									<?php _e('Own themes changes internal  order of filters, what causes the problems. With additional shortcode [raw] this is prevented.', 'wp-pro-quiz'); ?>
								</p>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php _e('Do not load the Javascript-files in the footer', 'wp-pro-quiz'); ?>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span><?php _e('Do not load the Javascript-files in the footer', 'wp-pro-quiz'); ?></span>
								</legend>
								<label>
									<input type="checkbox" value="1" name="jsLoadInHead" <?php echo $this->settings->isJsLoadInHead() ? 'checked="checked"' : '' ?> >
									<?php _e('Activate', 'wp-pro-quiz'); ?>
								</label>
								<p class="description">
									<?php _e('Generally all LDAdvQuiz-Javascript files are loaded in the footer and only when they are really needed.', 'wp-pro-quiz'); ?>
								</p>
								<p class="description">
									<?php _e('In very old Wordpress themes this can lead to problems.', 'wp-pro-quiz'); ?>
								</p>
								<p class="description">
									<?php _e('If you activate this option, all LDAdvQuiz-Javascript files are loaded in the header even if they are not needed.', 'wp-pro-quiz'); ?>
								</p>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php _e('Touch Library', 'wp-pro-quiz'); ?>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span><?php _e('Touch Library', 'wp-pro-quiz'); ?></span>
								</legend>
								<label>
									<input type="checkbox" value="1" name="touchLibraryDeactivate" <?php echo $this->settings->isTouchLibraryDeactivate() ? 'checked="checked"' : '' ?> >
									<?php _e('Deactivate', 'wp-pro-quiz'); ?>
								</label>
								<p class="description">
									<?php _e('In Version 0.13 a new Touch Library was added for mobile devices.', 'wp-pro-quiz'); ?>
								</p>
								<p class="description">
									<?php _e('If you have any problems with the Touch Library, please deactivate it.', 'wp-pro-quiz'); ?>
								</p>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php _e('jQuery support cors', 'wp-pro-quiz'); ?>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span><?php _e('jQuery support cors', 'wp-pro-quiz'); ?></span>
								</legend>
								<label>
									<input type="checkbox" value="1" name="corsActivated" <?php echo $this->settings->isCorsActivated() ? 'checked="checked"' : '' ?> >
									<?php _e('Activate', 'wp-pro-quiz'); ?>
								</label>
								<p class="description">
									<?php _e('Is required only in rare cases.', 'wp-pro-quiz'); ?>
								</p>
								<p class="description">
									<?php _e('If you have problems with the front ajax, please activate it.', 'wp-pro-quiz'); ?>
								</p>
								<p class="description">
									<?php _e('e.g. Domain with special characters in combination with IE', 'wp-pro-quiz'); ?>
								</p>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php _e('Repair database', 'wp-pro-quiz'); ?>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span><?php _e('Repair database', 'wp-pro-quiz'); ?></span>
								</legend>
								<input type="submit" name="databaseFix" class="button-primary" value="<?php _e('Repair database', 'wp-pro-quiz');?>">
								<p class="description">
									<?php _e('No data will be deleted. Only LDAdvQuiz tables will be repaired.', 'wp-pro-quiz'); ?>
								</p>
							</fieldset>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		
		<?php
	}
	
	private function emailSettingsTab() {
		?>
		
		<div class="wpProQuiz_tab_wrapper" style="padding-bottom: 10px;">
			<a class="button-primary" href="#" data-tab="#adminEmailSettings"><?php _e('Admin e-mail settings', 'wp-pro-quiz'); ?></a>
			<a class="button-secondary" href="#" data-tab="#userEmailSettings"><?php _e('User e-mail settings', 'wp-pro-quiz'); ?></a>
		</div>
		
		<?php $this->emailSettings(); ?>
		<?php $this->userEmailSettings(); ?>
		
		<?php 
	}
}