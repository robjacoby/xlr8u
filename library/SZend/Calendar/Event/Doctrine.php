<?php
/**
 * SZend Calendar Doctrine event source
 *
 * @package		SZend_Calendar
 * @subpackage	Event
 */
class SZend_Calendar_Event_Doctrine extends SZend_Calendar_Event_Abstract implements 
	SZend_Calendar_Event_Interface
{
	/**
	 * Event getter for Doctrine events. Data must 
	 * include one of model, query, or collection.
	 * 
	 * Options should include at least a dateField 
	 * (or startDateField), optional endDateField and
	 * a dateFormat.
	 */
	public function getEvents()
	{
		$data = $this->_data;
		$options = $this->_options;
		
		// Setup options
		$startDateField = null;
		$endDateField = null;
		if (!array_key_exists('dateField', $options)) {
			if (!array_key_exists('startDateField', $options)) {
				throw new Zend_Exception('dateField or startDateField required');
			}
			
			$startDateField = $options['startDateField'];
		} else {
			$startDateField = $options['dateField'];
		}
		
		if (array_key_exists('endDateField', $options)) {
			$endDateField = $options['endDateField'];
		}
		
		// Generate and retrieve data
		if (empty($data)) {
			throw new Zend_Exception('No data was set');
		}
		
		if (array_key_exists('collection', $data)) {
			$collection = $data['collection'];
			
			if ($collection instanceof Doctrine_Collection) {
				$collection = $collection->toArray();
			}
		} else if (array_key_exists('query', $data)) {
			if (!$data['query'] instanceof Doctrine_Query) {
				throw new Zend_Exception('Invalid query type: expected Doctrine_Query');
			}
			
			$collection = $data['query']->fetchArray();
		} else if (array_key_exists('model', $data)) {
			$q = Doctrine_Query::create()->from($data['model']);
			$collection = $q->fetchArray();
		} else {
			throw new Zend_Exception('Unknown data type: collection, query, or model expected');
		}
		
		if (empty($collection)) {
			return array();
		}
		
		// Verify start field exists
		if (!array_key_exists($startDateField, $collection[0])) {
			throw new Zend_Exception(sprintf('Start field "%s" does not exist', $startDateField));
		}
		
		// Verify end field exists if specified
		if (null !== $endDateField && !array_key_exists($endDateField, $collection[0])) {
			throw new Zend_Exception(sprintf('End field "%s" does not exist', $endDateField));
		}
		
//		$collection = $this->_cleanCollection($collection,
//			array_merge($options['fields'], array($startDateField, $endDateField)));
                
                if (array_key_exists('title', $options)){
                    $title = $options['title'];
                } else {
                    $title = 'Default Title';
                }

                $events = array();
		foreach ($collection as $record) {
			$options = array();
			
			$options['startTimestamp'] = strtotime($record[$startDateField]);
                        if (isset($endDateField)){
                            $options['endTimestamp'] = strtotime($record[$endDateField]);

                            if ($options['endTimestamp'] < $options['startTimestamp']) {
                                    $options['endTimestamp'] = $options['startTimestamp'];
                            }
                        } else {
                            $options['endTimestamp'] = $options['startTimestamp'];
                        }

                        $record['title'] = $title;

			$events[] = new SZend_Calendar_Event($record, $options);
		}
		
		return $events;
	}
	
	/**
	 * Strips unused fields from collection
	 * 
	 * TODO: Is there a faster way to do this?
	 * 
	 * @param array $collection
	 * @param array $fields
	 */
	protected function _cleanCollection($collection, $fields)
	{
		$stripKeys = array_keys($collection[0]);
		$renameKeys = array();
		
		foreach ($stripKeys as $key => $value) {
			if (in_array($value, $fields) || array_key_exists($value, $fields)) {
				if (array_key_exists($value, $fields)) {
					$renameKeys[$value] = $fields[$value];
				}
				
				unset($stripKeys[$key]);
			}
		}
		
		foreach ($collection as $recKey => $record) {
			foreach ($stripKeys as $stripKey) {
				if (array_key_exists($stripKey, $record)) {
					unset($collection[$recKey][$stripKey]);
				}
			}
			
			foreach ($renameKeys as $renameKey => $renameValue) {
				$collection[$recKey][$renameValue] = $collection[$recKey][$renameKey];
				unset($collection[$recKey][$renameKey]);
			}
		}
		
		return $collection;
	}
}