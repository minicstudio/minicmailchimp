<?php 
/*
* minicskeleton - a module template for Prestashop v1.5+
* Copyright (C) 2013 S.C. Minic Studio S.R.L.
* 
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
* 
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

if (!defined('_PS_VERSION_'))
  exit;
 
require_once 'MCAPI.class.php';

class MinicMailchimp extends Module
{
	// DB file
	const INSTALL_SQL_FILE = 'install.sql';

	private $api_key;
	private $ssl;
	private $module_path;
	private $admin_tpl_path;
	private $front_tpl_path;
	private $hooks_tpl_path;

	public function __construct()
	{
		$this->name = 'minicmailchimp';
		$this->tab = 'advertising_marketing';
		$this->version = '1.0.0';
		$this->author = 'minic studio';
		$this->need_instance = 0;
		$this->ps_versions_compliancy = array('min' => '1.5', 'max' => '1.6'); 
		// $this->dependencies = array('blockcart');

		parent::__construct();

		$this->displayName = $this->l('Minic Mailchimp');
		$this->description = $this->l('A module to syncronise Mailchimp with Prestashop easilly.');

		$this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

		// Paths
		$this->module_path 		= _PS_MODULE_DIR_.$this->name.'/';
		$this->admin_tpl_path 	= _PS_MODULE_DIR_.$this->name.'/views/templates/admin/';
		$this->front_tpl_path	= _PS_MODULE_DIR_.$this->name.'/views/templates/front/';
		$this->hooks_tpl_path	= _PS_MODULE_DIR_.$this->name.'/views/templates/hooks/';
		
		$this->message = array(
			'text' => '',
			'type' => 'conf'
		);
	}

	/**
 	 * install
	 */
	public function install()
	{
		// Create DB tables - uncomment below to use the install.sql for database manipulation
		/*
		if (!file_exists(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))
			return false;
		else if (!$sql = file_get_contents(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))
			return false;
		$sql = str_replace(array('PREFIX_', 'ENGINE_TYPE'), array(_DB_PREFIX_, _MYSQL_ENGINE_), $sql);
		// Insert default template data
		$sql = str_replace('THE_FIRST_DEFAULT', serialize(array('width' => 1, 'height' => 1)), $sql);
		$sql = str_replace('FLY_IN_DEFAULT', serialize(array('width' => 1, 'height' => 1)), $sql);
		$sql = preg_split("/;\s*[\r\n]+/", trim($sql));

		foreach ($sql as $query)
			if (!Db::getInstance()->execute(trim($query)))
				return false;
		*/

		if (!parent::install() || 
			!$this->registerHook('displayHome') || 
			!$this->registerHook('displayHeader') || 
			!$this->registerHook('displayBackOfficeHeader') || 
			!$this->registerHook('displayAdminHomeQuickLinks') || 
			!Configuration::updateValue(strtoupper($this->name).'_START', 1))
			return false;
		return true;
	}

	/**
 	 * uninstall
	 */
	public function uninstall()
	{
		if (!parent::uninstall() || !Configuration::deleteByName('MINIC_MAILCHIMP_SETTINGS'))
			return false;
		return true;
	}

	/**
 	 * admin page
	 */	
	public function getContent()
	{
		if(!$this->getMailchimpLists())
			$error = true;

		if(Tools::isSubmit('submitSettings'))
			$this->saveSettings();

		if(Tools::isSubmit('submitImport'))
			$this->importCustomers();


		// Smarty for admin
		$this->smarty->assign('minic', array(
			'first_start' 	 => Configuration::get(strtoupper($this->name).'_START'),
			// Settings
			'settings'		 => unserialize(Configuration::get('MINIC_MAILCHIMP_SETTINGS')),

			'admin_tpl_path' => $this->admin_tpl_path,
			'front_tpl_path' => $this->front_tpl_path,
			'hooks_tpl_path' => $this->hooks_tpl_path,

			'info' => array(
				'module'	=> $this->name,
            	'name'      => Configuration::get('PS_SHOP_NAME'),
        		'domain'    => Configuration::get('PS_SHOP_DOMAIN'),
        		'email'     => Configuration::get('PS_SHOP_EMAIL'),
        		'version'   => $this->version,
            	'psVersion' => _PS_VERSION_,
        		'server'    => $_SERVER['SERVER_SOFTWARE'],
        		'php'       => phpversion(),
        		'mysql' 	=> Db::getInstance()->getVersion(),
        		'theme' 	=> _THEME_NAME_,
        		'userInfo'  => $_SERVER['HTTP_USER_AGENT'],
        		'today' 	=> date('Y-m-d'),
        		'module'	=> $this->name,
        		'context'	=> (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') == 0) ? 1 : ($this->context->shop->getTotalShops() != 1) ? $this->context->shop->getContext() : 1,
			),
			'form_action' 	=> 'index.php?tab=AdminModules&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&tab_module='. $this->tab .'&module_name='.$this->name,
			'message' 		=> $this->message,
		));
			
		// Change first start
		if(Configuration::get(strtoupper($this->name).'_START') == 1)
			Configuration::updateValue(strtoupper($this->name).'_START', 0);

		return $this->display(__FILE__, 'views/templates/admin/minicmailchimp.tpl');
	}

	public function importCustomers()
	{
		$all_customers = 0;
		if(!Tools::isSubmit('list') || !Tools::getValue('list')){
			$this->message = array('text' => $this->l('List is required! Please select one.'), 'type' => 'error');
			return;
		}
		if(!Tools::isSubmit('all-user')){
			$this->message = array('text' => $this->l('The type of Customers to import is required.'), 'type' => 'error');
			return;		
		}else{
			$all_customers = 1;
		}



	}

	public function getMailchimpLists()
	{
		$mailchimp = new MCAPI($this->api_key, $this->ssl);

		$lists = $mailchimp->lists();

		if ($mailchimp->errorCode){
			$this->message = array('text' => $this->l('Mailchimp error code:').' '.$mailchimp->errorCode.'<br />'.$this->l('Milchimp message:').' '.$mailchimp->errorMessage, 'type' => 'error');
			return;
		} else {
			$this->context->smarty->assign('mailchimp_list', $lists);
			return true;
		}
	}

	/**
	 * Save settings into PS Configuration
	 */
	public function saveSettings()
	{
		$settings = array();

		if(!Tools::isSubmit('apikey') || !Tools::getValue('apikey')){
			$this->message = array('text' => $this->l('API Key is empty!'), 'type' => 'error');
			return;
		}
		if(!Tools::getValue('ssl') && Tools::getValue('ssl') != 0){
			$this->message = array('text' => $this->l('SSL save failed!'), 'type' => 'error');
			return;	
		}
		if(!Tools::getValue('registration') && Tools::getValue('registration') != 0){
			$this->message = array('text' => $this->l('Sync new registration save failed!'), 'type' => 'error');
			return;	
		}

		$settings['apikey'] = Tools::getValue('apikey');
		$settings['ssl'] = (int)Tools::getValue('ssl');
		$settings['registration'] = (int)Tools::getValue('registration');

		Configuration::updateValue('MINIC_MAILCHIMP_SETTINGS', serialize($settings));

		$this->message['text'] = $this->l('Saved!');
	}

	// BACK OFFICE HOOKS

	/**
 	 * admin <head> Hook
	 */
	public function hookDisplayBackOfficeHeader()
	{
		// Check if module is loaded
		if (Tools::getValue('configure') != $this->name)
			return false;

		// CSS
		$this->context->controller->addCSS($this->_path.'views/css/elusive-icons/elusive-webfont.css');
		$this->context->controller->addCSS($this->_path.'views/css/admin.css');
		// JS
		$this->context->controller->addJquery();
		$this->context->controller->addJS($this->_path.'views/js/admin.js');	
	}

	/**
	 * Hook for back office dashboard
	 */
	public function hookDisplayAdminHomeQuickLinks()
	{	
		$this->context->smarty->assign('minicmailchimp', $this->name);
	    return $this->display(__FILE__, 'views/templates/hooks/quick_links.tpl');    
	}

	// FRONT OFFICE HOOKS

	/**
 	 * <head> Hook
	 */
	public function hookDisplayHeader()
	{
		// CSS
		$this->context->controller->addCSS($this->_path.'views/css/'.$this->name.'.css');
		// JS
		$this->context->controller->addJS($this->_path.'views/js/'.$this->name.'.js');
	}

	/**
 	 * Top of pages hook
	 */
	public function hookDisplayTop($params)
	{
		return $this->hookDisplayHome($params);
	}

	/**
 	 * Home page hook
	 */
	public function hookDisplayHome($params)
	{
		$this->context->smarty->assign('MinicMailchimp', array(
			'some_smarty_var' => 'some_data',
			'some_smarty_array' => array(
				'some_smarty_var' => 'some_data',
				'some_smarty_var' => 'some_data'
			),
			'some_smarty_var' => 'some_data'
		));

		return $this->display(__FILE__, 'views/tempaltes/hooks/home.tpl');
	}

	/**
 	 * Left Column Hook
	 */
	public function hookDisplayRightColumn($params)
	{
		return $this->hookDisplayHome($params);
	}

	/**
 	 * Right Column Hook
	 */
	public function hookDisplayLeftColumn($params)
	{
	 	return $this->hookDisplayHome($params);
	}

	/**
 	 * Footer hook
	 */
	public function hookDisplayFooter($params)
	{
		return $this->hookDisplayHome($params);
	}
}

?>