<?php
/**
 * SZend Calendar array event source
 *
 * @package		SZend_Calendar
 * @subpackage	Event
 */
class SZend_Calendar_Event_Array extends SZend_Calendar_Event_Abstract implements 
	SZend_Calendar_Event_Interface
{
	public function getEvents()
	{
		if (empty($this->_data)) {
			throw new Zend_Exception('No data was set');
		}
		
		$events = array();
		foreach ($this->_data as $data) {
			$options = array();
			
			if (array_key_exists('startDate', $data)) {
				$options['startTimestamp'] = strtotime($data['startDate']);
			}
			
			if (array_key_exists('endDate', $data)) {
				$options['endTimestamp'] = strtotime($data['endDate']);
			}
			
			if ($options['endTimestamp'] < $options['startTimestamp']) {
				$options['endTimestamp'] = $options['startTimestamp'];
			}
			
			$events[] = new SZend_Calendar_Event($data, $options);
		}
		
		return $events;
	}
}