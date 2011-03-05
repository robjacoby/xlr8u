<?php
/**
 * Description of UserController
 *
 * @author rjacoby
 */
class UserController extends Zend_Controller_Action {

    /**
     *
     * @var Model_User
     */
    protected $_user;
    
    public function  init() {
        $this->_user = Zend_Auth::getInstance()->getIdentity();
        $this->view->navigation()->setRole($this->_user->getRole());
        $this->view->headLink()->appendStylesheet('/css/user.css');
    }

    public function indexAction()
    {
        $this->view->user = $this->_user;
        $this->view->upcoming = Model_Session::getUpcoming($this->_user);
        $this->view->previous = Model_Session::getPrevious($this->_user, 2);
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

    public function sendMailAction()
    {
        if ($this->getRequest()->isPost()) {
            $mail = new Zend_Mail();
            $mail->addTo($this->_user->email, $this->_user->name);
            $mail->setBodyText('This is a test email!');
            $mail->setSubject('Test Email!!');
            $mail->send();

            echo "Mail Sent Successfully!";
            $this->_redirect('/user/');
        }
    }

    public function calendarAction() {
        $month = $this->_getParam('month', date('F'));
        $year = $this->_getParam('year', date('Y'));

        $this->view->user = Zend_Auth::getInstance()->getIdentity();
        $diaryEvents = Model_Diary::getEvents($this->view->user);
        $sessionEvents = Model_Session::getEvents($this->view->user);
        $calendar = new SZend_Calendar(array(), "$month $year");
        $calendar->addEvents('doctrine',array('collection'=>$diaryEvents), array('dateField' => 'dateField', 'title' => 'Food &amp; Exercise Diary'));
        $calendar->addEvents('doctrine',array('collection'=>$sessionEvents), array('dateField' => 'datetime', 'title' => 'PT Session'));
//        $this->view->calHeader = $calendar->getCalendarHeaderDataArray(
//                        'user', //controller
//                        'calendar' //action
//                );
        $this->view->calendar = $calendar;

        $this->view->lastTouched = false;

        $lastDiaryEvent = $diaryEvents->getLast();

        if (       $lastDiaryEvent->breakfast != null
                || $lastDiaryEvent->lunch != null
                || $lastDiaryEvent->dinner != null
                || $lastDiaryEvent->snacks != null
                || $lastDiaryEvent->exercise != null) {
            $this->view->lastTouched = true;
            $form = new Zend_Form();
            $form->setAction('/user/send-mail');
            $form->setMethod('post');
            $form->addElement(new Zend_Form_Element_Submit('submit', 'Send Email'));
            $this->view->form = $form;
        }
    }
}
?>
