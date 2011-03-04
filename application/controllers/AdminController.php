<?php
/**
 * Description of AdminController
 *
 * @author rjacoby
 */
class AdminController extends Zend_Controller_Action
{
    public function init(){
        $this->view->navigation()->setRole(Zend_Auth::getInstance()->getIdentity()->getRole());
    }

    public function indexAction()
    {
        $this->view->user = Zend_Auth::getInstance()->getIdentity();
        $this->view->sessions = Model_Session::findAll();
        $this->view->users = Model_User::findAll();
    }

    public function changePasswordAction()
    {
        $config = new Zend_Config_Ini(APPLICATION_PATH
                                        . '/forms/user.ini', 'change-password');
        $this->view->form = new Zend_Form($config->user);

        if($this->getRequest()->isPost()) {
            $user = Zend_Auth::getInstance()->getIdentity();
            $salt = new My_Auth_Salt();
            $salt->setDynamicSaltString($user->saltstring);
            $salt->setPassword($this->_getParam('oldpassword'));
            $seasonedpassword = $salt->getEncryptedPassword();
            
            if($user->password == $seasonedpassword){
                $salt = new My_Auth_Salt($this->_getParam('newpassword'), 40);
                $user->saltstring = $salt->getDynamicSaltString();
                $user->password = $salt->getEncryptedPassword();
                $user->save();

                $this->_redirect('/admin/');
            } else {
                $this->view->message = 'Old Password does not match!';
            }
        }
    }

    public function generateInvoiceAction()
    {
        if ($this->getRequest()->isGet()) {
            $sessionid = $this->_getParam('id');
            $config = new Zend_Config_Ini(APPLICATION_PATH
                                        . '/forms/session.ini', 'invoice');
            $this->view->form = new Zend_Form($config->session);
            $this->view->form->sessionid->setValue($sessionid);
        } else if ($this->getRequest()->isPost()) {
            $inv = new Model_Invoice();
            
            $date = new Zend_Date;
            $inv->generationdate = $date->get(Zend_Date::W3C);
            $duedate = $date->add($this->_getParam('daystopay'),Zend_Date::DAY);
            $inv->duedate = $duedate->get(Zend_Date::W3C);
            $inv->amount = $this->_getParam('amount');
            $inv->save();
            $session = Model_Session::findOneById($this->_getParam('id'));
            $session->invoiceid = $inv->id;
            $session->save();

            $this->_redirect('/admin/');
        } else {
            $this->_redirect('/admin/');
        }
    }

    public function viewInvoiceAction()
    {
        if ($this->getRequest()->isGet()) {
            $sessionid = $this->_getParam('id');
            $session = Model_Session::findOneById($sessionid);
            $this->view->invoice = $session->Invoice;
            $this->view->user = $session->User;
            $this->view->session = $session;
        } else {
            $this->_redirect('/admin/');
        }
    }

    public function sessionAction()
    {
        if ($this->getRequest()->isGet()) {
            $sessionid = $this->_getParam('id');
            $session = Model_Session::findOneById($sessionid);
            $this->view->session = $session;

            $config = new Zend_Config_Ini(APPLICATION_PATH
                                        . '/forms/session.ini', 'result');
            $this->view->form = new Zend_Form($config->session);
            $this->view->form->sessionid->setValue($sessionid);

        } else if ($this->getRequest()->isPost()) {
            $sessionid = $this->_getParam('sessionid');
            
            $result = new Model_Result();
            $result->sessionid = $sessionid;
            $result->description = $this->_getParam('description');
            $result->value = $this->_getParam('value');
            $result->save();
            
            $session = Model_Session::findOneById($sessionid);
            
            $this->view->session = $session;
            $config = new Zend_Config_Ini(APPLICATION_PATH
                                        . '/forms/session.ini', 'result');
            $this->view->form = new Zend_Form($config->session);
            $this->view->form->sessionid->setValue($sessionid);
            
        } else {
            $this->_redirect('/admin/');
        }
    }

    public function generateDiaryAction()
    {
        if ($this->getRequest()->isPost()) {
            $userid = $this->_getParam('id');
            $user = Model_User::findById($userid);
            $diary = $user->Diaries;
            for($day = 0; $day < 7; $day++){
                $now = Zend_Date::now();
                $date = $now->addDay($day);
                $diary[]->dateField = $date->toString('yyyy-MM-dd');
            }
            $user->save();
        }
        exit;
    }
}
?>
