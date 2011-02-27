<?php
/**
 * SZend_Calendar_Render
 *
 * @package    	SZend_Calendar
 */
final class SZend_Calendar_Render
{
	const ADAPTER_ZENDVIEW = 'ZendView';
	const DEFAULT_ADAPTER = self::ADAPTER_ZENDVIEW;
	
	public static function factory(SZend_Calendar $calendar, array $options = array())
	{
		if (!array_key_exists('renderer', $options)) {
			$options['renderer'] = SZend_Calendar_Render::DEFAULT_ADAPTER;
		}
		
		if (!is_string($options['renderer']) or !strlen($options['renderer'])) {
			throw new Exception('Renderer name must be a string');
		}
		
		$rendererName = 'SZend_Calendar_Render_' . $options['renderer'];
		Zend_Loader::loadClass($rendererName);
		
		return new $rendererName($calendar, $options);
	}
}
