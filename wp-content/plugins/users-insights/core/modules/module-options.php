<?php

/**
 * Includes the main Options functionality for the Modules page.
 */
class USIN_Module_Options{
	
	protected $modules = array();
	protected $prefix = 'usin_module_';

	protected static $instance;

	protected function __construct(){}

	/**
	 * This is a singleton class, returns the instance of the class.
	 * @return USIN_Module_Options the instance
	 */
	public static function get_instance(){
		if(! self::$instance ){
			self::$instance = new USIN_Module_Options();
			self::$instance->init();
		}
		return self::$instance;
	}

	/**
	 * Registers the required action hooks.
	 */
	protected function init(){
		//load the options
		add_action('admin_init', array($this, 'get_module_options'));
	}

	/**
	 * Returns the Module Options. Uses a cached version if they have been loaded
	 * once.
	 * @return array the array with the module options.
	 */
	public function get_module_options(){
		if(empty($this->modules)){
			$this->load_module_options();
		}
		return $this->modules;
	}

	/**
	 * Loads the Module Options and sets them to the modules property.
	 */
	protected function load_module_options(){
		$modules = USIN_Module_Default_Options::get();
		
		foreach ($modules as $module) {
			$saved_options = $this->get_saved_module_options($module['id']);

			if(isset($saved_options['active'])){
				$active = $saved_options['active'];
			}elseif(isset($module['active'])){
				$active = $module['active'];
			}else{
				$active = false;
			}

			$module['options'] = $saved_options;
			$module['active'] = $active;
			$module['has_options'] = (isset($module['license']) && !isset($module['uses_module_license'])) || isset($module['option_fields']);

			$this->setup_license_data($module);

			$this->modules[] = $module;
		}

		do_action('usin_module_options_loaded');
	}
	
	public function refresh_module_options(){
		$this->modules = array();
		$this->load_module_options();
	}

	/**
	 * Activates a module.
	 * @param  string $module_id the ID of the module to activate
	 */
	public function activate_module($module_id){
		$module = &$this->get_module_by_id($module_id);
		if($module){
			$module['options']['active'] = true;
			$this->save_module_options($module_id, $module['options']);
			do_action('usin_module_activated', $module_id);
		}
	}

	/**
	 * Deactivates a module.
	 * @param  string $module_id the ID of the module to deactivate
	 */
	public function deactivate_module($module_id){
		$module = &$this->get_module_by_id($module_id);
		if($module && $module['allow_deactivate']){
			$module['options']['active'] = false;
			$this->save_module_options($module_id, $module['options']);
			do_action('usin_module_deactivated', $module_id);
		}
	}

	/**
	 * Loads the saved license data for a module, such as expiry date and text.
	 * If no data is saved, an empty array will be set as license data. The data
	 * is directly set to the module array.
	 * @param  array &$module the module for which the data will be loaded
	 */
	protected function setup_license_data(&$module){
		if(isset($module['license'])){
			if(isset($module['options']['license'])){
				$lc_options = &$module['options']['license'];

				if($module['license']['type']=='service' && isset($lc_options['activated']) && $lc_options['activated']===true) {
					
					if(empty($lc_options['status'])){
						//set a default value for backwards compatibility
						$lc_options['status'] = 'valid';
					}
					
					$status = USIN_Module_License::get_license_status($lc_options['expires'], $lc_options['status']);
					$lc_options['status_text'] = $status['status_text'];
					$lc_options['status'] = $status['status'];
					
				}
			}else{
				$module['options']['license'] = array();
			}
		}
	}

	/**
	 * Retrieves the license key for a module.
	 * @param  string $module_id the module ID
	 * @return string            the lincense key
	 */
	public function get_license($module_id){
		$module = $this->get_module_by_id($module_id);

		if(!empty($module)){
			if(isset($module['uses_module_license'])){
				$dep_module_license = $this->get_license($module['uses_module_license']);
				if($dep_module_license){
					return $dep_module_license;
				}
			}
			if(isset($module['options']['license']['key'])){
				return $module['options']['license']['key'];
			}
		}
	}

