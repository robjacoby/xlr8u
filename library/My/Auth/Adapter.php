<?php

/**
 * Description of My_Auth_Adapter
 * Auth Adapter integrating Doctrine and Zend_Auth
 *
 * @author rjacoby
 */
class My_Auth_Adapter implements Zend_Auth_Adapter_Interface {

    const NOT_FOUND_MSG = "Account Not Found";
    const BAD_PW_MSG = "Incorrect Password";
    
    /**
     * @var Model_User
     */
    protected $user;

    /**
     * @var string
     */
    protected $username = "";

    /**
     * @var string
     */
    protected $password = "";

    public function __construct($username,$password) {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @throws Zend_Auth_Adapter_Exception If authentication cannot be performed
     * @return Zend_Auth_Result
     */
    public function authenticate() {
        try {
            $this->user = Model_User::authenticate($this->username, $this->password);
            return $this->createResult(Zend_Auth_Result::SUCCESS);
        } catch (Exception $exc) {
            if($exc->getMessage() == Model_User::WRONG_PW){
                return $this->createResult(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, array(self::BAD_PW_MSG));
            }
            if($exc->getMessage() == Model_User::NOT_FOUND){
                return $this->createResult(Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND, array(self::NOT_FOUND_MSG));
            }
        }
    }

    /**
     * @param const $code
     * @param array $messages
     * @return Zend_Auth_Result 
     */
    private function createResult($code, $messages = array()) {
        return new Zend_Auth_Result($code, $this->user, $messages);
    }
}
?>
