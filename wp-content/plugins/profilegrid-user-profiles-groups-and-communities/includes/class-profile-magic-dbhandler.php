<?php
class PM_DBhandler
{
	
    public function insert_row($identifier, $data, $format = NULL )
    {
        global $wpdb;
        $pm_activator = new Profile_Magic_Activator;
        $table = $pm_activator->get_db_table_name($identifier);
        $result = $wpdb->insert($table,$data,$format);

        if ($result !== false)
            return $wpdb->insert_id;
        else
            return false;
    }

    public function update_row($identifier, $unique_field, $unique_field_value,$data,$format = NULL, $where_format = NULL)
    {
        global $wpdb;
        $pm_activator = new Profile_Magic_Activator;
        $table = $pm_activator->get_db_table_name($identifier);
        if ($unique_field === false)
            $unique_field = $pm_activator->get_db_table_unique_field_name ($identifier);

        $result = $wpdb->get_row("SELECT * from `$table` where $unique_field = $unique_field_value");
        if ($result === NULL)
            return false;
			
		$where = array($unique_field => $unique_field_value);
        return $wpdb->update($table,$data,$where,$format,$where_format);
		
    }

    public function remove_row($identifier, $unique_field, $unique_field_value, $where_format = NULL )
    {
        global $wpdb;
        $pm_activator = new Profile_Magic_Activator;
        $table = $pm_activator->get_db_table_name($identifier);
        if ($unique_field === false)
			$unique_field = $pm_activator->get_db_table_unique_field_name ($identifier);
        $result = $wpdb->get_row("SELECT * from `$table` where $unique_field = $unique_field_value");
        if ($result === NULL)
            return false;
			
		$where = array($unique_field => $unique_field_value);
        return $wpdb->delete($table,$where,$where_format);
    }
	

    public function get_row($identifier, $unique_field_value,$unique_field=false,$output_type='OBJECT')
    {
        global $wpdb;
        $pm_activator = new Profile_Magic_Activator;
        $table = $pm_activator->get_db_table_name($identifier);
     
        if ($unique_field === false)
           $unique_field = $pm_activator->get_db_table_unique_field_name ($identifier);

        return $wpdb->get_row("SELECT * from `$table` where $unique_field = $unique_field_value",$output_type);
    }
	
	public function get_value($identifier,$field,$unique_field_value,$unique_field=false)
    {
        global $wpdb;
        $pm_activator = new Profile_Magic_Activator;
        $table = $pm_activator->get_db_table_name($identifier);
     
        if ($unique_field === false)
           $unique_field = $pm_activator->get_db_table_unique_field_name ($identifier);

        return $wpdb->get_var("SELECT $field from `$table` where $unique_field = $unique_field_value");
    }
	
	
    public function get_all_result($identifier, $column = '*', $where = 1, $result_type = 'results', $offset = 0, $limit = false, $sort_by = null, $descending = false,$additional='',$output='OBJECT')
    {
        global $wpdb;
        $pm_activator = new Profile_Magic_Activator;
        $table = $pm_activator->get_db_table_name($identifier);
	$unique_id_name = $pm_activator->get_db_table_unique_field_name($identifier);
        $args = array();
        if (!$sort_by) 
        {
            $sort_by = $unique_id_name;
        }

        $qry = "SELECT $column FROM `$table` WHERE";

        if (is_array($where))
        {
            $i = 0;
            foreach ($where as $column_name => $column_value)
            {
                if ($i !== 0)
                    $qry .= " AND";
				$format = $pm_activator->get_db_table_field_type($identifier,$column_name);
                $qry .= " `$column_name` = $format";
                $args[] = $column_value;
                $i++;
            }
			if($additional!='')
			{
				$qry .= ' '.$additional;
			}	
        }
        elseif($where == 1){
            if($additional!='')
            {
                    $qry .= ' '.$additional;
            }  
            else 
            {
                $qry .= " 1";
            }
            
        }
        
        if ($descending === false)
        {
            $qry .= " ORDER BY `$sort_by`";
        } else
        {
            $qry .= " ORDER BY `$sort_by` DESC";
        }
		
		if($limit===false)
		{
            $qry .= "";
        } else
        {
            $qry .= " LIMIT $limit OFFSET $offset";
        }
       
        if ($result_type === 'results' || $result_type === 'row' || $result_type === 'var')
        {
            $method_name = 'get_' . $result_type;
            if(count($args) === 0)
            {   if($result_type === 'results'):
                    $results = $wpdb->$method_name($qry,$output);
                else:
                      $results = $wpdb->$method_name($qry);
                endif;
                    
            }
            else
            {
                if($result_type === 'results'):
                    $results = $wpdb->$method_name($wpdb->prepare($qry, $args),$output);
                else:
                    $results = $wpdb->$method_name($wpdb->prepare($qry, $args));
                endif;
            }
        } 
        else
        {
            return null;
        }

        if (is_array($results) && count($results)===0)
        {
            return null;
        }

        return $results;
    }
	
