<?php

	/*
		Handle profiles
		
		Author: Jean-Christophe Cuvelier <cybertotophe@gmail.com>
		Copyrights: Jean-Christophe Cuvelier - Morris & Chapman Belgium - 2010
		Licence: GPL		
	
	*/

class CMSProfile
{
	protected $id;
	protected $is_modified = false;
	protected $vars = array();
	
	protected static $fields = array(
	'user_id'
	);
	
	protected $extra_fields = array();
	
	const DB_NAME = 'module_cmsusers_profiles';
	const DB_ITEM = 'profile';
	
	// Custom class logic
	
	
	public static function createField($fieldname, $type)
	{
    $db = cms_utils::get_db();
		$dict = NewDataDictionary($db);
		$sqlarray = $dict->AddColumnSQL(cms_db_prefix() . self::DB_NAME, $fieldname . ' ' . CMSFormWidget::getAdoType($type));
		$dict->ExecuteSQLArray($sqlarray);
	}
	
	public function getUserProfileFields()
	{
		return CMSProfileField::getFieldsForUser($this->user_id);
	}
	
	public static function getFields()
	{
		$fields = self::$fields;
		$newfields = CMSProfileField::doSelect();
	
		foreach($newfields as $field)
		{
			$fields[] = $field->fname;
		}
		
		return $fields;
	}
	
	public function getProfileFields()
	{
		$fields = $this->getUserProfileFields();
		$array = array();
		foreach ($fields as $field)
		{
			$fname = $field->fname;
			$array[$field->name] = $this->$fname;
		}
		return $array;
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
		if(method_exists($this, $var))
		{
			return $this->$var();
		}
        elseif (array_key_exists($var, $this->vars))
		{
            return $this->vars[$var];
        } else {
            return null;//throw new Exception("Property $var does not exist");
        }
    }

		public function getAsArray()
		{
			$array = $this->vars;
			$array['profile_id'] = $this->getId();
			return $array;
		}

	// DB
	
	public function populate($row)
	{
		if (isset($row[self::DB_ITEM.'__id']))
		{
			$this->setId($row[self::DB_ITEM.'__id']);
		}
		
		foreach (self::getFields() as $field)
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
		$fields = array_merge(array('id','created_at','updated_at'), self::getFields());
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

    $query = 'SELECT '.self::DB_ITEM.'.*, '.self::generateSelectList().' FROM ' . cms_db_prefix() . self::DB_NAME . ' AS '.self::DB_ITEM;

    $values = array();

    if (isset($params['where']))
    {

      $fields = array();
      foreach ($params['where'] as $field => $value) 
      {
        $fields[] = $field . ' =  ?';
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
      $query .= ' ORDER BY created_at';
    }

    $dbresult = $db->Execute($query, $values);

    $items = array();
   if ($dbresult && $dbresult->RecordCount() > 0)
    {
      while ($dbresult && $row = $dbresult->FetchRow())
      {
        $item = new CMSProfile();
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
    $db = cms_utils::get_db();
		$query = 'INSERT INTO '.cms_db_prefix(). self::DB_NAME . '
			SET created_at = NOW(),
				updated_at = NOW()
								';
		$values = array();				
		foreach(self::getFields() as $field)
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
		foreach(self::getFields() as $field)
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