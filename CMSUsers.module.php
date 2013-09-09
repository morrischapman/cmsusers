<?php

/*
  Module: CMS Users - This is an alternative to FEU , SelfRegistration and Custom Content
  
  Copyrights: Jean-Christophe Cuvelier - Morris & Chapman Belgium - 2010 ©
  License: GPL
*/

require_once dirname(__FILE__) . '/lib/class.CMSUser.php';
require_once dirname(__FILE__) . '/lib/class.CMSProfile.php';
require_once dirname(__FILE__) . '/lib/class.CMSGroup.php';
require_once dirname(__FILE__) . '/lib/class.CMSUserGroup.php';
require_once dirname(__FILE__) . '/lib/class.CMSPermission.php';
require_once dirname(__FILE__) . '/lib/class.CMSGroupPermission.php';
require_once dirname(__FILE__) . '/lib/class.CMSProfileField.php';
require_once dirname(__FILE__) . '/lib/class.CMSGroupProfileField.php';

class CMSUsers extends CMSModule
{
  public static $frontend_actions = array(
    'default'         => 'default',
    'signin'           => 'signin',
    'signout'         => 'signout',
    'signup'           => 'signup',
    'profile'         => 'profile',
    'url_for'         => 'url_for',
    'sign_in'         => 'sign_in',
    'password_change' => 'password_change',
    'password_reset'   => 'password_reset',
    'password_forgot'  => 'password_forgot',
    'profile_edit'   => 'profile_edit',  
    'user_show'     => 'user_show',  
    'list'           => 'list',  
    );
    
  public static $frontend_templates = array(
    'default'                   => 'default',
    'signin_form'               => 'signin_form',
    'signin_success'             => 'signin_success',
    'signin_disabled'           => 'signin_disabled',
    'signout_success'           => 'signout_success',
    'signup_form'               => 'signup_form',
    'signup_success'             => 'signup_success',
    'signup_disabled'           => 'signup_disabled',
    //'profile_form'               => 'profile_form',
    'profile_success'           => 'profile_success',
    'profile_edit_form'         => 'profile_edit_form',
    'profile_edit_success'       => 'profile_edit_success',
    'password_change_form'       => 'password_change_form',
    'password_change_success'   => 'password_change_success',
    'password_reset_form'       => 'password_reset_form',
    'password_reset_error'       => 'password_reset_error',
    'password_reset_success'     => 'password_reset_success',
    'password_forgot_form'       => 'password_forgot_form',
    'password_forgot_success'   => 'password_forgot_success',
    'email_validation'           => 'email_validation',
    'validate_success'           => 'validate_success',
    'validate_error'             => 'validate_error',
    'email_signup_success'       => 'email_signup_success',
    'email_reset_password_success'       => 'email_reset_password_success',
    'show_success'               => 'show_success',
    'user_list_success'               => 'user_list_success',
    );
  
  public function GetName()                {  return 'CMSUsers';   }
  public function GetFriendlyName()        {  return 'CMS Users';  }
  public function GetVersion()             { return '1.0.14'; }
  public function GetAuthor()              { return 'Jean-Christophe Cuvelier'; }
  public function GetAuthorEmail()         { return 'cybertotophe@gmail.com'; }
  public function HasAdmin()               { return true; }
  public function VisibleToAdminUser()     { return $this->CheckAccess(); }
  public function CheckAccess()            { return $this->CheckPermission('Manage CMSUsers'); }
  public function GetDependencies()        { return array('CMSForms' => '0.0.24'); }
  public function GetAdminSection()        { return 'usersgroups';  }
  public function GetHelp()                { return $this->lang('help'); }
  public function MinimumCMSVersion()    { return '1.10';  }
  
  public function IsPluginModule() {    return true;  }
  
  function HasCapability($capability, $params=array()) {
    if ($capability == "users")
      return true;
    return false;
  }
  
  public function SetParameters()
  {
    if(!isset($this->initialized))
    {
     $this->initialized = true;
       // $this->InitializeAdmin(); // TODO: Useless
       $this->InitializeFrontend(); 
    }
  }
  
  public function InitializeFrontend() {  
        $this->RegisterModulePlugin();
        $this->SetupRoutes();
        $this->loadUser(); // Use the smarty {CMSUser} instead
        $this->smarty->register_function('CMSUser',
                   array('CMSUsers','retrieveUser'));
  }
    
  function retrieveUser($params,&$smarty)
  {
    $user = self::getUser();

    if(is_object($user))
    {
      if (isset($params['assign_to']))
      {
        $smarty->assign($params['assign_to'],$user);
      }
      else
      {
        $smarty->assign('CMSUser',$user);
      }        
    }

    
  }
  
