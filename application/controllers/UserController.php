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
}
?>
