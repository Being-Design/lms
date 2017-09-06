<?php
if( !defined( 'ABSPATH' ) ){
    exit();
}

class buddyboss_onclick_system_status_helper{
    public function __construct(){
    }
    
    public function let_to_num( $size ) {
        $l   = substr( $size, -1 );
        $ret = substr( $size, 0, -1 );
        switch ( strtoupper( $l ) ) {
            case 'P':
                $ret *= 1024;
            case 'T':
                $ret *= 1024;
            case 'G':
                $ret *= 1024;
            case 'M':
                $ret *= 1024;
            case 'K':
                $ret *= 1024;
        }
        return $ret;
    }
    
    public function get_environment_info() {
		global $wpdb;

		// Figure out cURL version, if installed.
		$curl_version = '';
		if ( function_exists( 'curl_version' ) ) {
			$curl_version = curl_version();
			$curl_version = $curl_version['version'] . ', ' . $curl_version['ssl_version'];
		}

		// WP memory limit
		$wp_memory_limit = $this->let_to_num( WP_MEMORY_LIMIT );
		if ( function_exists( 'memory_get_usage' ) ) {
			$wp_memory_limit = max( $wp_memory_limit, $this->let_to_num( @ini_get( 'memory_limit' ) ) );
		}

		// Return all environment info. Described by JSON Schema.
		return array(
			'home_url'                  => get_option( 'home' ),
			'site_url'                  => get_option( 'siteurl' ),
			'wp_version'                => get_bloginfo( 'version' ),
			'wp_multisite'              => is_multisite(),
			'wp_memory_limit'           => $wp_memory_limit,
			'wp_debug_mode'             => ( defined( 'WP_DEBUG' ) && WP_DEBUG ),
			'wp_cron'                   => ! ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ),
			'language'                  => get_locale(),
			'server_info'               => $_SERVER['SERVER_SOFTWARE'],
			'php_version'               => phpversion(),
			'php_post_max_size'         => $this->let_to_num( ini_get( 'post_max_size' ) ),
			'php_max_execution_time'    => ini_get( 'max_execution_time' ),
			'php_max_input_vars'        => ini_get( 'max_input_vars' ),
			'curl_version'              => $curl_version,
			'max_upload_size'           => wp_max_upload_size(),
			'mysql_version'             => ( ! empty( $wpdb->is_mysql ) ? $wpdb->db_version() : '' ),
			'default_timezone'          => date_default_timezone_get(),
			'fsockopen_or_curl_enabled' => ( function_exists( 'fsockopen' ) || function_exists( 'curl_init' ) ),
			'domdocument_enabled'       => class_exists( 'DOMDocument' ),
			'gzip_enabled'              => is_callable( 'gzopen' ),
			'mbstring_enabled'          => extension_loaded( 'mbstring' ),
		);
	}
    
    public function get_active_plugins() {
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		require_once( ABSPATH . 'wp-admin/includes/update.php' );

		if ( ! function_exists( 'get_plugin_updates' ) ) {
			return array();
		}

		// Get both site plugins and network plugins
		$active_plugins = (array) get_option( 'active_plugins', array() );
		if ( is_multisite() ) {
			$network_activated_plugins = array_keys( get_site_option( 'active_sitewide_plugins', array() ) );
			$active_plugins            = array_merge( $active_plugins, $network_activated_plugins );
		}

		$active_plugins_data = array();
		$available_updates   = get_plugin_updates();

		foreach ( $active_plugins as $plugin ) {
			$data           = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );

			// convert plugin data to json response format.
			$active_plugins_data[] = array(
				'plugin'            => $plugin,
				'name'              => $data['Name'],
				'version'           => $data['Version'],
				'url'               => $data['PluginURI'],
				'author_name'       => $data['AuthorName'],
				'author_url'        => esc_url_raw( $data['AuthorURI'] ),
				'network_activated' => $data['Network'],
			);
		}

		return $active_plugins_data;
	}
    
    public function help_tip( $tip ){
        return '<span class="dashicons dashicons-info" title="'. esc_attr( $tip ) .'"></span>';
    }
    
    public function display_brief(){
        ?>
        <div style="margin-bottom: 20px;"></div>
        <table class="widefat">
            <thead>
                <tr>
                    <th colspan="3"><?php _e( 'System Status', 'buddyboss-one-click' );?></th>
                </tr>
            </thead>
            <tbody>
                <?php 
                global $wp_version;
                if ( version_compare( $wp_version, '4.7', '<=' ) ) {
                    $status = __( 'FAIL', 'buddyboss-one-click' );
                } else {
                    $status = __( 'OK', 'buddyboss-one-click' );
                }
                ?>
                <tr class="entry-row alternate">
                    <td><?php _e( 'WordPress Version', 'buddyboss-one-click' );?></td>
                    <td><?php echo $wp_version;?></td>
                    <td><?php echo $status;?></td>
                </tr>
                
                <?php 
                global $wpdb;
                if ( version_compare( $wpdb->db_version(), '5.5.0', '<=' ) ) {
                    $status = __( 'FAIL', 'buddyboss-one-click' );
                } elseif ( version_compare( $wpdb->db_version(), '5.5.90', '<=' ) ) {
                    $status = __( 'WARNING', 'buddyboss-one-click' );
                } else {
                    $status = __( 'OK', 'buddyboss-one-click' );
                }
                ?>
                <tr class="entry-row alternate">
                    <td><?php _e( 'MySQL Version', 'buddyboss-one-click' );?></td>
                    <td><?php echo $wpdb->db_version();?></td>
                    <td><?php echo $status;?></td>
                </tr>
                
                <?php 
                //PHP Version
                $php_minimum = '5.3';
                if ( version_compare( phpversion(), $php_minimum, '<=' ) ) {
                    $status = __( 'WARNING', 'buddyboss-one-click' );
                } else {
                    $status = __( 'OK', 'buddyboss-one-click' );
                }
                ?>
                <tr class="entry-row alternate">
                    <td><?php _e( 'PHP Version', 'buddyboss-one-click' );?></td>
                    <td><?php echo phpversion();?></td>
                    <td><?php echo $status;?></td>
                </tr>
                
                <?php 
                //PHP Max execution time
                if ( str_ireplace( 's', '', ini_get( 'max_execution_time' ) ) < 30 ) {
                    $status = __( 'WARNING', 'buddyboss-one-click' );
                } else {
                    $status = __( 'OK', 'buddyboss-one-click' );
                }
                ?>
                <tr class="entry-row alternate">
                    <td>max_execution_time</td>
                    <td><?php echo ini_get( 'max_execution_time' );?></td>
                    <td><?php echo $status;?></td>
                </tr>
                
                <?php 
                // MEMORY LIMIT 
                $phpinfo_array = $this->phpinfo_array( 4 );

                $mem_limits = array();
                if ( ! isset( $phpinfo_array['memory_limit'] ) ) {
                    $parent_class_val = 'unknown';
                } else {
                    global $bb_local_mem, $bb_master_mem;
                    $bb_local_mem = $this->return_bytes( $phpinfo_array['memory_limit'][0] ) / 1024 / 1024;
                    $mem_limits[] = $phpinfo_array['memory_limit'][0];
                    $parent_class_val = $phpinfo_array['memory_limit'][0];
                    if ( isset( $phpinfo_array['memory_limit'][1] ) ) {
                        $bb_master_mem = $this->return_bytes( $phpinfo_array['memory_limit'][1] ) / 1024 / 1024;
                        $mem_limits[] = $phpinfo_array['memory_limit'][1];
                        $parent_class_val .= ' (local) / ' . $phpinfo_array['memory_limit'][1]. ' (master)';
                    }
                }

                $status = __( 'OK', 'buddyboss-one-click' );
                foreach( $mem_limits as $mem_limit ) {
                    if ( preg_match( '/(\d+)(\w*)/', $mem_limit, $matches ) ) {
                        $val = $matches[1];
                        $unit = $matches[2];
                        // Up memory limit if currently lower than 256M.
                        if ( 'g' !== strtolower( $unit ) ) {
                            if ( 'm' !== strtolower( $unit ) ) {
                                $status = __( 'WARNING', 'buddyboss-one-click' );
                            } elseif ( $val < 125 ) {
                                $status = __( 'FAIL', 'buddyboss-one-click' );
                            } elseif ( $val < 250 ) {
                                $status = __( 'WARNING', 'buddyboss-one-click' );
                            } else {
                                $status = __( 'OK', 'buddyboss-one-click' );
                            }
                        }
                    } else {
                        $status = __( 'WARNING', 'buddyboss-one-click' );
                    }

                    // Once set to warning, don't process any more.
                    if ( $status == __( 'WARNING', 'buddyboss-one-click' ) ) {
                        break;
                    }
                }
                ?>
                <tr class="entry-row alternate">
                    <td><?php _e( 'PHP Memory Limit', 'buddyboss-one-click' );?></td>
                    <td><?php echo $parent_class_val;?></td>
                    <td><?php echo $status;?></td>
                </tr>
            </tbody>
        </table>
        
        <p><a href="<?php echo admin_url( 'admin.php?page=buddyboss-oneclick-installer&_tab=system-info' );?>">
            <?php _e( 'View more', 'buddyboss-one-click' );?> &raquo;</a></p>
        <?php 
    }
    
    
    public function display_details(){
        global $wpdb;
        $environment = $this->get_environment_info();
        $active_plugins = $this->get_active_plugins();
        ?>
        
        <div style="margin-bottom: 40px;"></div>
        
        <table class="bboc_status_table widefat" cellspacing="0" id="status">
            <thead>
                <tr>
                    <th colspan="3" data-export-label="WordPress Environment"><h2><?php _e( 'WordPress environment', 'buddyboss-one-click' ); ?></h2></th>
                </tr>
            </thead>
            <tbody>
                <tr class="entry-row alternate">
                    <td data-export-label="Home URL"><?php _e( 'Home URL', 'buddyboss-one-click' ); ?>:</td>
                    <td class="help"><?php echo $this->help_tip( __( 'The homepage URL of your site.', 'buddyboss-one-click' ) ); ?></td>
                    <td><?php echo esc_html( $environment['home_url'] ) ?></td>
                </tr>
                <tr>
                    <td data-export-label="Site URL"><?php _e( 'Site URL', 'buddyboss-one-click' ); ?>:</td>
                    <td class="help"><?php echo $this->help_tip( __( 'The root URL of your site.', 'buddyboss-one-click' ) ); ?></td>
                    <td><?php echo esc_html( $environment['site_url'] ) ?></td>
                </tr>
                
                <tr class="entry-row alternate">
                    <td data-export-label="WP Version"><?php _e( 'WP version', 'buddyboss-one-click' ); ?>:</td>
                    <td class="help"><?php echo $this->help_tip( __( 'The version of WordPress installed on your site.', 'buddyboss-one-click' ) ); ?></td>
                    <td><?php echo esc_html( $environment['wp_version'] ) ?></td>
                </tr>
                
                <tr>
                    <td data-export-label="WP Multisite"><?php _e( 'WP multisite', 'buddyboss-one-click' ); ?>:</td>
                    <td class="help"><?php echo $this->help_tip( __( 'Whether or not you have WordPress Multisite enabled.', 'buddyboss-one-click' ) ); ?></td>
                    <td><?php echo ( $environment['wp_multisite'] ) ? '<span class="dashicons dashicons-yes"></span>' : '&ndash;'; ?></td>
                </tr>
                
                <tr class="entry-row alternate">
                    <td data-export-label="WP Memory Limit"><?php _e( 'WP memory limit', 'buddyboss-one-click' ); ?>:</td>
                    <td class="help"><?php echo $this->help_tip( __( 'The maximum amount of memory (RAM) that your site can use at one time.', 'buddyboss-one-click' ) ); ?></td>
                    <td><?php
                        if ( $environment['wp_memory_limit'] < 67108864 ) {
                            echo '<mark class="error"><span class="dashicons dashicons-warning"></span> ' . sprintf( __( '%1$s - We recommend setting memory to at least 64MB. See: %2$s', 'buddyboss-one-click' ), size_format( $environment['wp_memory_limit'] ), '<a href="https://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP" target="_blank">' . __( 'Increasing memory allocated to PHP', 'buddyboss-one-click' ) . '</a>' ) . '</mark>';
                        } else {
                            echo '<mark class="yes">' . size_format( $environment['wp_memory_limit'] ) . '</mark>';
                        }
                    ?></td>
                </tr>
                
                <tr>
                    <td data-export-label="WP Debug Mode"><?php _e( 'WP debug mode', 'buddyboss-one-click' ); ?>:</td>
                    <td class="help"><?php echo $this->help_tip( __( 'Displays whether or not WordPress is in Debug Mode.', 'buddyboss-one-click' ) ); ?></td>
                    <td>
                        <?php if ( $environment['wp_debug_mode'] ) : ?>
                            <mark class="yes"><span class="dashicons dashicons-yes"></span></mark>
                        <?php else : ?>
                            <mark class="no">&ndash;</mark>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr class="entry-row alternate">
                    <td data-export-label="WP Cron"><?php _e( 'WP cron', 'buddyboss-one-click' ); ?>:</td>
                    <td class="help"><?php echo $this->help_tip( __( 'Displays whether or not WP Cron Jobs are enabled.', 'buddyboss-one-click' ) ); ?></td>
                    <td>
                        <?php if ( $environment['wp_cron'] ) : ?>
                            <mark class="yes"><span class="dashicons dashicons-yes"></span></mark>
                        <?php else : ?>
                            <mark class="no">&ndash;</mark>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td data-export-label="Language"><?php _e( 'Language', 'buddyboss-one-click' ); ?>:</td>
                    <td class="help"><?php echo $this->help_tip( __( 'The current language used by WordPress. Default = English', 'buddyboss-one-click' ) ); ?></td>
                    <td><?php echo esc_html( $environment['language'] ) ?></td>
                </tr>
            </tbody>
        </table>
        
        <table class="bboc_status_table widefat" cellspacing="0">
            <thead>
                <tr>
                    <th colspan="3" data-export-label="Server Environment"><h2><?php _e( 'Server environment', 'buddyboss-one-click' ); ?></h2></th>
                </tr>
            </thead>
            <tbody>
                <tr class="entry-row alternate">
                    <td data-export-label="Server Info"><?php _e( 'Server info', 'buddyboss-one-click' ); ?>:</td>
                    <td class="help"><?php echo $this->help_tip( __( 'Information about the web server that is currently hosting your site.', 'buddyboss-one-click' ) ); ?></td>
                    <td><?php echo esc_html( $environment['server_info'] ); ?></td>
                </tr>
                <tr>
                    <td data-export-label="PHP Version"><?php _e( 'PHP version', 'buddyboss-one-click' ); ?>:</td>
                    <td class="help"><?php echo $this->help_tip( __( 'The version of PHP installed on your hosting server.', 'buddyboss-one-click' ) ); ?></td>
                    <td><?php
                        if ( version_compare( $environment['php_version'], '5.6', '<' ) ) {
                            echo '<mark class="error"><span class="dashicons dashicons-warning"></span> ' . sprintf( __( '%1$s - We recommend a minimum PHP version of 5.6.', 'buddyboss-one-click' ), esc_html( $environment['php_version'] ) ) . '</mark>';
                        } else {
                            echo '<mark class="yes">' . esc_html( $environment['php_version'] ) . '</mark>';
                        }
                        ?></td>
                </tr>
                <?php if ( function_exists( 'ini_get' ) ) : ?>
                    <tr class="entry-row alternate">
                        <td data-export-label="PHP Post Max Size"><?php _e( 'PHP post max size', 'buddyboss-one-click' ); ?>:</td>
                        <td class="help"><?php echo $this->help_tip( __( 'The largest filesize that can be contained in one post.', 'buddyboss-one-click' ) ); ?></td>
                        <td><?php echo esc_html( size_format( $environment['php_post_max_size'] ) ) ?></td>
                    </tr>
                    <tr>
                        <td data-export-label="PHP Time Limit"><?php _e( 'PHP time limit', 'buddyboss-one-click' ); ?>:</td>
                        <td class="help"><?php echo $this->help_tip( __( 'The amount of time (in seconds) that your site will spend on a single operation before timing out (to avoid server lockups)', 'buddyboss-one-click' ) ); ?></td>
                        <td><?php echo esc_html( $environment['php_max_execution_time'] ) ?></td>
                    </tr>
                    
                    <tr class="entry-row alternate">
                        <td data-export-label="cURL Version"><?php _e( 'cURL version', 'buddyboss-one-click' ); ?>:</td>
                        <td class="help"><?php echo $this->help_tip( __( 'The version of cURL installed on your server.', 'buddyboss-one-click' ) ); ?></td>
                        <td><?php echo esc_html( $environment['curl_version'] ) ?></td>
                    </tr>
                <?php endif;
                
                if ( $wpdb->use_mysqli ) {
                    $ver = mysqli_get_server_info( $wpdb->dbh );
                } else {
                    $ver = mysql_get_server_info();
                }
                if ( ! empty( $wpdb->is_mysql ) && ! stristr( $ver, 'MariaDB' ) ) : ?>
                    <tr>
                        <td data-export-label="MySQL Version"><?php _e( 'MySQL version', 'buddyboss-one-click' ); ?>:</td>
                        <td class="help"><?php echo $this->help_tip( __( 'The version of MySQL installed on your hosting server.', 'buddyboss-one-click' ) ); ?></td>
                        <td>
                            <?php
                            if ( version_compare( $environment['mysql_version'], '5.6', '<' ) ) {
                                echo '<mark class="error"><span class="dashicons dashicons-warning"></span> ' . sprintf( __( '%1$s - We recommend a minimum MySQL version of 5.6. See: %2$s', 'buddyboss-one-click' ), esc_html( $environment['mysql_version'] ), '<a href="https://wordpress.org/about/requirements/" target="_blank">' . __( 'WordPress requirements', 'buddyboss-one-click' ) . '</a>' ) . '</mark>';
                            } else {
                                echo '<mark class="yes">' . esc_html( $environment['mysql_version'] ) . '</mark>';
                            }
                            ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td data-export-label="Max Upload Size"><?php _e( 'Max upload size', 'buddyboss-one-click' ); ?>:</td>
                    <td class="help"><?php echo $this->help_tip( __( 'The largest filesize that can be uploaded to your WordPress installation.', 'buddyboss-one-click' ) ); ?></td>
                    <td><?php echo size_format( $environment['max_upload_size'] ) ?></td>
                </tr>
                
                <tr class="entry-row alternate">
                    <td data-export-label="DOMDocument"><?php _e( 'DOMDocument', 'buddyboss-one-click' ); ?>:</td>
                    <td class="help"><?php echo $this->help_tip( __( 'HTML/Multipart emails use DOMDocument to generate inline CSS in templates.', 'buddyboss-one-click' ) ); ?></td>
                    <td><?php
                        if ( $environment['domdocument_enabled'] ) {
                            echo '<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>';
                        } else {
                            echo '<mark class="error"><span class="dashicons dashicons-warning"></span> ' . sprintf( __( 'Your server does not have the %s class enabled - HTML/Multipart emails, and also some extensions, will not work without DOMDocument.', 'buddyboss-one-click' ), '<a href="https://php.net/manual/en/class.domdocument.php">DOMDocument</a>' ) . '</mark>';
                        } ?>
                    </td>
                </tr>
                
                <tr>
                    <td data-export-label="Multibyte String"><?php _e( 'Multibyte string', 'buddyboss-one-click' ); ?>:</td>
                    <td class="help"><?php echo $this->help_tip( __( 'Multibyte String (mbstring) is used to convert character encoding, like for emails or converting characters to lowercase.', 'buddyboss-one-click' ) ); ?></td>
                    <td><?php
                        if ( $environment['mbstring_enabled'] ) {
                            echo '<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>';
                        } else {
                            echo '<mark class="error"><span class="dashicons dashicons-warning"></span> ' . sprintf( __( 'Your server does not support the %s functions - this is required for better character encoding.', 'buddyboss-one-click' ), '<a href="https://php.net/manual/en/mbstring.installation.php">mbstring</a>' ) . '</mark>';
                        } ?>
                    </td>
                </tr>
                
            </tbody>
        </table>
        
        <table class="bboc_status_table widefat" cellspacing="0">
            <thead>
                <tr>
                    <th colspan="3" data-export-label="Active Plugins (<?php echo count( $active_plugins ) ?>)"><h2><?php _e( 'Active plugins', 'buddyboss-one-click' ); ?> (<?php echo count( $active_plugins ) ?>)</h2></th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $alternate = true;
                foreach ( $active_plugins as $plugin ) {
                    if ( ! empty( $plugin['name'] ) ) {
                        // Link the plugin name to the plugin url if available.
                        $plugin_name = esc_html( $plugin['name'] );
                        if ( ! empty( $plugin['url'] ) ) {
                            $plugin_name = '<a href="' . esc_url( $plugin['url'] ) . '" aria-label="' . esc_attr__( 'Visit plugin homepage' , 'buddyboss-one-click' ) . '" target="_blank">' . $plugin_name . '</a>';
                        }

                        $network_string = '';
                        if ( false != $plugin['network_activated'] ) {
                            $network_string = ' &ndash; <strong style="color:black;">' . __( 'Network enabled', 'buddyboss-one-click' ) . '</strong>';
                        }
                        ?>
                        <tr <?php if( $alternate ){ echo "class='entry-row alternate'"; }?>>
                            <td><?php echo $plugin_name; ?></td>
                            <td class="help">&nbsp;</td>
                            <td><?php
                                /* translators: %s: plugin author */
                                printf( __( 'by %s', 'buddyboss-one-click' ), $plugin['author_name'] );
                                echo ' &ndash; ' . esc_html( $plugin['version'] ) . $network_string;
                            ?></td>
                        </tr>
                        <?php
                        $alternate = !$alternate;
                    }
                }
                ?>
            </tbody>
        </table>
        
        <style type="text/css">
            table.bboc_status_table {
                margin-bottom: 1em;
            }
                table.bboc_status_table h2 {
                    font-size: 14px;
                    margin: 0;
                }
                table.bboc_status_table th {
                    font-weight: 700;
                    padding: 9px;
                }
                table.bboc_status_table td.help {
                    width: 1em;
                    color: #ccc;
                }
                
            table.bboc_status_table mark{
                background: transparent none;
            }
            
                table.bboc_status_table td mark.yes {
                    color: #7ad03a;
                }
                
                table.bboc_status_table td mark.error {
                    color: #a00;
                }
        </style>
        <?php 
    }
    
    function return_bytes($val) {
        $val = trim($val);
        $last = strtolower($val[strlen($val)-1]);
        switch($last) {
            // The 'G' modifier is available since PHP 5.1.0
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }

        return $val;
    }
    
    function phpinfo_array( $mode = -1 ) { 
        ob_start(); 
        phpinfo( $mode ); 
        $s = ob_get_contents(); 
        ob_end_clean(); 
        $a = $mtc = array(); 
        if (preg_match_all('/<tr><td class="e">(.*?)<\/td><td class="v">(.*?)<\/td>(:?<td class="v">(.*?)<\/td>)?<\/tr>/',$s,$mtc,PREG_SET_ORDER)) {
            foreach($mtc as $v){ 
                if($v[2] == '<i>no value</i>') continue; 
                $master = '';
                if ( isset( $v[3] ) ) {
                    $master = strip_tags( $v[3] );
                }
                $a[$v[1]] = array( $v[2], $master );
            } 
        } 
        return $a; 
    } 
}