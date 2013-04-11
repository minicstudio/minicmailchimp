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
			'text' => false,
			'type' => 'conf'
		);

		$this->hooks = array('Top', 'LeftColumn', 'RightColumn', 'Footer', 'Home', 'LeftColumnProduct', 'RightColumProduct', 'FooterProduct');
	}

	/**
 	 * install
	 */
	public function install()
	{
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
		if (!parent::uninstall() || 
			!Configuration::deleteByName(strtoupper($this->name).'_START') || 
			!Configuration::deleteByName('MINIC_MAILCHIMP_SETTINGS') || 
			!Configuration::deleteByName('MINIC_MAILCHIMP_FORM'))
			return false;
		return true;
	}

	/**
 	 * admin page
	 */	
	public function getContent()
	{
		$languages = Language::getLanguages(false);
		// smarty for admin
		$smarty_array = array(
			'first_start' 	 => Configuration::get(strtoupper($this->name).'_START'),

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
			'hooks'			=> $this->hooks,
			'languages'		=> $languages,
			'default_lang' 	=> $this->context->language->id,
			'flags'			=> array(
				'title' => $this->displayFlags($languages, $this->context->language->id, 'title¤form', 'title', true),
				'form'	=> $this->displayFlags($languages, $this->context->language->id, 'title¤form', 'form', true)
			)
		);

		// Handling settings
		if(Tools::isSubmit('submitMailchimp'))
			$this->configureMailchimp();

		// Mailchimp lists
		$settings = unserialize(Configuration::get('MINIC_MAILCHIMP_SETTINGS'));
		if (!$settings){
			$this->message['text'] = $this->l('Before you start useing this module you need to configure it below!');
		}else{
			$this->api_key = $settings['apikey'];
			$this->ssl = $settings['ssl'];

			$this->getMailchimpLists();
		}	

		// Handling Import
		if(Tools::isSubmit('submitImport'))
			$this->importCustomers();		
		// Handling Form
		if(Tools::isSubmit('submitForm'))
			$this->saveSubscriberForm();


		// Smarty for admin
		$smarty_array['mailchimp'] 	= $settings;
		$smarty_array['message'] 	= ($this->message['text']) ? $this->message : false;
		$smarty_array['form']		= unserialize(Configuration::get('MINIC_MAILCHIMP_FORM'));
		$this->smarty->assign('minic', $smarty_array);
			
		// Change first start
		if(Configuration::get(strtoupper($this->name).'_START') == 1)
			Configuration::updateValue(strtoupper($this->name).'_START', 0);

		return $this->display(__FILE__, 'views/templates/admin/minicmailchimp.tpl');
	}

	/**
	 * Save the subscriber form details
	 */
	public function saveSubscriberForm()
	{
		$def_lang = $this->context->language->id;
		// Get saved form data
		$form_settings = unserialize(Configuration::get('MINIC_MAILCHIMP_FORM'));
		// Prepear hooks array
		$hooks = array(
			'old' => ($form_settings) ? $form_settings['hooks'] : false,
			'new' => (Tools::isSubmit('hooks')) ? Tools::getValue('hooks') : false,
		);
		// Get form
		$form  = ( Tools::isSubmit('form_'.$def_lang)) ? Tools::getValue('form_'.$def_lang) : false;

		if(!$hooks['new']){
			$this->message = array('text' => $this->l('At least a hook is required to show the form.'), 'type' => 'error');
			return;
		}
		if(!$form){
			$this->message = array('text' => $this->l('The form code is required!'), 'type' => 'error');
			return;
		}

		// Unhook from all possible hooks
		foreach ($this->hooks as $hook) {
			if($this->isRegisteredInHook('display'.$hook))
				$this->unregisterHook('display'.$hook);
		}

		// Hook
		if($hooks['new']){ // pointless ?
			foreach ($hooks['new'] as $hook) {
				$this->registerHook('display'.$hook);
			}
		}

		// Save
		$data = array();
		$languages = Language::getLanguages(false);
		foreach ($languages as $key => $lang) {
			$title = ( Tools::isSubmit('title_'.$lang['id_lang'])) ? Tools::getValue('title_'.$lang['id_lang']) : false;

			$data[$lang['id_lang']] = array(
				'title' => ($title) ? $title : Tools::getValue('title_'.$def_lang),
				'form'  => ( $form) ? htmlspecialchars($form)  : htmlspecialchars(Tools::getValue('form_'.$def_lang)),
			);
		}

		if(Configuration::updateValue('MINIC_MAILCHIMP_FORM', serialize(array('data' => $data, 'hooks' => $hooks['new']))))
			$this->message['text'] = $this->l('Saved!');

	}

	/**
	 * Import customers into the selected Mailchimp list
	 */
	public function importCustomers()
	{
		// Get List id
		$list_id = Tools::getValue('list');
		if(!Tools::isSubmit('list') || !$list_id){
			$this->message = array('text' => $this->l('List is required! Please select one.'), 'type' => 'error');
			return;
		}

		// Get customers type
		$all_customers = (Tools::getValue('all-user')) ? true : false;
		// Get mailchimp fields
		$fields = Tools::getValue('fields');
		// Get Customers list
		$customers = Customer::getCustomers();
		
		// Creating import array
		$list = array();
		foreach ($customers as $customer_key => $customer) {
			// Get customer data
			$customer_details = new Customer($customer['id_customer']);

			// Populate customer array
 			if($all_customers){
 				$list[$customer_key]['EMAIL'] = $customer_details->email;
 				if(isset($fields[$list_id])){
 					foreach ($fields[$list_id] as $key => $field) {
						// mailchimp tag = customer field
						$list[$customer_key][$key] = $customer_details->$field;
					}
 				}
 			}else if(!$all_customers && $customer_details->newsletter){
 				$list[$customer_key]['EMAIL'] = $customer_details->email;
 				if(isset($fields[$list_id])){
 					foreach ($fields[$list_id] as $key => $field) {
						// mailchimp tag = customer field
						$list[$customer_key][$key] = $customer_details->$field;
					}
 				}
 			}
		}

		// listBatchSubscribe configuration
		$optin = (Tools::getValue('optin')) ? true : false; //send optin emails
		$up_exist = (Tools::getValue('update_users')) ? true : false; //update currently subscribed users
		$replace_int = true;

		// Import customers
		$mailchimp = new MCAPI($this->api_key, $this->ssl);
		$import = $mailchimp->listBatchSubscribe($list_id, $list, $optin, $up_exist, $replace_int);

		// Process response
		if ($mailchimp->errorCode){
			$this->message = array('text' => $this->l('Mailchimp error code:').' '.$mailchimp->errorCode.'<br />'.$this->l('Milchimp message:').' '.$mailchimp->errorMessage, 'type' => 'error');
			return;
		} else {
			$this->message['text'] =  $this->l('Successfull imported:').' <b>'.$import['add_count'].'</b><br />';
			$this->message['text'] .= $this->l('Successfull updated:').' <b>'.$import['update_count'].'</b><br />';
			if($import['error_count'] > 0){
				$this->message['text'] .= $this->l('Error occured:').' <b>'.$import['error_count'].'</b><br />';
				foreach ($import['errors'] as $error) {
					$this->message['text'] .= '<p style="margin-left: 15px;">';
					$this->message['text'] .= $error['email'].' - '.$error['code'].' - '.$error['message'];
					$this->message['text'] .= '</p>';
				}
				$this->message['type'] = 'warn';
			}
		}

	}

	/**
	 * Get all mailchimp list and the fields belongs to them
	 */
	public function getMailchimpLists()
	{
		$mailchimp = new MCAPI($this->api_key, $this->ssl);

		// Get Mailchimp lists
		$list_response = $mailchimp->lists();

		if ($mailchimp->errorCode){
			$this->message = array('text' => $this->l('Mailchimp error code:').' '.$mailchimp->errorCode.'<br />'.$this->l('Milchimp message:').' '.$mailchimp->errorMessage, 'type' => 'error');
			return;
		} else {
			$lists = '';
			foreach ($list_response['data'] as $key => $value) {
				$lists[$key] = $value;
				// Get list fields
				$lists[$key]['fields'] = $mailchimp->listMergeVars($value['id']);
			}
			$this->context->smarty->assign('mailchimp_list', $lists);
			return true;
		}

	}

	/**
	 * Save Mailchimp settings into PS Configuration (api key and ssl)
	 */
	public function configureMailchimp()
	{
		$settings = array();
		// Get apikey
		if(!Tools::isSubmit('apikey') || !Tools::getValue('apikey')){
			$this->message = array('text' => $this->l('API Key is empty!'), 'type' => 'error');
			return;
		}
		// Get ssl
		if(!Tools::getValue('ssl') && Tools::getValue('ssl') != 0){
			$this->message = array('text' => $this->l('SSL save failed!'), 'type' => 'error');
			return;	
		}

		$settings = array(
			'apikey' => Tools::getValue('apikey'),
			'ssl'	 => ((int)Tools::getValue('ssl') == 1) ? true : false,
		);

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
		$this->context->controller->addCSS($this->_path.'views/css/custom.css');
		// JS
		$this->context->controller->addJquery();
		$this->context->controller->addJS($this->_path.'views/js/admin.js');
		$this->context->controller->addJS($this->_path.'views/js/custom.js');	
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
		return $this->hookDisplayHome($params, 'top');
	}

	/**
 	 * Home page hook
	 */
	public function hookDisplayHome($params, $class = false)
	{
		$data = unserialize(Configuration::get('MINIC_MAILCHIMP_FORM'));
		if(!$data)
			return;

		$smarty['class'] = ($class) ? $class : 'home';
		$smarty['title'] = $data['data'][$this->context->language->id]['title'];
		$smarty['form']  = $data['data'][$this->context->language->id]['form'];

		$this->smarty->assign('mailchimp_form', $smarty);
		return $this->display(__FILE__, 'views/templates/hooks/home.tpl');
	}

	/**
 	 * Left Column Hook
	 */
	public function hookDisplayRightColumn($params)
	{
		return $this->hookDisplayHome($params, 'right');
	}

	/**
 	 * Right Column Hook
	 */
	public function hookDisplayLeftColumn($params)
	{
	 	return $this->hookDisplayHome($params, 'left');
	}

	/**
 	 * Footer hook
	 */
	public function hookDisplayFooter($params)
	{
		return $this->hookDisplayHome($params, 'footer');
	}

	/**
	 * Product page hook
	 */
	public function hookDisplayLeftColumnProduct($params)
	{
		return $this->hookDisplayHome($params, 'left-product');
	}

	/**
	 * Product page hook
	 */
	public function hookDisplayRightColumProduct($params)
	{
		return $this->hookDisplayHome($params, 'right-product');
	}

	/**
	 * Product page hook
	 */
	public function hookDisplayFooterProduct($params)
	{
		return $this->hookDisplayHome($params, 'footer-product');
	}
}