<?php

/**
 * Description of My_Auth_Salt
 *
 * @author rjacoby
 */
class My_Auth_Salt
{
    /**
     * @var string
     */
    protected $_separator;

    /**
     * @var string
     */
    protected $_staticSaltString;

    /**
     * @var string
     */
    protected $_dynamicSaltString;

    /**
     * @var string
     */
    protected $_password;


    /**
     *
     * @param string $password
     * @param integer $length
     * @param string $staticSaltString
     * @param string $separator
     */
    public function __construct($password = null, $length = 40, $staticSaltString = 'NaCl=Salt', $separator = '-')
    {
        $this->_password = $password;
        $this->_staticSaltString = $staticSaltString;
        $this->_separator = $separator;
        $this->_dynamicSaltString = $this->generateCode($length);
    }

    /**
     * Set Password
     *
     * @param string $password
     * @return void
     */
    public function setPassword($password)
    {
        $this->_password = $password;
    }

    /**
     * Set Dynamic Salt String from db for password authentication
     *
     * @param string $saltstring
     * @return void
     */
    public function setDynamicSaltString($saltstring)
    {
        $this->_dynamicSaltString = $saltstring;
    }

    /**
     * Get Dynamic Salt String for db storage
     *
     * @return Dynamic Salt String
     */
    public function getDynamicSaltString()
    {
        return $this->_dynamicSaltString;
    }

    /**
     *
     * @return string sha1 encrypted value of staticSaltString.separator.dynamicSaltString.separator.password
     */
    public function getEncryptedPassword()
    {
        return sha1($this->_staticSaltString
                    . $this->_separator
                    . $this->_dynamicSaltString
                    . $this->_separator
                    . $this->_password
                );
    }

    /**
     * Generate a random string of $length characters.
     *
     * @param integer $length
     * @return string
     */
    public function generateCode($length=8)
    {
        $string = "";
        $possible = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

        for($i=0;$i < $length;$i++) {
            $char = $possible[mt_rand(0, strlen($possible)-1)];
            $string .= $char;
        }

        return $string;
    }
}