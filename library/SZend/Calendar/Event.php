<?php
/**
 * SZend Calendar event
 * 
 * @package		SZend_Calendar
 */
class SZend_Calendar_Event
{
	const ALL = 'all';
	const MULTI = 'multi';
	const SINGLE = 'single';
	
	/**
	 * Options
	 * @var array
	 */
	protected $_options = array();
	
	/**
	 * Default options
	 * @var array
	 */
	protected $_defaultOptions = array('startTimestamp' => null, 'endTimestamp' => null);
	
	/**
	 * Data
	 * @var array
	 */
	protected $_data = array();
	
	/**
	 * Constructor
	 * 
	 * @param array $event
	 * @param array $options
	 */
	public function __construct(array $data, array $options = array())
	{
		$this->setData($data);
		$this->setOptions($options);
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
	 * Getter
	 * 
	 * @param string $key
	 */
	public function __get($key)
	{
		return $this->_data[$key];
	}
	
	/**
	 * Setter
	 * 
	 * @param string $key
	 * @param string $value
	 */
	public function __set($key, $value)
	{
		$this->_data[$key] = $value;
		return $this;
	}
	
	/**
	 * Getter for data
	 * 
	 * @return array
	 */
	public function getData()
	{
		return $this->_data;
	}
	
	/**
	 * Setter for data
	 * 
	 * @param array $data
	 */
	public function setData($data)
	{
		$this->_data = $data;
		return $this;
	}
	
	/**
	 * Getter for options
	 * 
	 * @return array
	 */
	public function getOptions()
	{
		return $this->_options;
	}
	
	/**
	 * Setter for options
	 * 
	 * @param array $options
	 */
	public function setOptions(array $options = array())
	{
		// Enforce default options
		foreach ($this->_defaultOptions as $key => $value) {
			if (!isset($options[$key])) {
				$options[$key] = $value;
			}
		}
		
		// Set date/time
		$current = time();
		
		if (null === $options['startTimestamp']) {
			$options['startTimestamp'] = $current;
		}
		
		if (null === $options['endTimestamp']) {
			$options['endTimestamp'] = $current;
		}
		
		if ($options['endTimestamp'] < $options['startTimestamp']) {
			throw new Zend_Exception('Ending timestamp can not be before starting timestamp');
		}
		
		$this->_options = $options;
		return $this;
	}
}