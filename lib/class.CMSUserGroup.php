<?php

	/*
		Handle users
		
		Author: Jean-Christophe Cuvelier <cybertotophe@gmail.com>
		Copyrights: Jean-Christophe Cuvelier - Morris & Chapman Belgium - 2010
		Licence: GPL		
	
	*/

class CMSUserGroup
{
	protected $id;
	protected $is_modified = false;
	protected $vars = array();
	
	protected $users;
	protected $groups;	
	
	protected $user;
	protected $group;
	
	protected static $fields = array(
	'group_id',
	'user_id'
	);
	
	const DB_NAME = 'module_cmsusers_usergroups';
	const DB_ITEM = 'usergroup';
	
	// Specific module logic
	
	public static function setGroupUsers($group_id, Array $users)
	{
		$current_users = self::getUsersList($group_id);

		// 1. Delete users
		$delete_users = (array_diff_key($current_users,array_flip($users)));
		foreach($delete_users as $user)
		{
			$user->delete();
		}
		
		// 2. Add users
		$add_users = (array_diff_key(array_flip($users), $current_users));
				
		foreach($add_users as $user_id => $user)
				{
					$new = new self();
					$new->user_id = $user_id;
					$new->group_id = $group_id;
					$new->save();
				}
	}
	
	public static function addGroupUser($group_id, $user_id)
	{
		$rel = new self();
		$rel->user_id = $user_id;
		$rel->group_id = $group_id;
		return $rel->save();
	}
	
	public static function getUsersList($group_id, $only_keys = false)
	{
		$users = self::doSelect(array('where' => array('group_id' => $group_id)));
		$array = array();
		foreach($users as $user)
		{
			if ($only_keys == true)
			{
				$array[] = $user->user_id;
			}
			else
			{
				$array[$user->user_id] = $user;
			}
		}
		return $array;
	}
	
	public static function setUserGroups($user_id, Array $groups)
	{
		$current_groups = self::getGroupsList($user_id);

		// 1. Delete groups
		$delete_groups = (array_diff_key($current_groups,array_flip($groups)));
		foreach($delete_groups as $group)
		{
			$group->delete();
		}
		
		// 2. Add users
		$add_groups = (array_diff_key(array_flip($groups), $current_groups));
				
		foreach($add_groups as $group_id => $group)
				{
					$new = new self();
					$new->user_id = $user_id;
					$new->group_id = $group_id;
					$new->save();
				}
	}
	
	public static function getGroupsList($user_id, $only_keys = false)
	{
		$groups = self::doSelect(array('where' => array('user_id' => $user_id)));
		$array = array();
		foreach($groups as $group)
		{
			if ($only_keys == true)
			{
				$array[] = $group->group_id;
			}
			else
			{
				$array[$group->group_id] = $group;
			}
		}
		return $array;
	}
	
	public static function deleteUserPrefs($user_id)
	{
		$list = self::doSelect(array('where' => array('user_id' => $user_id)));
		foreach($list as $item)
		{
			$item->delete();
		}
	}
	
	public static function deleteGroupPrefs($group_id)
	{
			$list = self::doSelect(array('where' => array('group_id' => $group_id)));
			foreach($list as $item)
			{
				$item->delete();
			}
	}
	
	public function getGroup()
	{
		if (!is_object($this->group))
		{
			$this->group = CMSGroup::retrieveByPk($this->group_id);
		}
		return $this->group;
	}
	
	public function getUser()
	{
		if (!is_object($this->user))
		{
			$this->user = CMSUser::retrieveByPk($this->user_id);
		}
		return $this->user;
	}
	
	// GETSETTERS
	
	public function getId()
	{
		return $this->id;
	}
	
	protected function setId($value)
	{
		$this->id = $value;
	}
	
    public function __set($var, $val){
		$this->is_modified = true;
        $this->vars[$var] = $val;
    }

    public function __get($var){
		try
		{
			if(method_exists($this, $var))
			{
				return $this->$var();
			}
	        elseif (array_key_exists($var, $this->vars))
			{
	            return $this->vars[$var];
	        } else {
	            //throw new Exception("Property $var does not exist");
	        }
		}
		catch(Exception $e)
		{
			echo 'Error: ',  $e->getMessage(), "\n";
		}
   }
	
	// DB
	
	public function populate($row)
	{
		if (isset($row[self::DB_ITEM.'__id']))
		{
			$this->setId($row[self::DB_ITEM.'__id']);
		}		
		
		foreach (self::$fields as $field)
		{
			if (isset($row[self::DB_ITEM.'__'.$field]))
			{
				$this->$field = $row[self::DB_ITEM.'__'.$field];
			}
		}
		
		$this->user = new CMSUser();
		$this->user->populate($row);
		$this->group = new CMSGroup();
		$this->group->populate($row);
	}
	
