<?php
/**
 * SZend_Calendar_Event_Render_Abstract
 *
 * @package		SZend_Calendar
 * @subpackage	Render
 */
class SZend_Calendar_Render_Abstract
{
	/**
	 * SZend_Calendar object
	 * @var SZend_Calendar|null
	 */
	protected $_calendar = null;
	
	/**
	 * Options for renderer
	 * @var array
	 */
	protected $_options = array();
	
	/**
	 * Default options
	 * @var array
	 */
	protected $_defaultOptions = array();
	
	/**
	 * Constructor
	 * 
	 * @param SZend_Calendar $calendar
	 * @param array $options
	 */
	public function __construct(SZend_Calendar $calendar, array $options = array())
	{
		$this->setCalendar($calendar);
		$this->setOptions($options);
		
		$this->init();
	}
	
	/**
	 * __call
	 * @param string $name
	 * @param array $args
	 */
	public function __call($name, $args)
	{
		$matches = array();
		if (preg_match('/^(set)(\w+)$/', $name, $matches) || preg_match('/^(get)(\w+)$/', $name, $matches)) {
			$option = $matches[2];
			$option[0] = strtolower($option[0]);
			
			if (array_key_exists($option, $this->_options)) {
				if ($matches[1] == 'get') {
					return $this->_options[$option];
				} else if ($matches[1] == 'set') {
					if (empty($args)) {
						throw new Zend_Exception(sprintf('No value specified for option "%s"', $option));
					}
					
					$this->_options[$option] = $args[0];
					return $this;
				}
			} else {
				throw new Zend_Exception(sprintf('No such option "%s" exists', $option));
			}
		}
		
		throw new Zend_Exception(sprintf('No such method "%s" exists', $name));
	}
	
	/**
	 * Initialization method for children
	 */
	public function init()
	{}
	
	/**
	 * @return SZend_Calendar
	 */
	public function getCalendar()
	{
		return $this->_calendar;
	}
	
	/**
	 * @return array
	 */
	public function getOptions()
	{
		return $this->_options;
	}
	
	/**
	 * @param array $options
	 */
	public function setOptions(array $options)
	{
		// Enforce default options
		foreach ($this->_defaultOptions as $key => $value) {
			if (!isset($options[$key])) {
				$options[$key] = $value;
			}
		}
		
		$this->_options = $options;
		return $this;
	}
	
	/**
	 * @param SZend_Calendar $calendar
	 */
	public function setCalendar(SZend_Calendar $calendar)
	{
		$this->_calendar = $calendar;
		return $this;
	}
}