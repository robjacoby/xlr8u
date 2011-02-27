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
 * @package    SZend_Dojo_View_Helper_DataGrid
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * SZend_Calendar
 *
 * @category   Zend
 * @package    SZend_Dojo_View_Helper_DataGrid
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class SZend_Dojo_View_Helper_DataGrid extends Zend_Dojo_View_Helper_Dijit
{
	/**
	 * Ensures only one script capture is active at a time.
	 * @var bool
	 */
	protected $_captureLock = false;
	
	/**
	 * Holds the attributes for the grid.
	 * @var array
	 */
	protected $_attribs = array();
	
	/**
	 * Holds the field data.
	 * @var array
	 */
	protected $_fields = array();
	
	/**
	 * Holds the data for the current script capture.
	 */
	protected $_currentScript = array();
	
	/**
	 * Holds the data for script captures.
	 * @var array
	 */
	protected $_scriptCapture = array();
	
	/**
	 * Holds the tab character.
	 * @var string
	 */
	protected $_tab = '    ';
	
	/**
	 * Holds the current theme.
	 */
	protected static $_theme = 'tundra';
	
	/**
	 * __toString();
	 */
	public function __toString()
	{
		return $this->render();
	}
	
	/**
	 * DataGrid view helper.
	 * 
	 * @param string $id JavaScript id for the data store.
	 * @param array $attribs Attributes for the data store.
	 */
	public function dataGrid($id = '', array $attribs = array())
	{
		if (!$id) {
			throw new Zend_Exception('Invalid arguments: required jsId.');
		}
		
		if (!array_key_exists('id', $attribs)) {
			$attribs['id'] = $id;
		}
		
		if (array_key_exists('fields', $attribs)) {
			foreach ($attribs['fields'] as $f) {
				$this->addField($f['field'], $f['label'], isset($f['options']) ? $f['options'] : array());
			}
			unset($attribs['fields']);
		}
		
		$this->_attribs = $attribs;
		
		return $this;
	}
	
	/**
	 * Static setter for theme.
	 * @param string $theme Name of the current them.
	 */
	public static function setTheme($theme)
	{
		self::$_theme = $theme;
	}
	
	/**
	 * Static getter for theme.
	 * @return string
	 */
	public static function getTheme()
	{
		return self::$_theme;
	}
	
	/**
	 * Adds field data.
	 * @param string $field Field name.
	 * @param string $label Label of the field.
	 * $param array $attribs Optional parameters for the field.
	 */
	public function addField($field, $label, array $attribs = array())
	{
		$this->_fields[] = array(
			'label' => $label, 
			'attribs' => array_merge(array('field' => $field), $attribs));
		return $this;
	}
	
	/**
	 * Adds script captures.
	 * @param array $data
	 */
	public function addScriptCapture(array $script = array())
	{
		if (!array_key_exists('data', $script)) {
			throw new Zend_Exception('Script data must include keys data and attribs');
		}
		
		$this->_scriptCapture[] = $script;
		return $this;
	}
	
	/**
	 * Begins script capturing.
	 */
	public function scriptCaptureStart($type, $event, array $attribs = array())
	{
		if ($this->_captureLock) {
			throw new Zend_Exception('Cannot nest captures.');
		}
		
		$this->_currentScript = array('type' => $type, 'event' => $event, 'attribs' => $attribs);
		
		$this->_captureLock = true;
		ob_start();
		return;
	}
	
	/**
	 * Ends script capturing.
	 */
	public function scriptCaptureEnd()
	{
		$data = ob_get_clean();
		$this->_captureLock = false;
		
		$this->_currentScript['data'] = $data;
		
		$this->addScriptCapture($this->_currentScript);
		$this->_currentScript = array();
		
		return true;
	}
	
	/**
	 * Renders the grid based on programmatic setting.
	 */
	public function render()
	{
		$this->dojo->requireModule('dojox.grid.DataGrid');
		
		// Setup the stylesheets
		self::renderStylesheets();
		
		// Programmatic
		if ($this->_useProgrammatic()) {
			if (!$this->_useProgrammaticNoScript()) {
				$this->_renderJavascript();
			}
			return '<div id="' . $this->_attribs['id'] . '"></div>';
		}
		
		return $this->_renderDeclarative();
	}
	
	/**
	 * Renders stylesheets. Static method so they can be rendered once
	 * in a page with delayed execution (AJAX calls).
	 */
	public static function renderStylesheets()
	{
		SZend_Dojo_View_Helper_Dojo_Extended::addStylesheet('/dojox/grid/resources/Grid.css');
		SZend_Dojo_View_Helper_Dojo_Extended::addStylesheet(
			'/dojox/grid/resources/' . self::getTheme() . 'Grid.css');
	}
	
	/**
	 * Renders a table for declarative grids.
	 */
	protected function _renderDeclarative()
	{
		if (!array_key_exists('jsId', $this->_attribs)) {
			$this->_attribs['jsId'] = $this->_attribs['id'];
		}
		
		$table = '<table dojoType="dojox.grid.DataGrid"' . $this->_htmlAttribs($this->_attribs) . ">\n";
		
		foreach ($this->_scriptCapture as $s) {
			$attribs = array_merge($s['attribs'], array('event' => $s['event'], 'type' => $s['type']));
			$table .= "\t<script" . $this->_htmlAttribs($attribs) . ">\n";
			$table .= "\t\t" . $s['data'];
			$table .= "\t</script>\n";
		}
		
		$table .= "\t<thead>\n";
		$table .= "\t\t<tr>\n";
		
		foreach ($this->_fields as $f) {
			$table .= "\t\t\t<th" . $this->_htmlAttribs($f['attribs']) . '>' . $f['label'] . "</th>\n";
		}
		
		$table .= "\t\t</tr>\n";
		$table .= "\t</thead>\n";
		$table .= "</table>\n";
		
		return $table;
	}
	
	/**
	 * Renders javascript for programmatic declaration.
	 */
	protected function _renderJavascript()
	{
		$tab = $this->_tab;
		
		// Grid names
		$gridName = $this->_attribs['id'] . 'Grid';
		$layoutName = $this->_attribs['id'] . 'Layout';
		
		$this->dojo->addJavascript('var ' . $gridName . ";\n");
		
		// Setup layout
		$js = $tab . 'var ' . $layoutName . " = [\n";
		foreach ($this->_fields as $f) {
			$f['attribs']['name'] = $f['label'];
			
			$f['attribs'] = $this->_jsonExpr($f['attribs']);
			$js .= "{$tab}{$tab}{$tab}" . Zend_Json::encode($f['attribs'], false, 
				array('enableJsonExprFinder' => true)) . ",\n";
		}
		
		$js = substr($js, 0, -2);
		$js .= "];\n\n";
		
		// Use expressions for structure, store, formatter, and get
		$this->_attribs = $this->_jsonExpr($this->_attribs);
		$this->_attribs['structure'] = new Zend_Json_Expr($layoutName);
		
		// Generate grid
		$js .= $tab . $tab . $gridName . ' = ' . 'new dojox.grid.DataGrid(' . Zend_Json::encode(
			$this->_attribs, false, array('enableJsonExprFinder' => true)) . "), document.createElement('div');\n";
		
		$js .= $tab . $tab . 'dojo.byId("' . $this->_attribs['id'] . '").appendChild(' . $gridName . '.domNode);' .
			 "\n";
		$js .= $tab . $tab . $gridName . ".startup();\n";
		
		// Generate connects for script captures
		foreach ($this->_scriptCapture as $s) {
			$s['data'] = trim($s['data'], "\r\n");
			$args = isset($s['attribs']['args']) ? $s['attribs']['args'] : '';
			
			$js .= "{$tab}{$tab}dojo.connect({$gridName}, \"{$s['event']}\", function({$args}){{$s['data']}});\n";
		}
		
		$this->dojo->_addZendLoad("function(){\n{$tab}{$js}\n{$tab}}");
	}
	
	/**
	 * Parses an array looking for keys that should be Zend_Json_Expr()'s and coverts them.
	 */
	protected function _jsonExpr(array $data)
	{
		$jsonExprAttr = array('formatter', 'get', 'query', 'store', 'type');
		
		foreach ($jsonExprAttr as $exp) {
			if (array_key_exists($exp, $data)) {
				$data[$exp] = new Zend_Json_Expr($data[$exp]);
			}
		}
		
		return $data;
	}
}