  public function loadUser()
  {
    $user = self::getUser();
    if($user)
    {
      $this->smarty->assign('CMSUser',$user);
      return true;
    }
    return false;
  }
  
  public function SetupRoutes()
  {
    // foreach(self::$frontend_actions as $action)
    //     {
    //       $this->RegisterRoute('/user\/'.$action.'\/(?P<returnid>[0-9]+)(\/.*?)?$/', array('action' => $action));
    //     }
    ;
    $this->RegisterRoute('/user\/validate\/(?P<user_id>[0-9]+)\/(?P<token>[a-zA-Z0-9_-]+)(\/.*?)?$/', 
    array(
      'action' => 'validate',
      'returnid' => $this->GetPreference('default_page',  cmsms()->GetContentOperations()->GetDefaultPageID()),
      ));        
      
    $this->RegisterRoute('/user\/show\/(?P<username>[a-zA-Z0-9_-]+)(\/.*?)?$/', 
          array(
            'action' => 'user_show',
            'returnid' => $this->GetPreference('default_page',  cmsms()->GetContentOperations()->GetDefaultPageID()),
            ));      
                
    $this->RegisterRoute(  '/user\/(?P<action>[a-zA-Z0-9_-]+)\/(?P<user_id>[0-9]+)\/(?P<token>[a-zA-Z0-9_-]+)(\/.*?)?$/', array(
      'returnid' => $this->GetPreference('default_page',  cmsms()->GetContentOperations()->GetDefaultPageID())
      
    ));
    $this->RegisterRoute(  '/user\/(?P<action>[a-zA-Z0-9_-]+)\/(?P<user_id>[0-9]+)\/(?P<token>[a-zA-Z0-9_-]+)\/(?P<returnid>[0-9]+)(\/.*?)?$/');
    $this->RegisterRoute('/user\/(?P<action>[a-zA-Z0-9_-]+)\/(?P<returnid>[0-9]+)(\/.*?)?$/');
    $this->RegisterRoute('/user\/(?P<action>[a-zA-Z0-9_-]+)(\/.*?)?$/', array(
      'returnid' => $this->GetPreference('default_page',  cmsms()->GetContentOperations()->GetDefaultPageID()),
      ));

  }
  
  public function checkActionRight($action = 'default')
  {
    if (in_array($action, self::$frontend_actions))
    {
      return $action;
    }
    else
    {
      return 'default';
    }
  }
  
  function getIcon($icon, $alt=null)
  {
    $config =& $this->getConfig();
    $image_path = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'images'. DIRECTORY_SEPARATOR . 'icons' . DIRECTORY_SEPARATOR . $icon;
    $image_url = $config['root_url'] . '/modules/CMSUsers/images/icons/' . $icon;
    if(is_file($image_path))
    {
      $img = '<img src="'.$image_url.'"';
      if (!is_null($alt))
      {
        $img .= ' alt="'.$alt.'" title="'.$alt.'"';
      }
      $img .= ' />';
      return  $img;
    }
    return null;
  }
  
  // GENERAL TOOLS
  
  public function GetDefaultTemplates()
  {
    return unserialize($this->GetPreference('default_templates', serialize(array())));
  } 
  
  public function SetDefaultTemplates($list = array())
  {
    return $this->SetPreference('default_templates', serialize($list));
  }
  
  public function AddDefaultTemplate($action, $template)
  {
    $list = $this->GetDefaultTemplates();
    $list[$action] = $template;
    $this->SetDefaultTemplates($list);
  }
  
  public function GetDefaultTemplate($action)
  {
      $list = $this->GetDefaultTemplates();
      if (array_key_exists($action, $list))
      {
        return $list[$action];
      }
      else
      {
        return false;
      }
  }
  
  public function isDefaultTemplate($template)
  {    
    $list = $this->GetDefaultTemplates();
    $action = array_search($template, $list);
    if($action !== false)
    {
      return $action;
    }
    return false;
  }  
  
  public function removeDefaultTemplate($template)
  {    
    $list = $this->GetDefaultTemplates();
    $action = array_search($template, $list);
    if($action !== false)
    {
      unset($list[$action]);
      $this->SetDefaultTemplates($list);
    }
    return false;
  }
  
  public function ProcessTemplateFor($action, $params = array())
  {
    if (isset($params['template']) && $this->GetTemplate($params['template'])) {
      return $this->ProcessTemplateFromDatabase($params['template']);
    }
    elseif (($template = $this->GetDefaultTemplate($action))  &&  ($this->GetTemplate($template) !== false))
    {
      return $this->ProcessTemplateFromDatabase($template);
    }
    else
    {
      return $this->ProcessTemplate('frontend.'.$action.'.tpl');
    }
  }

