<?php
/**
 * Description of UserController
 *
 * @author rjacoby
 */
class UserController extends Zend_Controller_Action {

    public function indexAction()
    {
        $this->view->user = Zend_Auth::getInstance()->getIdentity();
        $this->view->diaryEvents = Model_Diary::getEvents($this->view->user);
        $this->view->sessionEvents = Model_Session::getEvents($this->view->user);
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

                $this->_redirect('/user/');
            } else {
                $this->view->message = 'Old Password does not match!';
            }
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
            $this->_redirect('/user/');
        }
    }

    public function sessionAction()
    {
        if ($this->getRequest()->isGet()) {
            $sessionid = $this->_getParam('id');
            $session = Model_Session::findOneById($sessionid);
            $this->view->session = $session;
        } else {
            $this->_redirect('/user/');
        }
    }

    public function diaryAction()
    {
        $config = new Zend_Config_Ini(APPLICATION_PATH
                                        . '/forms/user.ini', 'diary');
        $this->view->form = new Zend_Form($config->user);
        if($this->getRequest()->isPost()) {
            $diary = Model_Diary::getDiary($this->_getParam('id'));
            $diary->breakfast = $this->_getParam('breakfast');
            $diary->lunch = $this->_getParam('lunch');
            $diary->dinner = $this->_getParam('dinner');
            $diary->snacks = $this->_getParam('snacks');
            $diary->exercise = $this->_getParam('exercise');
            $diary->save();
            $this->_redirect('/user/');
        } else if ($this->getRequest()->isGet()) {
            $diaryid = $this->_getParam('id');
            $diary = Model_Diary::getDiary($diaryid);
            $this->view->diary = $diary;
            $this->view->user = $diary->User;
            $this->view->form->id->setValue($diaryid);
            $this->view->form->breakfast->setValue($diary->breakfast);
            $this->view->form->lunch->setValue($diary->lunch);
            $this->view->form->dinner->setValue($diary->dinner);
            $this->view->form->snacks->setValue($diary->snacks);
            $this->view->form->exercise->setValue($diary->exercise);
        } else {
            $this->_redirect('/user/');
        }
    }
}
?>
