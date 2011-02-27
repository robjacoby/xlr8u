<?php
/**
 * Primary class for SZend_Calendar
 *
 * @package		SZend_Calendar
 */
class SZend_Calendar
{
	const SUNDAY = 0;
	const MONDAY = 1;
	const TUESDAY = 2;
	const WEDNESDAY = 3;
	const THURSDAY = 4;
	const FRIDAY = 5;
	const SATURDAY = 6;
	
	/**
	 * Options for calendar
	 * @var array
	 */
	protected $_options = array();
	
	/**
	 * Calendar date
	 * @var null|Zend_Date
	 */
	protected $_date = null;
	
	/**
	 * Calendar day names
	 * @var array
	 */
	protected $_dayNames = array();
	
	/**
	 * Calendar month names
	 * @var array
	 */
	protected $_monthNames = array();
	
	/**
	 * Events for the calendar
	 * @var array
	 */
	protected $_events = array();
	
	/**
	 * Events for a day
	 * @var array
	 */
	protected $_dayEvents = array();
	
	/**
	 * Weeks of the calendar
	 * @var array
	 */
	protected $_weeks = array();
	
	/**
	 * Default options
	 * @var array
	 */
	protected $_defaultOptions = array('startOfWeek' => self::SUNDAY);
	
	/**
	 * Zend_Controller_Request
	 * @var null|Zend_Controller_Request_Abstract
	 */
	protected $_request = null;

        /**
         *
         * @var integer Start of the Week
         */
        protected $_startOfWeek = self::SUNDAY;

        /**
         *
         * @var integer End of the Week
         */
        protected $_endOfWeek = self::SATURDAY;


        /**
	 * Constructor
	 * @param array $options
	 */
	public function __construct(array $options = array())
	{
		$this->_request = Zend_Controller_Front::getInstance()->getRequest();
		
		// Generate calendar timestamp
		if (array_key_exists('timestamp', $options)) {
			$timestamp = $options['timestamp'];
		} else {
			// Default date based on current
			$date = array(
				'hour' => (int) date('h'), 
				'minute' => (int) date('i'), 
				'second' => (int) date('s'), 
				'month' => (int) date('m'), 
				'day' => (int) date('d'), 
				'year' => (int) date('Y'));
			
			// Timestamp from parameters
			if ($this->_request->getParam('second')) {
				$date['second'] = $this->_request->getParam('second');
			}
			if ($this->_request->getParam('minute')) {
				$date['minute'] = $this->_request->getParam('minute');
			}
			if ($this->_request->getParam('hour')) {
				$date['hour'] = $this->_request->getParam('hour');
			}
			if ($this->_request->getParam('day')) {
				$date['day'] = $this->_request->getParam('day');
			}
			if ($this->_request->getParam('month')) {
				$date['month'] = $this->_request->getParam('month');
			}
			if ($this->_request->getParam('year')) {
				$date['year'] = $this->_request->getParam('year');
			}
			
			// Finally merge with any date options
			$date = array_merge($date, 
				array_key_exists('date', $options) ? $options['date'] : array());
			
			// Generate the timestamp
			$timestamp = mktime($date['hour'], $date['minute'], $date['second'], 
				$date['month'], $date['day'], $date['year']);
		}
		
		$this->setDate($timestamp);
		$this->setOptions($options);
		
		// Caching
		$cache = null;
		if (array_key_exists('autoCache', $options)) {
			if (extension_loaded('apc')) {
				$cache = Zend_Cache::factory('Core', 'apc');
			}
		} else if (array_key_exists('cache', $options)) {
			$cache = $options['cache'];
		} else if (Zend_Registry::isRegistered('SZend_Calendar_Cache')) {
			$cache = Zend_Registry::get('SZend_Calendar_Cache');
		}
		
		if (null !== $cache) {
			if (!$cache instanceof Zend_Cache_Core) {
				throw new Zend_Exception('Instance of Zend_Cache expected');
			}
			
			$this->_date->setOptions(array('cache' => $cache));
		}
		
		$this->_generateWeeks();
		$this->init();
	}
	
	/**
	 * __call
	 * 
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
	 * Serialize as string
	 * 
	 * Proxies to {#link render()}
	 * 
	 * @return string
	 */
	public function __toString()
	{
		try {
			return $this->render();
		} catch (Exception $e) {
			trigger_error($e->getMessage(), E_USER_WARNING);
		}
		
		return '';
	}
	
	/**
	 * Initialization method for children
	 */
	public function init()
	{}
	
	/**
	 * Render block
	 *
	 * @param array $options
	 * @return string
	 */
	public function render()
	{
		return SZend_Calendar_Render::factory($this, $this->_options)->render();
	}
	