  public function mkLink($id, $returnid='', $params=array(), $onlyhref=false, $inline=false, $addttext='', $targetcontentonly=false)
  {
    if(isset($params['maction']) && ($action = $this->checkActionRight($params['maction'])))
    {
      $title = isset($params['title'])?$params['title']:$this->lang($action);
      unset($params['action']);
      unset($params['maction']);
      unset($params['title']);
      unset($params['module']);

      switch ($action)
      {
        case 'user_show':
          $slug = 'user/show/' . $params['username'] .'/';
          unset($params['username']);
          break;
        default:
          $slug = 'user/'.$action.'/';
          break;
      }
      

      if ($targetcontentonly || ($returnid != '' && !$inline)) {
            $id = 'cntnt01';
      }

      $newparams = array();
      foreach($params as $key => $param)
      {
        $newparams[$id.$key] = $param; 
      }

      if (count($newparams) > 0)
      {
        $slug .= '?'.http_build_query($newparams);
      }
      
      $warn_message = '';
      $inline = false;
      $addttext = '';
      $targetcontentonly = false;
      
      return $this->CreateLink($id, $action, $returnid, $title, $params, $warn_message, $onlyhref, $inline, $addttext, $targetcontentonly, $slug);
    }
  }

  // Function to check all the modules to search for profile module existance
  
  public static function getUserableModules()
  {
    $userable = array();
    $modules = cmsms()->GetModuleOperations()->get_modules_with_capability('cms_users');

    foreach($modules as $module)
    {
      $userable[$module] = $module;
    }
    return $userable;
  }
  
  // User specific functions

  public static function getUser()
  {
    if (isset($_SESSION['modules']['CMSUsers']['user_id']))
    {
      $user = CMSUser::retrieveByPk($_SESSION['modules']['CMSUsers']['user_id']);
      if(is_object($user))
      {
        return $user;
      }
    }
    elseif(isset($_SERVER['PHP_AUTH_USER']) && !is_null($_SERVER['PHP_AUTH_USER']))
    {
      $user = CMSUser::retrieveByUsername($_SERVER['PHP_AUTH_USER']);
            
      if ($user && $user->authenticate($_SERVER['PHP_AUTH_PW']))
      {
        $_SESSION['modules']['CMSUsers']['user_id'] = $user->getId();
        return $user;
      }
    }  
    elseif(isset($_SERVER['REMOTE_USER']) && !is_null($_SERVER['REMOTE_USER']))
    {
      // I'M NOT SURE THIS WAY IS SAFE
      $user = CMSUser::retrieveByUsername($_SERVER['REMOTE_USER']);

      if (is_object($user))
      {
        //&& $user->authenticate($_SERVER['PHP_AUTH_PW']) // I DO NOT LIKE THIS WAY !!!
        $_SESSION['modules']['CMSUsers']['user_id'] = $user->getId();
        return $user;
      }
    }
    return null;
  }
  
  public static function getUserOrLogin($params = array()) //$redirect = 'profile', $redirect_url = null)
  {
    if(!is_array($params))
    {
      $params = array('redirect' => $params);
    }
    elseif(count($params) == 0)
    {   
      $params = array('redirect' => 'profile');
    }    
    
    if (isset($_SESSION['modules']['CMSUsers']['user_id']))
    {
      return CMSUser::retrieveByPk($_SESSION['modules']['CMSUsers']['user_id']);
    }
    $cmsusers = cms_utils::get_module('CMSUsers');
    $returnid = $cmsusers->GetPreference('default_page',  cmsms()->GetContentOperations()->GetDefaultPageID());
    echo $cmsusers->Redirect($id,'signin',$returnid, $params);
    return null;
  }
  
