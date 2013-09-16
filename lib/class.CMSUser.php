<?php

    /*
        Handle users

        Author: Jean-Christophe Cuvelier <cybertotophe@gmail.com>
        Copyrights: Jean-Christophe Cuvelier - Morris & Chapman Belgium - 2010
        Licence: GPL

    */

    class CMSUser
    {
        protected $id;
        protected $is_modified = false;
        protected $vars = array();
        protected $profile;
        private $permissions = array();
        private $groups = array();
        private $password;
        private $htpassword;

        private $authenticated = false;

        protected static $fields = array(
            'username',
            'algorithm',
            'salt',
            //'password', VERY BAD IDEA. Password shouldn't be accessible "like that" even hashed...
            'email',
            'token',
            'is_active',
            'is_disabled',
            'last_login',
            'is_ldap',
            'comments'
        );

        const DB_NAME = 'module_cmsusers_users';
        const DB_ITEM = 'user';

        // Custom class logic

        public function __toString()
        {
            return (string)$this->username;
        }

        public function authenticate($password)
        {
            if ($this->authenticated) {
                return true;
            }

            $auth = $this->checkPassword($password);
            if (($auth === true) && ($this->is_active == 1) && ($this->is_disabled != 1)) // If auth = true
            {
                $this->last_login  = date('Y-m-d H:i:s');
                $this->is_modified = true;
                $this->save(false);
                $this->authenticated = true;

                return $auth;
            } else {
                return false;
            }
        }

        public function getProfile()
        {
            if (empty($this->profile)) {
                $this->profile          = new CMSProfile();
                $this->profile->user_id = $this->getId();
            }

            return $this->profile;
        }

        public function salt()
        {
            if (empty($this->vars['salt'])) $this->vars['salt'] = substr(md5(time() * time()), 0, 32);

            return $this->vars['salt'];
        }

        public function algorithm()
        {
            if (empty($this->vars['algorithm'])) $this->vars['algorithm'] = 'sha1';

            return $this->vars['algorithm'];
        }

        public function checkPassword($password)
        {
            if (($this->algorithm == 'md5') && (md5($this->salt . $password) === $this->password)) {
                return true;
            }

            if (($this->algorithm == 'sha1') && (sha1($this->salt . $password) === $this->password)) {
                return true;
            }

            return false;
        }

        public function setPassword($password)
        {
            // Always create an htpasswd
            $this->htpassword = $this->createHtPassword($password);

            if (($this->algorithm == 'md5')) {
                $this->password = md5($this->salt . $password);

                return true;
            }

            if (($this->algorithm == 'sha1')) {
                $this->password = sha1($this->salt . $password);

                return true;
            }

            return false;
        }

        public function createHtPassword($password)
        {
            return crypt($password, base64_encode($password));
        }

        public function generatePassword()
        {
            $password = base_convert(mt_rand(0x19A100, 0x39AA3FF), 10, 36);
            $this->setPassword($password);

            return $password;
        }

        public function getToken()
        {
            if (empty($this->vars['token'])) $this->generateToken();

            return $this->vars['token'];
        }

        public function generateToken()
        {
            $this->vars['token'] = substr(md5(time() * time()), 0, 32);
            $this->save();
        }


        public static function getUserList()
        {
            $users = self::doSelect(array('order_by' => array('username')));
            $array = array();
            foreach ($users as $user) {
                $array[$user->getId()] = (string)$user;
            }

            return $array;
        }

        public static function getUserNameById($user_id)
        {
            return (string)self::retrieveByPk($user_id);
        }

        public function getAsArray()
        {
            // We will just sum the profile and user (more convinient for json for example)
            $array             = $this->getProfile()->getAsArray();
            $array['id']       = $this->getId();
            $array['username'] = $this->username;
            $array['email']    = $this->email;

            return $array;
        }

        public function countGroups()
        {
            return count(CMSUserGroup::getGroupsList($this->getId()));
        }

        public function getGroups()
        {
            if (
                (!is_null($this->getId()))
                &&
                (count($this->groups) == 0)
            ) {
                $this->groups = CMSUserGroup::getGroupsList($this->getId());
            }

            return $this->groups;
        }

        public function getPermissions()
        {
            if (
                (!is_null($this->getId()))
                &&
                (count($this->permissions) == 0)
            ) {
                $this->permissions = CMSPermission::getPermissionsForUser($this->getId());
            }

            return $this->permissions;
        }

        public function checkPermission($name)
        {
            $permissions = $this->getPermissions();
            if (isset($permissions[$name]) || in_array($name, $permissions)) {
                return true;
            }

            return false;
        }

        public function checkGroup($name)
        {
            $groups = $this->getGroups();
            $grp    = array();
            foreach ($groups as $group) {
                $grp[$group->group_id] = (string)$group->getGroup(); //->__toString();
            }

            if (isset($grp[$name]) || in_array($name, $grp)) {
                return true;
            }

            return false;
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

        public function __set($var, $val)
        {
            $this->is_modified = true;
            $this->vars[$var]  = $val;
        }

        public function __get($var)
        {
            try {
                if (method_exists($this, $var)) {
                    return $this->$var();
                } elseif (array_key_exists($var, $this->vars)) {
                    return $this->vars[$var];
                } else {
                    //throw new Exception("Property $var does not exist");
                }
            } catch (Exception $e) {
                echo 'Error: ', $e->getMessage(), "\n";
            }
        }

        // DB

        public function populate($row)
        {
            if (isset($row[self::DB_ITEM . '__id'])) {
                $this->setId($row[self::DB_ITEM . '__id']);
            }

            if (isset($row[self::DB_ITEM . '__password'])) {
                $this->password = $row[self::DB_ITEM . '__password'];
            }

            if (isset($row[self::DB_ITEM . '__htpassword'])) {
                $this->htpassword = $row[self::DB_ITEM . '__htpassword'];
            }

            foreach (self::$fields as $field) {
                if (isset($row[self::DB_ITEM . '__' . $field])) {
                    $this->$field = $row[self::DB_ITEM . '__' . $field];
                }
            }

            $this->getProfile()->populate($row);
        }

        public static function retrieveByLdap($username, $password)
        {
            $CMSUsers = cms_utils::get_module('CMSUsers');

            $ldapconn = ldap_connect($CMSUsers->getPreference('ldap_server_host', '127.0.0.1'), $CMSUsers->getPreference('ldap_server_port', 389));
            ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);

            if ($ldapconn) {
                $ldaprdn = "cn=" . $username . ',' . $CMSUsers->getPreference('ldap_base_dn');

                $ldapbind = ldap_bind($ldapconn, $ldaprdn, $password);

                if ($ldapbind) {
                    $user = self::retrieveByUsername($username);

                    if (is_null($user)) {
                        $user           = new self();
                        $user->username = $username;
                        $user->is_ldap  = true;
                        $user->algorithm = 'ldap';
                        $user->save();
                    }

                    $user->authenticated = true;

                    return $user;
                } else {

//                    var_dump(ldap_error($ldapconn));

                    return NULL;
                }

            }

            return NULL;
        }

        /**
         * @param $email
         *
         * @return array
         */

        public static function retrieveByEmail($email)
        {
            return self::doSelect(array('where' => array('email' => $email)));
        }

        /**
         * @param $email
         *
         * @return CMSUser|null
         */

        public static function retrieveOneByEmail($email)
        {
            return self::doSelectOne(array('where' => array('email' => $email)));
        }

        /**
         * @param $username
         *
         * @return CMSUser|null
         */

        public static function retrieveByUsername($username)
        {
            return self::doSelectOne(array('where' => array('username' => $username)));
        }

        /**
         * @param $id
         *
         * @return CMSUser|null
         */

        public static function retrieveByPk($id)
        {
            return self::doSelectOne(array('where' => array('id' => $id)));
        }

        /**
         * @param array $params
         *
         * @return CMSUser|null
         */

        public static function doSelectOne($params = array())
        {
            $params['limit'] = 1;
            $items           = self::doSelect($params);
            if ($items) {
                return current($items);
            } else {
                return NULL;
            }
        }

        // public static function generateSelectList()
        // {
        // 	$list = self::DB_ITEM . '.id as '.self::DB_ITEM .'__id, ';
        // 	$list .= self::DB_ITEM . '.password as '.self::DB_ITEM .'__password, ';
        // 	foreach (self::$fields as $field)
        // 	{
        // 		$list .= self::DB_ITEM . '.'.$field.' as '.self::DB_ITEM .'__'.$field.', ';
        // 	}
        //
        // 	$list .= self::DB_ITEM . '.created_at as '.self::DB_ITEM .'__created_at, ';
        // 	$list .= self::DB_ITEM . '.updated_at as '.self::DB_ITEM .'__updated_at ';
        // 	return $list;
        // }
        //
        public static function generateSelectList()
        {
            $fields = array_merge(array('id', 'password', 'htpassword', 'created_at', 'updated_at'), self::$fields);
            $list   = array();
            foreach ($fields as $field) {
                $list[] = self::DB_ITEM . '.' . $field . ' as ' . self::getRowName($field);
            }

            return implode(' , ', $list);
        }

        public static function getRowName($name)
        {
            return self::DB_ITEM . '__' . $name;
        }


        public static function doSelect($params = array())
        {
            $db    = cms_utils::get_db();
            $query = 'SELECT
		' . self::DB_ITEM . '.*, ' . CMSProfile::DB_ITEM . '.*,
		' . self::generateSelectList() . ', ' . CMSProfile::generateSelectList() . '
		FROM ' . cms_db_prefix() . self::DB_NAME . ' AS ' . self::DB_ITEM . '
		LEFT JOIN ' . cms_db_prefix() . CMSProfile::DB_NAME . ' AS ' . CMSProfile::DB_ITEM . '
		ON ' . self::DB_ITEM . '.id = ' . CMSProfile::DB_ITEM . '.user_id';

            $values = array();

            if (isset($params['where'])) {

                $fields = array();
                foreach ($params['where'] as $field => $value) {
                    $fields[] = self::DB_ITEM . '.' . $field . ' =  ?';
                    $values[] = $value;
                }

                $query .= ' WHERE ' . implode(' AND ', $fields);
            }

            if (isset($params['order_by'])) {
                $query .= ' ORDER BY ' . implode(', ', $params['order_by']);
            } else {
                $query .= ' ORDER BY ' . self::DB_ITEM . '.created_at';
            }

            $dbresult = $db->Execute($query, $values);
            $items    = array();

            if ($dbresult && $dbresult->RecordCount() > 0) {
                while ($dbresult && $row = $dbresult->FetchRow()) {
                    $item = new CMSUser();
                    $item->populate($row);
                    $items[] = $item;
                }
            }

            return $items;
        }


        public function save($generate_passwords = true)
        {
            $response = false;
            if ($this->getId()) {
                if ($this->is_modified) {
                    $response = $this->update();
                }
            } else {
                $response = $this->insert();
            }
            if ($generate_passwords) {
                self::generateHtPasswd();
            }

            return $response;
        }

        protected function insert()
        {
            // Username should exists only once
            if (self::retrieveByUsername($this->username) !== NULL) return false;
            //

            $db = cms_utils::get_db();

            $query  = 'INSERT INTO ' . cms_db_prefix() . self::DB_NAME . '
			SET created_at = NOW(),
				updated_at = NOW()
								';
            $values = array();
            foreach (self::$fields as $field) {
                $query .= ', ' . $field . ' = ?';
                $values[] = $this->$field;
            }
            $query .= ', password = ?';
            $values[] = $this->password;
            $query .= ', htpassword = ?';
            $values[] = $this->htpassword;

            $result = $db->Execute($query, $values);
            if ($result === false) return false;
            $this->setId($db->Insert_ID());

            return true;
        }

        protected function update()
        {
            $db = cms_utils::get_db();

            $query = 'UPDATE ' . cms_db_prefix() . self::DB_NAME . '
			SET updated_at = NOW()';

            $values = array();
            foreach (self::$fields as $field) {
                $query .= ', ' . $field . ' = ?';
                $values[] = $this->$field;
            }
            $query .= ', password = ?';
            $values[] = $this->password;
            $query .= ', htpassword = ?';
            $values[] = $this->htpassword;

            $query .= ' WHERE id = ?';
            $values[] = $this->getId();

            $result = $db->Execute($query, $values);
            if ($result === false) return false;

            return true;
        }

        public function delete()
        {
            if ($this->getId()) {
                $db     = cms_utils::get_db();
                $query  = 'DELETE FROM ' . cms_db_prefix() . self::DB_NAME . ' WHERE id = ?';
                $result = $db->Execute($query, array($this->getId()));
                if ($result !== false) {
                    $this->getProfile()->delete();
                    CMSUserGroup::deleteUserPrefs($this->getId());

                    return true;
                }
            }

            return false;
        }

        public static function generateHtPasswd()
        {
            $users = self::doSelect(array('where' => array(
                'is_active'   => 1,
                'is_disabled' => 0,
            )));

            $module = cms_utils::get_module('CMSUsers');
            $config = cms_utils::get_config();
            $file   = $module->getPreference('htpassword_path', $config['root_path']) . DIRECTORY_SEPARATOR . ".htpasswd";

            $data = '';
            foreach ($users as $user) {
                $data .= addslashes($user->username) . ':' . $user->htpassword . "\n";
            }

            file_put_contents($file, $data);
        }
    }