	public static function retrieveByPk($id)
  	{
  		return self::doSelectOne(array('where' => array('id' => $id)));    
  	}

  	public static function doSelectOne($params = array())
  	{
  	  $params['limit'] = 1;
  		$items = self::doSelect($params);
  		if ($items)
  		{
  			return $items[0];
  		}
  		else 
  		{
  			return null;
  		}  	
 	}

	public static function generateSelectList()
	{
		$fields = array_merge(array('id','created_at','updated_at'), self::$fields);
		$list = array();
		foreach ($fields as $field)
		{
			$list[] = self::DB_ITEM . '.'.$field.' as '. self::getRowName($field);
		}
		return implode(' , ',$list);
	}

  public static function getRowName($name)
	{
		return self::DB_ITEM . '__' . $name;
	}


  public static function doSelect($params = array())
  {
    $db = cms_utils::get_db();

    $query = 'SELECT '
		. self::DB_ITEM.'.*, '
		. self::generateSelectList() . ', '
		. CMSGroup::DB_ITEM . '.*, '
		. CMSGroup::generateSelectList() . ', '
		. CMSUser::DB_ITEM . '.*, '
		. CMSUser::generateSelectList()		
		. ' FROM ' . cms_db_prefix() . self::DB_NAME . ' AS ' . self::DB_ITEM
		. ' LEFT JOIN ' . cms_db_prefix() . CMSGroup::DB_NAME . ' AS ' . CMSGroup::DB_ITEM
		. ' ON ' . self::DB_ITEM . '.group_id = ' . CMSGroup::DB_ITEM .'.id'
		. ' LEFT JOIN ' . cms_db_prefix() . CMSUser::DB_NAME . ' AS ' . CMSUser::DB_ITEM
		. ' ON ' . self::DB_ITEM . '.user_id = ' . CMSUser::DB_ITEM . '.id'
		;

    $values = array();

    if (isset($params['where']))
    {

      $fields = array();
      foreach ($params['where'] as $field => $value) 
      {
        $fields[] = self::DB_ITEM.'.'.$field . ' =  ?';
        $values[] = $value;
      }

      $query .= ' WHERE ' . implode(' AND ', $fields);
    } 

    if(isset($params['order_by']))
    {
     $query .= ' ORDER BY ' . implode(', ' , $params['order_by']);
    }
    else
    {
      $query .= ' ORDER BY '.self::DB_ITEM.'.created_at';
    }

    $dbresult = $db->Execute($query, $values);
    $items = array();

   if ($dbresult && $dbresult->RecordCount() > 0)
    {
      while ($dbresult && $row = $dbresult->FetchRow())
      {	
        $item = new CMSUserGroup();
        $item->populate($row);
        $items[] = $item;
      }
    }

    return  $items;   
  }
  
	
	public function save() {
		if ($this->getId()) 
		{
			if ($this->is_modified)
			{			
				return $this->update();	
			}
		} 
		else
		{
			return $this->insert();
		}
		
		return false;
	}

	protected function insert() {
		//
    $db = cms_utils::get_db();
		$query = 'INSERT INTO '.cms_db_prefix(). self::DB_NAME . '
			SET created_at = NOW(),
				updated_at = NOW()
								';
		$values = array();				
		foreach(self::$fields as $field)
		{
			$query .= ', ' . $field . ' = ?';
			$values[] = $this->$field;
		}
																
		$result = $db->Execute($query,$values);
		if ($result === false) return false;
		$this->setId($db->Insert_ID());
		
		return true;
	}

	protected function update() {
    $db = cms_utils::get_db();
		$query = 'UPDATE '.cms_db_prefix(). self::DB_NAME . '
			SET updated_at = NOW()';
			
		$values = array();				
		foreach(self::$fields as $field)
		{
			$query .= ', ' . $field . ' = ?';
			$values[] = $this->$field;
		}
	
		$query .= ' WHERE id = ?';
		$values[] = $this->getId();

		$result = $db->Execute($query, $values);
		if ($result === false) return false;
		
		return true;
	}
	
	public function delete() {
		if ($this->getId()) {
    	$db = cms_utils::get_db();
			$query = 'DELETE FROM '.cms_db_prefix().self::DB_NAME . ' WHERE id = ?';
			$result = $db->Execute($query, array($this->getId()));
			if($result !== false) return true;
		}
		return false;
	}
}