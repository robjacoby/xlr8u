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
 * @package    SZend_Dojo_View_Helper
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * SZend_Calendar
 *
 * @category   Zend
 * @package    SZend_Dojo_View_Helper
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class SZend_Dojo_View_Helper_Dojo_Extended extends Zend_Dojo_View_Helper_Dojo
{
	/**
	 * Holds the base path for scripts.
	 */
	protected static $_scriptBase = null;
	
	/**
	 * Dojo for static methods.
	 */
	protected static $_dojo = null;
	
	/**
	 * View for static methods.
	 */
	protected static $_view = null;
	
	/**
	 * Static setting for script base.
	 * @param string $scriptBase Path to the base script directory.
	 */
	public static function setScriptBase($scriptBase)
	{
		self::$_scriptBase = $scriptBase;
	}
	
	/**
	 * Static getter for script base.
	 * @return string
	 */
	public static function getScriptBase()
	{
		return self::$_scriptBase;
	}
	
	/**
	 * Adds a stylesheet based on the dojo path (local or cdn). 
	 * Method is static for situations where you need the stylesheets 
	 * to render even if even the dijits haven't been called but
	 * will during execution (AJAX calls).
	 * 
	 * @param string $stylesheet Path to stylesheet from base path.
	 */
	public static function addStylesheet($path)
	{
		// Set static view if necessary
		if (null === self::$_view) {
			$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
			self::$_view = $viewRenderer->view;
			self::$_dojo = self::$_view->dojo();
		}
		
		// Determine path to use
		if (null === self::getScriptBase()) {
			if (self::$_dojo->useLocalPath()) {
				$path = str_replace('\\', '/', self::$_dojo->getLocalPath());
				self::setScriptBase(str_replace('/dojo/dojo.js', '', $path));
			} else {
				self::setScriptBase(self::$_dojo->getCdnBase() . self::$_dojo->getCdnVersion());
			}
		}
		
		self::$_dojo->addStylesheet(self::getScriptBase() . $path);
	}
}