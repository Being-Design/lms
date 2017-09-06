<?php

class USIN_List_Ajax extends USIN_Ajax{

	protected $user_capability;
	protected $options;
	protected $nonce_key;

	public function __construct($options, $user_capability, $nonce_key){
		$this->options = $options;
		$this->user_capability = $user_capability;
		$this->update_user_capability = USIN_Capabilities::UPDATE_USERS;
		$this->export_users_capability = USIN_Capabilities::EXPORT_USERS;
		$this->nonce_key = $nonce_key;
	}

	public function add_actions(){
		add_action('wp_ajax_usin_get_users', array($this, 'get_users'));
		add_action('wp_ajax_usin_get_user_data', array($this, 'get_user_data'));
		add_action('wp_ajax_usin_update_list_opion', array($this, 'update_list_opion'));
		add_action('wp_ajax_usin_get_coordinates', array($this, 'get_coordinates'));
		add_action('wp_ajax_usin_export_link', array($this, 'get_export_link'));
		add_action('wp_ajax_usin_update_groups', array($this, 'update_groups'));
		add_action('wp_ajax_usin_update_groups_bulk', array($this, 'update_groups_bulk'));
		add_action('wp_ajax_usin_add_note', array($this, 'add_note'));
		add_action('wp_ajax_usin_delete_note', array($this, 'delete_note'));
		add_action('wp_ajax_usin_update_user_field', array($this, 'update_user_field'));
	}

	public function get_users(){
		if(!$this->validate_request(true)){
			exit;
		}
		
		$filters = $this->get_request_array('filters');

		$args = array(
			'number' => $_GET['users_per_page'],
			'orderby' => $_GET['orderby'],
			'order' => $_GET['order']
		);

		if(isset($_GET['page']) && $_GET['page']!=1){
			$args['offset'] = $this->get_query_offset($_GET['page'], $args['number']);
		}

		$user_query = new USIN_User_Query($args, $filters);

		$users = $user_query->get_users();

		echo json_encode($users);
		exit;
	}

	public function get_user_data(){
		if(!$this->validate_request(true)){
			exit;
		}
		
		if(isset($_GET['userid'])){
			$user_id = intval($_GET['userid']);

			$user_query = new USIN_User_Query();
			$user = $user_query->get_user($user_id);

			if(!empty($user)){
				$user->set_profile_data();
				$user = apply_filters('usin_user_profile_data', $user);
				echo json_encode($user);
			}else{
				echo json_encode(array('error'=>__('No data found for this user', 'usin')));
			}
			
			exit;
		}
	}

	public function get_coordinates(){
		if(!$this->validate_request(true)){
			exit;
		}
		
		$filters = $this->get_request_array('filters');

		$user_query = new USIN_Coordinates_Query(array(), $filters);

		$coordinates = $user_query->get_coordinates();

		echo json_encode($coordinates);
		exit;
	}

	public function update_list_opion(){
		if(!$this->validate_request(true)){
			exit;
		}

		$res = false;

		if(isset($_POST['option_key']) && isset($_POST['option_val'])){
			$res = $this->options->update_user_option($_POST['option_key'], $_POST['option_val']);
		}

		echo json_encode(array('success'=>$res));
		exit;
		
	}
	
	public function update_groups(){
		if(!$this->validate_request(true, $this->update_user_capability)){
			exit;
		}
		
		$res = false;
		$user_id = $_POST['user'];
		$groups = isset($_POST['groups']) ? $_POST['groups'] : array();
		
		if(!empty($user_id) && is_numeric($user_id) && is_array($groups)){
			$groups = $this->array_values_to_integer($groups);
			$res = USIN_Groups::update_user_groups($user_id, $groups);
		}
		
		echo json_encode(array('success'=>$res));
		exit;
	}
	
	public function update_groups_bulk(){
		$this->verify_request($this->update_user_capability);
		$this->validate_required_post_params(array('users', 'groupId', 'bulkAction'));
		
		$users = $_POST['users'];
		$group_id = intval($_POST['groupId']);
		$action = $_POST['bulkAction'];
		
		$res = false;
		if(is_array($users) && $group_id && in_array($action, array('add', 'remove'))){
			$users = $this->array_values_to_integer($users);
			$res = USIN_Groups::update_user_groups_bulk($users, $group_id, $action);
		}
		
		$this->respond($res);
	}
	
	public function add_note(){
		if(!$this->validate_request(true, $this->update_user_capability)){
			exit;
		}
		
		$res = false;
		$user_id = $_POST['user'];
		$note_content = $_POST['note'];
		$response = array();
		
		if(!empty($user_id) && is_numeric($user_id) && !empty($note_content)){
			$user_id = intval($user_id);
			$res = USIN_Note::create($user_id, $note_content);
			if($res){
				$res = true;
				$all_notes = USIN_Note::get_all($user_id);
				$response['notes'] = $all_notes;
			}
		}
		
		$response['success'] = $res;
		echo json_encode($response);
		exit;
	}
	
	public function delete_note(){
		if(!$this->validate_request(true, $this->update_user_capability)){
			exit;
		}
		
		$res = false;
		$note_id = $_POST['note_id'];
		if(!empty($note_id) && is_numeric($note_id)){
			$note_id = intval($note_id);
			$note = new USIN_Note($note_id);
			$res = $note->delete();
		}
		
		echo json_encode(array('success'=>$res));
		exit;
	}
	
	public function update_user_field(){
		if(!$this->validate_request(true, $this->update_user_capability)){
			exit;
		}
		
		$success = false;
		
		if(isset($_POST['user']) && isset($_POST['field_id'])){
			$user_id = (int)$_POST['user'];
			$field_id = $_POST['field_id'];
			$field_value = isset($_POST['field_value']) ? $_POST['field_value'] : '';
			
			$field = $this->options->get_field_by_id($field_id);
			if(!empty($field)){
				$usin_field = new USIN_Field($field);
				$success = $usin_field->update_value_for_user($user_id, $field_value);
			}
			
		}

		echo json_encode(array('success'=>$success));
		exit;
	}

	public function get_export_link(){
		if(!$this->validate_request(true, $this->export_users_capability)){
			exit;
		}

		$filters = $this->get_request_array('filters');
		$fields = $this->get_request_array('fields');
		$orderby = $_GET['orderby'];
		$order = $_GET['order'];
		$data = array('filters'=>$filters, 'fields'=>$fields, 'orderby'=>$orderby, 'order'=>$order);

		$transient_key = 'usin_export_'.time(); //generate unique key that will be used
		//to access the export file

		if(set_transient($transient_key, $data, 600)){
			$url = add_query_arg(array(
				'usin_export' => $transient_key,
				'nonce' => $this->get_nonce()
				), admin_url());
			$res = array('success'=>true, 'export_url'=>$url);
		}else{
			$res = array('success'=>false);
		}

		echo json_encode($res);
		exit;
	}

	protected function get_query_offset($page, $items_per_page){
		return ($page - 1) * $items_per_page;
	}
	

}