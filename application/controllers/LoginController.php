<?php

class LoginController extends Zend_Controller_Action {

    public function indexAction()
    {
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/forms/login.ini', 'login');
        $this->view->form = new Zend_Form($config->login);

        if($this->getRequest()->isPost()){
            $adapter = new My_Auth_Adapter($this->_getParam('username'), $this->_getParam('password'));
            $result = Zend_Auth::getInstance()->authenticate($adapter);

            if(Zend_Auth::getInstance()->hasIdentity()){
                if(Zend_Auth::getInstance()->getIdentity()->username == 'admin'){
                    Zend_Auth::getInstance()->getIdentity()->setRole(My_Acl_Roles::ADMIN);
                    $this->_redirect('/admin/');
                } else {
                    Zend_Auth::getInstance()->getIdentity()->setRole(My_Acl_Roles::FREE);
                    $this->_redirect('/user/');
                }
            } else {
                $this->view->message = implode(' ', $result->getMessages());
            }
        }

        if($this->_getParam('message'))
            $this->view->message = $this->_getParam('message');
    }

    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_redirect('/');
    }

}
?>
