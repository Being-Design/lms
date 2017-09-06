<?php
if (!class_exists('Learndash_Admin_Settings_Support_Panel')) {
	class Learndash_Admin_Settings_Support_Panel {

		var $mo_files = array();
		
		function __construct() {
			$this->parent_menu_page_url		=	'admin.php?page=learndash_lms_settings';
			$this->menu_page_capability		=	LEARNDASH_ADMIN_CAPABILITY_CHECK;
			$this->settings_page_id 		= 	'learndash_support'; 
			$this->settings_page_title 		= 	_x( 'Support', 'Support Tab Label', 'learndash' );
			$this->settings_tab_title		=	$this->settings_page_title;
			$this->settings_tab_priority	=	40;

			add_action( 'admin_menu', 			array( $this, 'admin_menu' ) );
			add_action( 'load_textdomain', array( $this, 'load_textdomain' ), 10, 2 );
			add_action( 'learndash_admin_tabs_set', array( $this, 'admin_tabs' ), 10 );
		}
		
		/**
		 * Register settings page
		 */
		public function admin_menu() {
			$this->settings_screen_id = add_submenu_page(
				$this->parent_menu_page_url,
				$this->settings_page_title,
				$this->settings_page_title,
				$this->menu_page_capability,
				$this->settings_page_id,
				array( $this, 'admin_page' )
			);
			add_action( 'load-'. $this->settings_screen_id, array( $this, 'on_load_panel' ) );
		}
		
		function admin_tabs( $admin_menu_section = '' ) {

			if ( $admin_menu_section == $this->parent_menu_page_url ) {
				learndash_add_admin_tab_item(
					$this->parent_menu_page_url,
					array(
						'id'	=> 	$this->settings_screen_id,
						'link'	=> 	add_query_arg( array( 'page' => $this->settings_page_id ), 'admin.php' ),
						'name'	=> 	!empty( $this->settings_tab_title )  ? $this->settings_tab_title : $this->settings_page_title,
					),
					$this->settings_tab_priority
				);
			}
		}

		// Track the loaded MO files for our text domain. This is used on Support tab
		function load_textdomain( $domain = '', $mofile = '' ) {			
			if ( ( $domain == LEARNDASH_LMS_TEXT_DOMAIN ) || ( $domain == WPPROQUIZ_TEXT_DOMAIN ) ) {
				if ( ( !empty( $mofile ) ) && ( !isset( $this->mo_files[$mofile] ) ) ) {
					if ( !isset( $this->mo_files[$domain] ) ) $this->mo_files[$domain] = array();
					
					$this->mo_files[$domain][$mofile] = $mofile;
				}
			}
		}
		
		function on_load_panel() {

			// Load JS/CSS as needed for page
			wp_enqueue_style( 
				'sfwd-module-style', 
				LEARNDASH_LMS_PLUGIN_URL . '/assets/css/sfwd_module'. ( ( defined( 'LEARNDASH_SCRIPT_DEBUG' ) && ( LEARNDASH_SCRIPT_DEBUG === true ) ) ? '' : '.min') .'.css', 
				array(), 
				LEARNDASH_VERSION 
			);
			$learndash_assets_loaded['styles']['sfwd-module-style'] = __FUNCTION__;
		}

		/**
		 * Output settings page
		 */
		public function admin_page() {
			?>
			<div id="learndash-settings-support" class="learndash-settings" class="wrap">
				<h1><?php _e( 'Support', 'learndash' ); ?></h1>
				<p><a class="button button-primary" target="_blank" href="http://support.learndash.com/"><?php _e('LearnDash Support', 'learndash') ?></a></p>
				<hr />

				<?php
				global $wpdb, $wp_version;
				?>
				
				<h2><?php _e('Server', 'learndash' ); ?></h2>
				<table cellspacing="0" class="learndash-support-settings">
					<thead>
						<tr>
							<th scope="col" class="learndash-support-settings-left"><?php _e('Setting', 'learndash') ?></th>
							<th scope="col" class="learndash-support-settings-right"><?php _e('Value', 'learndash') ?></th>
						</tr>
					</thead>
					<tbody>
						<tr><td scope="row"><strong><?php _e('PHP Version', 'learndash') ?></strong></td><td><?php 
							$php_version = phpversion(); 
							$version_compare = version_compare( '7.0', $php_version, '>' );
							$color = 'green';
							if ( $version_compare == -1) {
								$color = 'red';
							} 
							echo '<span style="color: '. $color .'">'. $php_version .'</span>'; 
							if ( defined( 'PHP_OS' ) )  echo __(' PHP_OS: ', 'learndash') . PHP_OS;
							if ( defined( 'PHP_OS_FAMILY' ) )  echo __(' PHP_OS_FAMILY: ', 'learndash') . PHP_OS_FAMILY;
							//echo ', Family: '. PHP_OS_FAMILY;
							if ( $version_compare == -1 ) {
								echo ' - <a href="https://wordpress.org/about/requirements/" target="_blank">'. __('WordPress Minimum Requirements', 'learndash') .'</a>';
							}
						?></th></tr>
						<?php if ($wpdb->is_mysql == true) { ?>
						<tr><td><strong><?php _e('MySQL version', 'learndash') ?></strong></td><td><?php 
							$mysql_version = $wpdb->db_version();
							
							$version_compare = version_compare( '5.6', $mysql_version, '>' );
							$color = 'green';
							if ( $version_compare == -1) {
								$color = 'red';
							} 
							echo '<span style="color: '. $color .'">'. $mysql_version .'</span>'; 
							if ( $version_compare == -1) {
								echo ' - <a href="https://wordpress.org/about/requirements/" target="_blank">'. __('WordPress Minimum Requirements', 'learndash') .'</a>';
							}
							
						?><td></tr>
						<?php } ?>
					</tbody>
				</table>
				
				<h2><?php _e('WordPress Settings', 'learndash' ); ?></h2>
				<table cellspacing="0" class="learndash-support-settings">
					<thead>
						<tr>
							<th scope="col" class="learndash-support-settings-left"><?php _e('Setting', 'learndash') ?></th>
							<th scope="col" class="learndash-support-settings-right"><?php _e('Value', 'learndash') ?></th>
						</tr>
					</thead>
					<tbody>
						<tr><th scope="row"><strong><?php _e('WordPress Version', 'learndash') ?></strong></th>
							<td><?php echo $wp_version; ?></td></tr>
						<tr><th scope="row"><strong><?php _e('Is Multisite', 'learndash') ?></strong></th>
							<td><?php echo is_multisite() ? __( 'Yes', 'learndash' ) : __( 'No', 'learndash' ) ?></td></tr>
						<tr><th scope="row"><strong><?php _e('Site Language', 'learndash') ?></strong></th>
							<td><?php echo get_locale(); ?></td></tr>
						<tr><th scope="row"><strong><?php _e('DISABLE_WP_CRON', 'learndash') ?></strong></th>
							<td><?php echo ( defined( 'DISABLE_WP_CRON' ) ) ? DISABLE_WP_CRON : __('not defined', 'learndash');  ?></td></tr>
						<tr><th scope="row"><strong><?php _e('WP_DEBUG', 'learndash') ?></strong></th>
							<td><?php echo ( defined( 'WP_DEBUG' ) ) ? WP_DEBUG : __('not defined', 'learndash'); ?></td></tr>
						<tr><th scope="row"><strong><?php _e('WP_DEBUG_DISPLAY', 'learndash') ?></strong></th>
							<td><?php echo ( defined( 'WP_DEBUG_DISPLAY' ) ) ? WP_DEBUG_DISPLAY : __('not defined', 'learndash'); ?></td></tr>
						<tr><th scope="row"><strong><?php _e('SCRIPT_DEBUG', 'learndash') ?></strong></th>
							<td><?php echo ( defined( 'SCRIPT_DEBUG' ) ) ? SCRIPT_DEBUG : __('not defined', 'learndash'); ?></td></tr>
						<tr><th scope="row"><strong><?php _e('WP_DEBUG_DISPLAY', 'learndash') ?></strong></th>
							<td><?php echo ( defined( 'WP_DEBUG_DISPLAY' ) ) ? WP_DEBUG_DISPLAY : __('not defined', 'learndash'); ?></td></tr>
						<tr><th scope="row"><strong><?php _e('WP_DEBUG_LOG', 'learndash') ?></strong></th>
							<td><?php echo ( defined( 'WP_DEBUG_LOG' ) ) ? WP_DEBUG_LOG : __('not defined', 'learndash'); ?></td></tr>
						<tr><th scope="row"><strong><?php _e('WP_AUTO_UPDATE_CORE', 'learndash') ?></strong></th>
							<td><?php echo ( defined( 'WP_AUTO_UPDATE_CORE' ) ) ? WP_AUTO_UPDATE_CORE : __('not defined', 'learndash'); ?></td></tr>
						<tr><th scope="row"><strong><?php _e('WP_MAX_MEMORY_LIMIT', 'learndash') ?></strong></th>
							<td><?php echo ( defined( 'WP_MAX_MEMORY_LIMIT' ) ) ? WP_MAX_MEMORY_LIMIT : __('not defined', 'learndash'); ?></td></tr>
						<tr><th scope="row"><strong><?php _e('WP_MEMORY_LIMIT', 'learndash') ?></strong></th>
							<td><?php echo ( defined( 'WP_MEMORY_LIMIT' ) ) ? WP_MEMORY_LIMIT : __('not defined', 'learndash');  ?></td></tr>
						<tr><th scope="row"><strong><?php _e('DB_CHARSET', 'learndash') ?></strong></th>
							<td><?php echo ( defined( 'DB_CHARSET' ) ) ? DB_CHARSET : __('not defined', 'learndash');  ?></td></tr>
						<tr><th scope="row"><strong><?php _e('DB_COLLATE', 'learndash') ?></strong></th>
							<td><?php echo ( defined( 'DB_COLLATE' ) ) ? DB_COLLATE : __('not defined', 'learndash');  ?></td></tr>
						<tr><th scope="row"><strong><?php _e('Object Cache', 'learndash') ?></strong></th>
							<td><?php echo wp_using_ext_object_cache() ? __( 'Yes', 'learndash' ) : __( 'No', 'learndash' ) ?></td></tr>

					</tbody>
				</table>
				
				<h2><?php _e('Learndash settings', 'learndash' ); ?></h2>
				<table cellspacing="0" class="learndash-support-settings">
					<thead>
						<tr>
							<th scope="col" class="learndash-support-settings-left"><?php _e('Setting', 'learndash') ?></th>
							<th scope="col" class="learndash-support-settings-right"><?php _e('Value', 'learndash') ?></th>
						</tr>
					</thead>
					<tbody>
						<tr><th scope="row"><strong><?php _e('Version', 'learndash') ?></strong></th>
							<td><?php echo LEARNDASH_VERSION; ?></td></tr>
						<tr><th scope="row"><strong><?php _e('DB Version', 'learndash') ?></strong></th>
							<td><?php echo LEARNDASH_SETTINGS_DB_VERSION; ?></td></tr>
						<tr><th scope="row"><strong><?php _e('Script Debug', 'learndash') ?></strong></th>
							<td><?php echo ( defined( 'LEARNDASH_SCRIPT_DEBUG' ) ) ? LEARNDASH_SCRIPT_DEBUG : __('not defined', 'learndash'); ?></td></tr>
						<tr><th scope="row"><strong><?php _e('Translation Files', 'learndash') ?></strong></th>
							<td><?php 
							if ( !empty( $this->mo_files ) ) {
								foreach( $this->mo_files as $domain => $mo_files ) {
									$mo_files_output = '';
									foreach( $mo_files as $mo_file ) {
										if ( file_exists( $mo_file ) ) {
											if ( !empty( $mo_files_output ) ) $mo_files_output .= ', ';
											$mo_files_output .= str_replace( ABSPATH, '', $mo_file );
											$mo_files_output .= ' <em>'. learndash_adjust_date_time_display( filectime( $mo_file ) ) .'</em>';
										}
									}
									if ( !empty( $mo_files_output ) ) {
										echo '<strong>'. $domain .'</strong> - ' . $mo_files_output .'<br />';
									}
								}
							}
						?></td></tr>
					</tbody>
				</table>

				<?php
					$ABSPATH_tmp = str_replace('\\', '/', ABSPATH );
					$LEARNDASH_LMS_PLUGIN_DIR_tmp = str_replace( '\\', '/', LEARNDASH_LMS_PLUGIN_DIR );
					$LEARNDASH_TEMPLATES_DIR_tmp = str_replace( '\\', '/', LEARNDASH_TEMPLATES_DIR );
					$CHILD_THEME_TEMPLATE_DIR_tmp = str_replace( '\\', '/', get_stylesheet_directory() );
					$PARENT_THEME_TEMPLATE_DIR_tmp = str_replace( '\\', '/', get_template_directory() );
				?>

				<h2><?php _e('Learndash Templates', 'learndash' ); ?></h2>
				<p>
					<?php _e('The following is the search order paths for override templates, relative to site root:', 'learndash'); ?>
					<ol>
						<li><?php echo str_replace( $ABSPATH_tmp, '/', $CHILD_THEME_TEMPLATE_DIR_tmp ).'/learndash/'; ?></li>
						<li><?php echo str_replace( $ABSPATH_tmp, '/', $CHILD_THEME_TEMPLATE_DIR_tmp ).'/'; ?></li>

						<?php if ( $CHILD_THEME_TEMPLATE_DIR_tmp != $PARENT_THEME_TEMPLATE_DIR_tmp ) { ?>
							<li><?php echo str_replace( $ABSPATH_tmp, '/', $PARENT_THEME_TEMPLATE_DIR_tmp ).'/learndash/'; ?></li>
							<li><?php echo str_replace( $ABSPATH_tmp, '/', $PARENT_THEME_TEMPLATE_DIR_tmp ).'/'; ?></li>
						<?php } ?>

						<li><?php echo str_replace( $ABSPATH_tmp, '/', $LEARNDASH_TEMPLATES_DIR_tmp ); ?></li>
						<li><?php echo str_replace( $ABSPATH_tmp, '/', $LEARNDASH_LMS_PLUGIN_DIR_tmp ).'templates/'; ?></li>
					</ol>
				</p>
				
				<?php
					$template_array = glob( $LEARNDASH_LMS_PLUGIN_DIR_tmp . 'templates/*.{php,js,css}', GLOB_BRACE);
					foreach( $template_array as $idx => $template_file ) {
						$filename = basename( $template_file );
						
						if ( strpos( $filename, '.min.') !== false) {
							unset( $template_array[$idx] );
						} else {
							$template_array[$idx] = $filename;
						}
					}
				?>
				<table cellspacing="0" class="learndash-support-settings">
					<thead>
						<tr>
							<th scope="col" class="learndash-support-settings-left"><?php _e('Template Name', 'learndash') ?></th>
							<th scope="col" class="learndash-support-settings-right"><?php _e('Template Path', 'learndash') ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($template_array as $template) { ?>
							<tr>
								<th scope="row"><strong><?php echo $template ?></strong></th>
								<td><?php 
									$template_path = SFWD_LMS::get_template( $template, null, null, true );
									$template_path = str_replace('\\', '/', $template_path );
									if ( strncmp ( $template_path, $LEARNDASH_LMS_PLUGIN_DIR_tmp , strlen( $LEARNDASH_LMS_PLUGIN_DIR_tmp ) ) != 0) {
										$color = 'red';
									} else {
										$color = 'inherit';
									}
								
									echo '<span style="color: '. $color .'">'. str_replace( $ABSPATH_tmp, '', $template_path ) .'</span>'; 
								?></td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
				

				<?php
				$db_tables = array('learndash_user_activity', 'learndash_user_activity_meta', 'wp_pro_quiz_category', 'wp_pro_quiz_form', 'wp_pro_quiz_lock', 'wp_pro_quiz_master', 'wp_pro_quiz_prerequisite', 'wp_pro_quiz_question', 'wp_pro_quiz_statistic', 'wp_pro_quiz_statistic_ref', 'wp_pro_quiz_template', 'wp_pro_quiz_toplist');
				sort( $db_tables );
				
				?>
				<h2><?php _e('Database Tables', 'learndash' ); ?></h2>
				<table cellspacing="0" class="learndash-support-settings">
					<thead>
						<tr>
							<th scope="col" class="learndash-support-settings-left"><?php _e('Table Name', 'learndash') ?></th>
							<th scope="col" class="learndash-support-settings-right"><?php _e('Present', 'learndash') ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($db_tables as $db_table ) { ?>
							<tr><th scope="row"><strong><?php echo $wpdb->prefix.$db_table ?></strong></th>
								<td><?php 
								if ( $wpdb->get_var("SHOW TABLES LIKE '". $wpdb->prefix.$db_table ."'") == $wpdb->prefix.$db_table ) {
									echo '<span style="color: green">'. __('Yes', 'learndash' ) .'</span>';
								} else {
									echo '<span style="color: red">'. __('No', 'learndash' ) .'</span>';
								}
								 ?></td></tr>
						<?php } ?>
					</tbody>
				</table>

				
				<?php
				$php_ini_settings = array('max_execution_time', 'max_input_time', 'max_input_vars', 'post_max_size', 'max_file_uploads', 'upload_max_filesize');
				sort($php_ini_settings);
				?>
				<h2><?php _e('PHP Settings', 'learndash' ); ?></h2>
				<table cellspacing="0" class="learndash-support-settings">
					<thead>
						<tr>
							<th scope="col" class="learndash-support-settings-left"><?php _e('Setting', 'learndash') ?></th>
							<th scope="col" class="learndash-support-settings-right"><?php _e('Value', 'learndash') ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($php_ini_settings as $ini_key ) {?>
							<tr><th scope="row"><strong><?php echo $ini_key ?></strong></th>
								<td><?php echo ini_get( $ini_key ) ?></td></tr>
						<?php } ?>
						
						<tr><th scope="row"><strong><?php _e('cURL - Used for PayPal IPN Processing', 'learndash') ?></strong></th>
							<td>
							<?php
								if ( !extension_loaded( 'curl' ) ) {
									echo '<span style="color: red">'. __('No', 'learndash' ) .'</span>';
								} else {
									echo '<span style="color: green">'. __('Yes', 'learndash' ) .'</span><br />';
									$version = curl_version();

									echo __("Version", 'learndash' ) .": ". $version['version'] ."<br />";
									echo __("SSL Version", 'learndash' ) .": ". $version['ssl_version'] ."<br />";
									echo __("Libz Version", 'learndash' ) .": ". $version['libz_version'] ."<br />";
									echo __("Protocols", 'learndash' ) .": ". join(', ', $version['protocols']) ."<br />";
								}
							?>
							</td></tr>
					</tbody>
				</table>
						
				
				<?php /* ?>
				<h2><?php _e('Active Theme', 'learndash' ); ?></h2>
				<ul>
				<?php 
					$current_theme =  wp_get_theme(); 
					//echo "current_theme<pre>". print_r($current_theme, true) ."</pre>";
					if ( $current_theme->exists() ) {
						?><li><strong><?php echo $current_theme->get( 'Name' ) ?></strong>: <?php echo $current_theme->get( 'Version' ) ?> ( <?php echo $current_theme->get( 'ThemeURI' ) ?> )</li><?php
					}
				?>
				</ul>
				<?php */ ?>
				<?php /* ?>
				<h2><?php _e('Active Plugins', 'learndash' ); ?></h2>
				<?php 
					$all_plugins = get_plugins(); 
					//echo "all_plugins<pre>". print_r($all_plugins, true) ."</pre>";
					if (!empty( $all_plugins ) ) {
						?><ul><?php
						foreach( $all_plugins as $plugin_key => $plugin_data ) { 
							if (is_plugin_active($plugin_key)) {
								?><li><strong><?php echo $plugin_data['Name'] ?></strong>: <?php echo $plugin_data['Version'] ?> ( <?php echo $plugin_data['PluginURI'] ?> )</li><?php
							}
						}
						?></ul><?php
					}
				?>
				<?php */ ?>
				
			</div>
			<?php
		}
	}
}
