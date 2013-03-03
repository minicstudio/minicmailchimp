<?php
/*
* 2007-2012 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author Minic Studio <office@minic.ro>
*  @copyright  2013 S.C. Minic Studio S.R.L.
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

if (!defined('_PS_VERSION_'))
	exit;

class MinicMailchimp extends Module
{
	private $_html = '';

	public function __construct()
	{
		$this->name = 'minicmailchimp';
		$this->tab = 'front_office_features';
		$this->version = '0.1';
		$this->author = 'minic studio';
		$this->need_instance = 0;
		$this->secure_key = Tools::encrypt($this->name);

		parent::__construct();

		$this->displayName = $this->l('Minic mailchimp sync');
		$this->description = $this->l('Displays the Mailchimp subscriber form.');
	}

	/**
	 * @see Module::install()
	 */
	public function install()
	{
		if (!parent::install() || !$this->registerHook('displayFooter') || !$this->registerHook('displayHeader'))
			return false;
		return true;
	}

	public function uninstall()
	{
		if (!parent::uninstall())
			return false;
		return true;
	}

	public function getContent()
	{

	}

	public function hookDisplayHeader()
	{
		// $this->context->controller->addCSS($this->_path.$this->name.'.css');
	}

	public function hookDisplayFooter()
	{
		// return $this->display(__FILE__, '');
	}
}
