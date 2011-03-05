<?php

class IndexController extends Zend_Controller_Action
{

    public function init(){
        if (Zend_Auth::getInstance()->hasIdentity())
            $this->view->navigation()->setRole(Zend_Auth::getInstance()->getIdentity()->getRole());
    }

    public function indexAction()
    {
        
    }

    public function createAccountAction()
    {
        $config = new Zend_Config_Ini(APPLICATION_PATH
                                        . '/forms/user.ini', 'user');
        $this->view->form = new Zend_Form($config->user);

        if($this->getRequest()->isPost()) {
            $salt = new My_Auth_Salt($this->_getParam('password'), 40);
            $u = new Model_User();
            $u->username   = $this->_getParam('username');
            $u->password   = $salt->getEncryptedPassword();
            $u->saltstring = $salt->getDynamicSaltString();
            $u->name       = $this->_getParam('name');
            $u->address    = $this->_getParam('address');
            $u->phone      = $this->_getParam('phone');
            $u->email      = $this->_getParam('email');
            $u->save();
        }
    }

    public function phpinfoAction() {
        $this->_helper->layout()->disableLayout();
    }

}

