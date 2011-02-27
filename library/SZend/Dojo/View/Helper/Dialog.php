<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    SZend_Dojo_View_Helper_Dialog
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * SZend_Calendar
 *
 * @category   Zend
 * @package    SZend_Dojo_View_Helper_Dialog
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class SZend_Dojo_View_Helper_Dialog extends Zend_Dojo_View_Helper_DijitContainer
{
	const DIALOG_DOJO = 'dijit.Dialog';
	const DIALOG_DOJOX = 'dojox.widget.Dialog';
	
	/**
	 * Holds the default dialog type.
	 */
	public static $_dialogType = self::DIALOG_DOJO;
	
	/**
	 * Sets the default dialog type to use.
	 * 
	 * @param string $dojoType Type of dijit to use.
	 */
	public static function setDialogType($dojoType)
	{
		switch ($dojoType) {
			case self::DIALOG_DOJOX:
				self::$_dialogType = $dojoType;
				break;
			default:
				self::$_dialogType = self::DIALOG_DOJO;
				break;
		}
	}
	
	/**
	 * Dialog view helper.
	 * 
	 * @param string $id JavaScript id for the dialog.
	 * @param array $attribs Attributes for the dialog.
	 * @param array $options Options for the dialog.
	 */
	public function dialog($id = null, $content = '', array $params = array(), array $attribs = array())
	{
		if (0 === func_num_args()) {
			return $this;
		}
		
		$this->_setup($params);
		return $this->_createLayoutContainer($id, $content, $params, $attribs);
	}
	
	/**
	 * Begin capturing content for layout container
	 *
	 * @param  string $id
	 * @param  array $params
	 * @param  array $attribs
	 * @return void
	 */
	public function captureStart($id, array $params = array(), array $attribs = array())
	{
		$this->_setup($params);
		return parent::captureStart($id, $params, $attribs);
	}
	
	/**
	 * Setup the dialog type and add stylesheets if needed. 
	 */
	protected function _setup($params)
	{
		$this->_module = self::$_dialogType;
		if (array_key_exists('enhanced', $params)) {
			switch ($params['enhanced']) {
				case self::DIALOG_DOJOX:
					SZend_Dojo_View_Helper_Dojo_Extended::addStylesheet('/dojox/widget/Dialog/Dialog.css');
					$this->_module = self::DIALOG_DOJOX;
					break;
				default:
					$this->_module = self::DIALOG_DOJO;
			}
			unset($params['enhanced']);
		}
		
		if (array_key_exists('easing', $params)) {
			$this->dojo->requireModule('dojo.fx.easing');
		}
		
		$this->_dijit = $this->_module;
	}
}