  public function sendEmail($type, CMSUser &$user, $id,$returnid)
  {
    switch($type)
    {
      case 'validation':
        $subject = $this->GetPreference('validation_email_subject', $this->lang('account validation'));
        $user->generateToken(); // Used to generate a new token
        $url = $this->CreateLink($id,'validate',$returnid,'',array('user_id' => $user->getId(), 'token' => $user->getToken()),'',true,false,'',false,'user/validate/' . $user->getId() . '/' . $user->getToken() . '/');
        $template = 'email_validation';
        break;
      case 'signup':
        $subject = $this->GetPreference('signup_email_subject', $this->lang('account details'));
        $template = 'email_signup_success';
        $url = $this->CreateLink($id,'signin',$returnid,'',array(),'',true,false,'',false,'user/signin/');
        break;      
      case 'password_reset':
        $subject = $this->GetPreference('password_reset_email_subject',  $this->lang('account reset password'));
        $template = 'email_reset_password_success';
        $url = $this->CreateLink($id,'password_reset',$returnid,'',array('user_id' => $user->getId(), 'token' => $user->getToken()),'',true,false,'',false,'user/password_reset/' . $user->getId() . '/' . $user->getToken() . '/' . $this->GetPreference('default_page',  cmsms()->GetContentOperations()->GetDefaultPageID()) . '/');
        break;
    }
    
    if (isset($template)) // So we are in existing types only...
    {
      $subject = str_replace('%s', $user->username, $subject);
      
      $cmsmailer =& $this->GetModuleInstance('CMSMailer');
      $cmsmailer->reset();
      
      $cmsmailer->SetFromName($this->GetPreference('email_from'));
      $cmsmailer->SetFrom($this->GetPreference('email_address'));
      
      $cmsmailer->SetSubject($subject);
      $cmsmailer->AddAddress($user->email);
      $this->smarty->assign('user', $user);
      $this->smarty->assign('url', $url);
      $cmsmailer->SetBody($this->ProcessTemplateFor($template));
      $cmsmailer->IsHTML(true);
      $cmsmailer->Send();
    }
  }
  
  public static function jumpTo($url)
  {
    $url = self::parseUrl($url);
    if (headers_sent())
    {
      echo '
      <script type="text/javascript">
      <!--
        location.replace("'.$url.'");
      // -->
      </script>
      <noscript>
        <meta http-equiv="Refresh" content="0;URL='.$url.'">
      </noscript>';
      exit;
    }
    else
    {
      header('Location: '.$url);
    }
  }
  
  private static function parseUrl($url)
  {
    if (strpos($url,'http') === 0)
    {
      return $url;
    }
    else
    {
      ;
      $manager = cmsms()->GetHierarchyManager();
      $node = $manager->sureGetNodeByAlias($url);
      if ($node) {
        $content = $node->GetContent();
        if ($content)
        {
          return $content->GetUrl();
        }
      }
      else
      {
        $node = $manager->sureGetNodeById($url);
        if ($node) {
          $content = $node->GetContent();
          if ($content)
          {
            return $content->GetUrl();
          }
        }
      }
    }  
    return null;
  }
  
  public function switchHttpAuth($on = false)
  {
    $config = cms_utils::get_config();
    $file = $config['root_path'] . DIRECTORY_SEPARATOR . '.htaccess';
    $data = file_get_contents($file);
    if(false !== $data)
    {
      $data = $this->replaceHttpAuth($data, $on);
      file_put_contents($file, $data);
    }
  }
  
  public function generateHtPasswd()
  {
    // $data = 
  }
  
  protected function replaceHttpAuth($data, $on = false)
  {
    if(false === $on) {
      if(strpos($data, '#--<HTTPAUTH>--') !== FALSE)  {
        $data = $this->removeHttpAuthValue($data);
      } else {
        // Nothing to do
        return $data;
      }
    } elseif(true === $on) {
      if(strpos($data, '#--<HTTPAUTH>--') !== FALSE) {
        $data = $this->removeHttpAuthValue($data);
        $data .= $this->getHttpAuthValue();
      } else {
        // Just add it
        $data .= $this->getHttpAuthValue();
      }
    }
    // Wrong $on value, we do nothing
    return $data;
  }
  
  protected function removeHttpAuthValue($data)
  {
    if($start = strpos($data, "\n#--<HTTPAUTH>--"))
    {
      $end = strpos($data, '#--</HTTPAUTH>--') + strlen("#--</HTTPAUTH>--\n");
      
      $data = substr($data, 0, $start) . substr($data, $end);


      // echo $start . ' ------ ' . $end;
    }    
    return $data;
  }
  
  protected function getHttpAuthValue()
  {
    $config = cms_utils::get_config();
    $text = "\n#--<HTTPAUTH>--\n";
    
    $text .= "AuthUserFile " . $this->getPreference('htpassword_path',$config['root_path']) . DIRECTORY_SEPARATOR . ".htpasswd\n";
    $text .= "AuthGroupFile /dev/null\n";
    $text .= "AuthName \"" . str_replace(array('"', '\\'), array('', ''), $this->getPreference('htpassword_title','Secure area')) . "\"\n";
    $text .= "AuthType Basic\n\n";
    $text .= "Require valid-user\n";
    
    $text .= "#--</HTTPAUTH>--\n";
    return $text;
  }
}