	/**
	 * Add event to calendar
	 * 
	 * @param SZend_Calendar_Event $event
	 */
	public function addEvent(SZend_Calendar_Event $event)
	{
		$startTimestamp = $event->getStartTimestamp();
		$endTimestamp = $event->getEndTimestamp();
		
		$day = (int) strftime('%d', $startTimestamp);
		$month = (int) strftime('%m', $startTimestamp);
		$year = (int) strftime('%Y', $startTimestamp);
		
		$span = $this->dateDiff(date('c', $startTimestamp), date('c', $endTimestamp));
		$days = $span + 1;
		
		// Add regular event
		$event->eventSpan = $span;
		$event->eventDays = $days;
		$this->_events[] = $event;
		
		$eventId = count($this->_events) - 1;
		
		// Add event id
		$date = new Zend_Date($startTimestamp);
		$event = array('id' => $eventId, 'days' => $days, 'span' => $span);
		$this->_dayEvents[$year][$month][(int) $date->get(Zend_Date::DAY_SHORT)][SZend_Calendar_Event::MULTI][] = $event;
	}
	
	/**
	 * Adds all events from a source to the calendar
	 * 
	 * @param string|Zend_Calendar_Event_Abstract
	 * @param array $data
	 * @param array $options
	 */
	public function addEvents($source, array $data = array(), array $options = array())
	{
		if (is_string($source)) {
			$className = 'SZend_Calendar_Event_' . ucfirst($source);
			
			Zend_Loader::loadClass($className);
			$class = new $className($this, $data, $options);
			$events = $class->getEvents();
		}
		
		foreach ($events as $event) {
			$this->addEvent($event);
		}
	}
	
	/**
	 * Populates calendar day names from translation list and caches them.
	 *
	 * @param array $format
	 * @return array
	 */
	public function getDayNames(array $format = array())
	{
		if (empty($this->_dayNames)) {
			$dayNames = Zend_Locale::getTranslationList('day', $this->_date->getLocale(), $format);
			
			$start = $this->_startOfWeek;
                        $end = $this->_endOfWeek;
                        $counter = 0;
			
                        $calWeekdays = array();

                        foreach ($dayNames as $dayShort => $dayLong){
                            if ($counter == $start) {
                                $class = 'first';
                            } elseif ($counter == $end) {
                                $class = 'last';
                            }
                            $counter++;
                            array_push($calWeekdays,
                                    array(
                                        'class' => $class,
                                        'name' => strtoupper($dayShort)
                                      ));
                        }

			$this->_dayNames = $calWeekdays;
		}
		
		return $this->_dayNames;
	}
	
	/**
	 * Populates calendar's month names from translation list and caches them.
	 *
	 * @param array $format
	 * @return array
	 */
	public function getMonthNames(array $format = array())
	{
		if (empty($this->_monthNames)) {
			$months = Zend_Locale::getTranslationList('month', $this->_date->getLocale(), $format);
			
			foreach ($months as $month) {
				$this->_monthNames[] = $month;
			}
		}
		
		return $this->_monthNames;
	}
	
	/**
	 * Gets events for a day. Can take day, month, and year inputs or
	 * a SZend_Calendar_Day. Can also accept a type of event to return
	 * (single, multi, or all).
	 * 
	 * @param int|SZend_Calendar_Day $date
	 * @param null|int|string $arg2
	 * @param null|int $arg3
	 * @param null|string $arg4
	 */
	public function getDayEvents($date, $arg2 = null, $arg3 = null, $arg4 = null)
	{
		// Verify type of date
		if ($date instanceof SZend_Calendar_Day) {
			$day = $date->get(Zend_Date::DAY);
			$month = $date->get(Zend_Date::MONTH_SHORT);
			$year = $date->get(Zend_Date::YEAR);
			
			$type = $arg2;
		} else {
			if (null === $arg2) {
				$month = $this->_date->get(Zend_Date::MONTH_SHORT);
			}
			
			if (null === $arg3) {
				$year = $this->_date->get(Zend_Date::YEAR);
			}
			
			$type = $arg4;
		}
		
		// Force integer values
		$day = (int) $day;
		$month = (int) $month;
		$year = (int) $year;
		
		// Ensure type is set correctly, else default to all events
		switch ($type) {
			case SZend_Calendar_Event::SINGLE:
			case SZend_Calendar_Event::MULTI:
				break;
			default:
				$type = SZend_Calendar_Event::ALL;
				break;
		}
		
		// No events exist
		if (!isset($this->_dayEvents[$year][$month][$day])) {
			return array();
		}
		
		// Return merged array of single and multiple events
		$dayEvent = $this->_dayEvents[$year][$month][$day];
		if ($type == SZend_Calendar_Event::ALL) {
			$singles = isset($dayEvent[SZend_Calendar_Event::SINGLE]) ? $dayEvent[SZend_Calendar_Event::SINGLE] : array();
			$multis = isset($dayEvent[SZend_Calendar_Event::MULTI]) ? $dayEvent[SZend_Calendar_Event::MULTI] : array();
			
			return array_merge($multis, $singles);
		}
		
		// Single event types
		if (!isset($dayEvent[$type])) {
			return array();
		}
		
		return $dayEvent[$type];
	}
	
	/**
	 * Getter for days events
	 * 
	 * @return bool
	 */
	public function getDaysEvents()
	{
		return $this->_dayEvents;
	}
	
