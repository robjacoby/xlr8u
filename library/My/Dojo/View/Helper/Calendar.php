<?php

/** Zend_Dojo_View_Helper_DijitContainer */
require_once 'Zend/Dojo/View/Helper/DijitContainer.php';

/**
 * Dojo Calendar dijit
 *
 * @uses       Zend_Dojo_View_Helper_DijitContainer
 * @package    My_Dojo
 * @subpackage View
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
  */
class My_Dojo_View_Helper_Calendar extends Zend_Dojo_View_Helper_DijitContainer
{
    /**
     * Dijit being used
     * @var string
     */
    protected $_dijit  = 'dijit.Calendar';

    /**
     * Module being used
     * @var string
     */
    protected $_module = 'dijit.Calendar';

    /**
     * dijit.Calendar
     *
     * @param  int $id
     * @param  string $content
     * @param  array $params  Parameters to use for dijit creation
     * @param  array $attribs HTML attributes
     * @return string
     */
    public function dojoCalendar($id = null, $content = '', array $params = array(), array $attribs = array())
    {
        if (0 === func_num_args()) {
            return $this;
        }

        return $this->_createLayoutContainer($id, $content, $params, $attribs);
    }
}
