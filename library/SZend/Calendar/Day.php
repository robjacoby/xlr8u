<?php
/**
 * SZend Calendar day
 *
 * @package		SZend_Calendar
 */
class SZend_Calendar_Day extends Zend_Date
{
	/**
	 * SZend_Calendar instance
	 * 
	 * @var SZend_Calendar
	 */
	protected $_calendar;

        protected $_class;
	
	/**
	 * Generates the standard date object, could be a unix timestamp, localized date,
	 * string, integer, array and so on. Also parts of dates or time are supported
	 * Always set the default timezone: http://php.net/date_default_timezone_set
	 * For example, in your bootstrap: date_default_timezone_set('America/Los_Angeles');
	 * For detailed instructions please look in the documentation.
	 *
	 * @param  SZend_Calendar $calendar					Instance of SZend_Calendar the day belongs to
	 * @param  string|integer|Zend_Date|array  $date    OPTIONAL Date value or value of date part to set
	 * ,depending on $part. If null the actual time is set
	 * @param  string                          $part    OPTIONAL Defines the input format of $date
	 * @param  string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
	 * @return Zend_Date
	 * @throws Zend_Date_Exception
	 */
	public function __construct($date = null, $part = null, $locale = null, $class = null)
	{
		parent::__construct($date, $part, $locale);
                $this->_class = $class;
	}
	
	/**
	 * toString
	 */
	public function __toString()
	{
		return $this->get(Zend_Date::DAY_SHORT);
	}
	
	/**
	 * Get calendar instance
	 * 
	 * @return SZend_Calendar_Day
	 */
	public function getCalendar()
	{
		return $this->_calendar;
	}
	
	/**
	 * Gets the events for this day
	 * 
	 * @return array
	 */
	public function getEvents()
	{
		return array();
	}

        public function getClass()
        {
            return $this->_class;
        }
	
	/**
	 * Determines if date is the current day
	 * 
	 * @return bool
	 */
	public function isToday()
	{
		return $this->get(Zend_Date::DATE_SHORT) == Zend_Date::now()->get(Zend_Date::DATE_SHORT);
	}
}