	/**
	 * Wrapper for _date->get()
	 * 
	 * @param string $part
	 * @param string|Zend_Locale $locale
	 * @return string
	 */
	public function get($part = null, $locale = null)
	{
		return $this->_date->get($part, $locale);
	}
	
	/**
	 * Gets all days
	 * 
	 * @return array
	 */
	public function getDays()
	{
		return $this->_days;
	}
	
	/**
	 * Gets an event
	 * 
	 * @param int $index
	 */
	public function getEvent($index)
	{
		return $this->_events[$index];
	}
	
	/**
	 * Gets a day from the calendar. If no such day
	 * exists calls set day to initialize a day.
	 * 
	 * @param int $day 
	 * @param int $month
	 * @param int $year
	 */
	public function getDay($day, $month = null, $year = null)
	{
		if (null === $month) {
			$month = $this->_date->get(Zend_Date::MONTH);
		}
		
		if (null === $year) {
			$year = $this->_date->get(Zend_Date::YEAR);
		}
		
		$day = (int) $day;
		$month = (int) $month;
		$year = (int) $year;
		
		if (!isset($this->_days[$year][$month][$day])) {
			$this->setDay(mktime(0, 0, 0, $month, $day, $year));
		}
		
		return $this->_days[$year][$month][$day];
	}
	
	/**
	 * Sets a day
	 * 
	 * @param string|integer|Zend_Date|array $date
	 * @param string $part
	 * @param string|Zend_Locale $locale
	 */
	public function setDay($date, $part = null, $locale = null)
	{
		$day = new SZend_Calendar_Day($date, $part, $locale);
		$this->_days[(int) $day->get(Zend_Date::YEAR)][(int) $day->get(Zend_Date::MONTH_SHORT)][(int) $day->get(
			Zend_Date::DAY_SHORT)] = $day;
		
		return $this;
	}
	
	/**
	 * Get a particular week
	 * 
	 * @param int $week
	 * @return array
	 */
	public function getWeek($week)
	{
		return $this->_weeks[$week];
	}
	
	/**
	 * Get weeks
	 * 
	 * @return array
	 */
	public function getWeeks()
	{
		return $this->_weeks;
	}
	
	/**
	 * Get calendar date
	 * 
	 * @return null|Zend_Date
	 */
	public function getDate()
	{
		return $this->_date;
	}
	
	/**
	 * Set the calendar date
	 * 
	 * @param string|integer|Zend_Date|array $date
	 * @param string $part
	 * @param string|Zend_Locale $locale
	 */
	public function setDate($date = null, $part = null, $locale = null)
	{
		$this->_date = new Zend_Date($date, $part, $locale);
		return $this;
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
	 * Generate weeks of the month (use native PHP)
	 */
	protected function _generateWeeks()
	{
		$m = $this->_date->get(Zend_Date::MONTH);
		$Y = $this->_date->get(Zend_Date::YEAR);
		
		$daysInMonth = $this->_date->get(Zend_Date::MONTH_DAYS);
		$firstDay = date('w', strtotime(date($m . '/01/' . $Y)));
		
		// Add days from previous month to beginning
		$endOfDays = date('t', strtotime(date(($m - 1) . '/01/' . $Y)));
		$prevMonth = ($m == 1) ? $m = 12 : $m - 1;
		
		for ($i = 0; $i < $firstDay; $i++) {
			$this->_weeks[1][$i] = $this->getDay($endOfDays--, $m - 1);
		}
		
		// Current months weeks
		for ($i = 1, $j = $firstDay, $w = 1; $i <= $daysInMonth; $i++) {
			$this->_weeks[$w][$j] = $this->getDay($i);
			$j++;
			if ($j == 7) {
				$j = 0;
				$w++;
			}
		}
		
		// Add days from next month to end
		$lastIndex = count($this->_weeks);
		$nextMonth = ($m == 12) ? $m = 1 : $m + 1;
		for ($i = $j; $i < 7; $i++) {
			$this->_weeks[$lastIndex][$i] = $this->getDay(($i - $j) + 1, $nextMonth);
		}
	}
	
	/**
	 * Finds the difference in days between two calendar dates.
	 */
	public function dateDiff($startDate, $endDate)
	{
		// Parse dates for conversion 
		$startArry = date_parse($startDate);
		$endArry = date_parse($endDate);
		
		// Convert dates to Julian Days 
		$start_date = gregoriantojd($startArry["month"], $startArry["day"], $startArry["year"]);
		$end_date = gregoriantojd($endArry["month"], $endArry["day"], $endArry["year"]);
		
		// Return difference 
		return round(($end_date - $start_date), 0);
	}
	
	/**
	 * Returns a date in RFC3339 format
	 * 
	 * @param int $timestamp
	 */
	public function date3339($timestamp)
	{
		$date = date('Y-m-d\TH:i:s', $timestamp);
		
		$matches = array();
		if (preg_match('/^([\-+])(\d{2})(\d{2})$/', date('O', $timestamp), $matches)) {
			$date .= $matches[1] . $matches[2] . ':' . $matches[3];
		} else {
			$date .= 'Z';
		}
		return $date;
	
	}
}