<?php

/**
 * Description of My_Controller_Plugin_UserLoginControl
 *
 * @author rjacoby
 */
class My_Controller_Plugin_UserLoginControl extends Zend_Controller_Plugin_Abstract
{
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {

        $controller = $request->getControllerName();
        $action = $request->getActionName();

        if($controller != 'user'){
            return;
        }

        if(!Zend_Auth::getInstance()->hasIdentity()){
            $request->setControllerName('login');
            $request->setActionName('index');
            $request->setParam('message', 'You are trying to access a protected area.  Please log in first.');
            return;
        }
    }
}
?>
