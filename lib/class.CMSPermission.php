<?php
	/*		
		Author: Jean-Christophe Cuvelier <cybertotophe@gmail.com>
		Copyrights: Jean-Christophe Cuvelier - Morris & Chapman Belgium - 2010
		Licence: GPL		
	
	*/

class CMSPermission
{
	protected $id;
	protected $is_modified = false;
	protected $vars = array();
	
	protected $users;
	
	protected static $fields = array(
	'name',
	'is_active',
	'comments'
	);
	
	const DB_NAME = 'module_cmsusers_permissions';
	const DB_ITEM = 'permission';
	
	// Custom logic
	
	public function __toString()
	{
		return $this->name;
	}
	
	public static function getPermissionList()
	{
		$permissions = self::doSelect(array('order_by' => array('name')));
		$array = array();
		foreach($permissions as $permission)
		{
			$array[$permission->getId()] = (string)$permission;
		}
		return $array;
	}
	
	public static function getPermissionsForUser($user_id)
	{
		if (!is_numeric($user_id)) die ('User id must be a numerical value !');
		$db = cms_utils::get_db();

		 $query = 'SELECT '.self::DB_ITEM.'.*, '.self::generateSelectList() . '
			FROM ' . cms_db_prefix() . self::DB_NAME . ' AS ' . self::DB_ITEM .'
			LEFT JOIN ' . cms_db_prefix() . CMSGroupPermission::DB_NAME . ' AS '.CMSGroupPermission::DB_ITEM.' 
			ON '.self::DB_ITEM.'.id = '.CMSGroupPermission::DB_ITEM.'.permission_id
			LEFT JOIN ' . cms_db_prefix() . CMSGroup::DB_NAME . ' AS '.CMSGroup::DB_ITEM.' 
			ON '.CMSGroupPermission::DB_ITEM.'.group_id = '.CMSGroup::DB_ITEM.'.id
			LEFT JOIN ' . cms_db_prefix() . CMSUserGroup::DB_NAME . ' AS '.CMSUserGroup::DB_ITEM.' 
			ON '.CMSGroup::DB_ITEM.'.id = '.CMSUserGroup::DB_ITEM.'.group_id
			WHERE 
			'.self::DB_ITEM.'.is_active = 1 AND
			'.CMSGroup::DB_ITEM.'.is_active = 1 AND
			'.CMSUserGroup::DB_ITEM.'.user_id = ?
			GROUP BY '.self::DB_ITEM.'.id
			';
			
			$values = array($user_id);
			$dbresult = $db->Execute($query, $values);
			
			$permissions = array();
			
			if ($dbresult && $dbresult->RecordCount() > 0)
	    {
	      while ($dbresult && $row = $dbresult->FetchRow())
	      {	
	        $item = new self();
	        $item->populate($row);
	        $permissions[$row[self::getRowName('id')]] = $item->name;
	      }
	    }
		return $permissions;
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
	}
	
	public static function retrieveByPk($id)
  	{
  		return self::doSelectOne(array('where' => array('id' => $id)));    
  	}

  	public static function doSelectOne($params = array())
  	{
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

    $query = 'SELECT 
		'.self::DB_ITEM.'.*,
		'.self::generateSelectList() . ' 
		FROM ' . cms_db_prefix() . self::DB_NAME . ' AS ' . self::DB_ITEM;

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
        $item = new self();
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
			if($result !== false) 
			{
				return true;
			}
		}
		return false;
	}
}