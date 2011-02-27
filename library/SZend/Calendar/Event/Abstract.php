<?php
/**
 * SZend Calendar abstract class
 *
 * @package		SZend_Calendar
 * @subpackage	Event
 */
abstract class SZend_Calendar_Event_Abstract
{
	/**
	 * SZend_Calendar
	 * @var SZend_Calendar
	 */
	protected $_calendar = null;
	
	/**
	 * Event data
	 * @var array
	 */
	protected $_data = array();
	
	/**
	 * Event options
	 * @var array
	 */
	protected $_options = array();
	
	/**
	 * Constructor
	 * 
	 * @param SZend_Calendar $calendar
	 * @param array $data
	 * @param array $options
	 */
	public function __construct(SZend_Calendar $calendar, array $data = array(), array $options = array())
	{
		$this->_calendar = $calendar;
		$this->_data = $data;
		$this->_options = $options;
		
		$this->init();
	}
	
	/**
	 * Initilization method for children
	 */
	public function init()
	{}
}