    public function pm_count($identifier, $where=1, $data_specifiers='')
    {
        global $wpdb;
        $pm_activator = new Profile_Magic_Activator;
        $table_name = $pm_activator->get_db_table_name($identifier);
        if($data_specifiers=='')
        {
            $unique_id_name = $pm_activator->get_db_table_unique_field_name($identifier);
            if ($unique_id_name === false){return false;}
        }
        else
        {
            $unique_id_name = $data_specifiers;	
        }
        
        $qry = "SELECT COUNT($unique_id_name) FROM $table_name WHERE ";
        
        if (is_array($where))
        {
            $i =0;
            foreach ($where as $column_name => $column_value)
            {
                if($i!=0)
                {
                    $qry .= "AND ";
                }
                $qry .= "`$column_name` = $column_value ";
            }
            
        } elseif($where == 1)
        {
            $qry .= "1 ";
        }

        $count = $wpdb->get_var($qry);

        if ($count === null)
        {
            return false;
        }
        return (int) $count;
    }
    
	public function pm_add_user($user_name,$password,$user_email,$user_role='subscriber')
	{
		if(is_multisite())
		{
			$blog_id = get_current_blog_id();
			if(email_exists($user_email))
			{
				 $user_id = email_exists($user_email);
				 if(!is_user_member_of_blog($user_id, $blog_id))
				 add_user_to_blog( $blog_id,$user_id,$user_role);		
			}
			else
			{
				$user_id = wp_create_user( $user_name, $password, $user_email );//Creates new WP user after successful registration
				if(is_numeric($user_id))
                                {
                                    $user_id = wp_update_user( array( 'ID' => $user_id, 'role' => $user_role ));
                                    if(!is_user_member_of_blog($user_id, $blog_id))
                                    add_user_to_blog( $blog_id,$user_id,$user_role);
                                }
			}
		}
		else
		{
			$user_id = wp_create_user( $user_name, $password, $user_email );//Creates new WP user after successful registration
			if(is_numeric($user_id))
                        {
                            $user_id = wp_update_user( array( 'ID' => $user_id, 'role' => $user_role ));
                        }
		}
		return $user_id;	
	}
	
    public function get_global_option_value($option,$default='')
    {
            $value =  get_option( $option, $default );
            if(!isset($value) || $value=='')
            {
                $value = $default;
            }
            return $value;
    }

    public function update_global_option_value($option,$value)
    {
            update_option( $option, $value );
    }
	
	public function pm_get_all_users($search='',$meta_query=array(),$role='',$offset='',$limit='',$order='ASC',$orderby='ID',$exclude=array(),$datequery=array(),$include=array())
	{
		$args = array (
			'order' => $order,
			'orderby' => $orderby
		);
		if($offset!='')
		{
			$args['offset'] = $offset;	
		}
		if($limit!='')
		{
			$args['number'] = $limit;	
		}
		if($role!='')
		{
			$args['role'] = $role;	
		}
		if($search!='')
		{
			$args['search'] = '*'.esc_attr($search).'*';	
		}
		if($role!='')
		{
			$args['role'] = $role;	
		}
		if(!empty($meta_query))
		{
			$args['meta_query'] = $meta_query;	
		}
		if(!empty($exclude))
		{
			$args['exclude'] = $exclude;	
		}
		if(!empty($include))
		{
			$args['include'] = $include;	
		}
		if(!empty($datequery))
		{
			$args['date_query'] = $datequery;	
		}
               // echo '<pre>';
		//print_r($args);
                //echo '</pre>';
                //die;
		$users = get_users($args);
		return $users;
	}
	
	public function pm_get_pagination($num_of_pages,$pagenum,$base='')
	{
		if($pagenum=="")$pagenum=1;
                if($base=='')$base = add_query_arg( 'pagenum', '%#%' );
		$args = array(
		'base'               => $base,
		'format'             => '',
		'total'              => $num_of_pages,
		'current'            => $pagenum,
		'show_all'           => false,
		'end_size'           => 1,
		'mid_size'           => 2,
		'prev_next'          => true,
		'prev_text'          => __('&laquo;', 'profile-magic' ),
		'next_text'          => __('&raquo;', 'profile-magic'),
		'type'               => 'list',
		'add_args'           => false,
		'add_fragment'       => '',
		'before_page_number' => '',
		'after_page_number'  => '' );
	
		$page_links = paginate_links( $args );
		return $page_links;
		
	}
    

}