	/**
	 * Retrieves the saved options for a module.
	 * @param  string $module_id the module ID
	 * @return array            the saved module options if any, or an empty
	 * array otherwise
	 */
	protected function get_saved_module_options($module_id){
		$option = get_option($this->prefix.$module_id);
		if(!empty($option)){
			return $option;
		}
		return array();
	}


	/**
	 * Retrieves the module data for a selected module.
	 * @param  string $module_id the module ID
	 * @return array            the module data
	 */
	public function &get_module_by_id($module_id){
		if(empty($this->modules)){
			$this->load_module_options();
		}

		foreach ($this->modules as $option) {
			if($option['id'] === $module_id){
				return $option;
			}
		}
		
		$res = null;
		return $res;
	}

	/**
	 * Saves the options for a selected module.
	 * @param  string $module_id the module ID
	 * @param  array $options   the options to save
	 */
	public function save_module_options($module_id, $options){
		$options = $this->prepare_options_for_save($options);
		update_option( $this->prefix.$module_id, $options );
	}
	
	/**
	 * Removes some of the fields that shouldn't be stored
	 * @param  array $options the module options
	 * @return array          
	 */
	protected function prepare_options_for_save($options){
		if(isset($options['license']) && isset($options['license']['status_text'])){
			unset($options['license']['status_text']);
		}
		return $options;
	}

	/**
	 * After activating a license, sets the activation details in the module options.
	 * @param string $module_id   the module ID
	 * @param string $license_key the license key
	 * @param string $item_name   the name of the product item for which the license has been activated
	 * @param string $expires     expiry date
	 */
	public function set_license_active($module_id, $license_key, $item_name, $expires){
		$module = &$this->get_module_by_id($module_id);

		if(!empty($module)){
			if(!isset($module['options']['license'])){
				$module['options']['license'] = array();
			}

			$ls_options = &$module['options']['license'];

			$ls_options['key'] = $license_key;
			$ls_options['activated'] = true;
			$ls_options['expires'] = $expires;
			$ls_options['status'] = 'valid'; //!!! new field since 3.0

			$license_free = isset($module['license']['free_name']) && $item_name === $module['license']['free_name'];

			$ls_options['is_free'] = $license_free;

			if(!isset($ls_options['free_activated']) && $license_free){
				//save this only once and never change it
				$ls_options['free_activated'] = true;
			}

			$this->save_module_options($module_id, $module['options']);
			$this->setup_license_data($module);

			return $module['options'];
		}
	}
	
	
	public function update_license_status($module_id, $status, $expires){
		$module = &$this->get_module_by_id($module_id);
		
		if(!empty($module) && isset($module['options']) && !empty($module['options']['license'])){
			$module['options']['license']['expires'] = $expires;
			$module['options']['license']['status'] = $status;
			
			$this->save_module_options($module_id, $module['options']);
			$this->setup_license_data($module);
		}
		
		return $module['options'];
	}

	/**
	 * After deactivating a license, removes the activation details in the module options.
	 * @param string $module_id   the module ID
	 * @param string $license_key the license key
	 * @return array the module options
	 */
	public function set_license_inactive($module_id, $license_key){
		$module = &$this->get_module_by_id($module_id);

		if(!empty($module)){
			$ls_options = &$module['options']['license'];

			$ls_options['activated'] = false;
			$ls_options['key'] = '';
			$ls_options['expires'] = '';
			$ls_options['is_free'] = null;
			$ls_options['status'] = 'inactive'; //!!! new field since 3.0

			$this->save_module_options($module_id, $module['options']);

			return $module['options'];
		}
	}

	/**
	 * Checks whether a module is activated.
	 * @param  string  $module_id the module ID
	 * @return boolean            true if the module is activated and false otherwise
	 */
	public function is_module_active($module_id){
		$module = $this->get_module_by_id($module_id);
		if($module && $module['active']){
			return true;
		}
		return false;
	}

}