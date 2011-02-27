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
 * @category   SZend
 * @package    SZend_Dojo_View_Helper_DataStore
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * SZend_Calendar
 *
 * @category   SZend
 * @package    SZend_Dojo_View_Helper_DataStore
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class SZend_Dojo_View_Helper_DataStore extends \Zend_Dojo_View_Helper_Dijit
{
	/**
	 * DataStore view helper.
	 * 
	 * @param string $id JavaScript id for the data store.
	 * @param string $dojoType DojoType of the data store (e.g., dojox.data.QueryReadStore)
	 * @param array $attribs Attributes for the data store.
	 */
	public function dataStore($id = '', $dojoType = '', array $attribs = array())
	{
		if (!$id || !$dojoType) {
			throw new Zend_Exception('Invalid arguments: required jsId and dojoType.');
		}
		
		$this->dojo->requireModule($dojoType);
		
		// Programmatic
		if ($this->_useProgrammatic()) {
			if (!$this->_useProgrammaticNoScript()) {
				$this->dojo->addJavascript('var ' . $id . ";\n");
				$js = $id . ' = ' . 'new ' . $dojoType . '(' . Zend_Json::encode($attribs) . ");";
				$this->dojo->_addZendLoad("function(){{$js}}");
			}
			return '';
		}
		
		// Set extra attribs for declarative
		if (!array_key_exists('id', $attribs)) {
			$attribs['id'] = $id;
		}
		
		if (!array_key_exists('jsId', $attribs)) {
			$attribs['jsId'] = $id;
		}
		
		if (!array_key_exists('dojoType', $attribs)) {
			$attribs['dojoType'] = $dojoType;
		}
		
		return '<div' . $this->_htmlAttribs($attribs) . "></div>\n";
	}
}