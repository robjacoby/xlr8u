<?php
/**
 * Default Zend View renderer
 *
 * @package		SZend_Calendar
 * @subpackage	Render
 */
class SZend_Calendar_Render_ZendView extends SZend_Calendar_Render_Abstract implements 
	SZend_Calendar_Render_Interface
{
	/**
	 * Default options
	 * @var array
	 */
	protected $_defaultOptions = array('viewScript' => 'calendar.phtml', 'scriptPath' => '');
	
	public function render()
	{
		// Setup view with calendar
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		
		$view = clone $viewRenderer->view;
		$view->clearVars();
		$view->calendar = $this->getCalendar();
		$view->addScriptPath($this->getScriptPath());
		
		// Generate list of events
		$weekList = array();
		foreach ($this->getCalendar()->getWeeks() as $k => $week) {
			foreach ($week as $day) {
				$weekList[$k][] = array(
					'day' => $day, 
					'events' => $this->getCalendar()->getDayEvents($day, SZend_Calendar_Event::ALL));
			}
		
		}
		
		$view->calendar->weekList = $weekList;
		
		return $view->render($this->getViewScript());
	}
}