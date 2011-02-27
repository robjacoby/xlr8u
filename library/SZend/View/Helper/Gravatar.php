<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    SZend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: Doctype.php 16971 2009-07-22 18:05:45Z mikaelkael $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Helper for retrieving the avatars from gravatar.com
 *
 * @package    SZend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @link http://pl.gravatar.com/site/implement/url
 */
class SZend_View_Helper_Gravatar extends Zend_View_Helper_HtmlElement
{
	
	/**
	 * URL to gravatar service
	 */
	const GRAVATAR_URL = 'http://www.gravatar.com/avatar';
	/**
	 * Secure URL to gravatar service
	 */
	const GRAVATAR_URL_SECURE = 'https://secure.gravatar.com/avatar';
	
	/**
	 * Gravatar rating
	 */
	const GRAVATAR_RATING_G = 'g';
	const GRAVATAR_RATING_PG = 'pg';
	const GRAVATAR_RATING_R = 'r';
	const GRAVATAR_RATING_X = 'x';
	
	/**
	 * Options
	 *
	 * @var array
	 */
	protected $_options = array('imgSize' => 80, 'defaultImg' => 'wavatar', 'rating' => 'g');
	
	/**
	 * Returns an avatar from gravatar's service.
	 *
	 * @link http://pl.gravatar.com/site/implement/url
	 * @throws Zend_View_Exception
	 *
	 * @param string|null $email Valid email adress
	 * @param null|array $options Options
	 * 'imgSize' height of img to return
	 * 'defaultImg' img to return if email adress has not found
	 * 'rating' rating parametr for avatar
	 * @param null|array $attribs Attribs for img tag (title, alt etc.)
	 * @param null|bool Use HTTPS? Default false.
	 * @link http://pl.gravatar.com/site/implement/url More information about gravatar's service.
	 * @return string|Zend_View_Helper_Gravatar
	 */
	public function gravatar($email = null, $options = array(), $attribs = array(), $flag = false)
	{
		if ($email === null) {
			return '';
		}
		
		if (count($options) > 0) {
			if (isset($options['imgSize']))
				$this->setImgSize($options['imgSize']);
			if (isset($options['defaultImg']))
				$this->setDefaultImg($options['defaultImg']);
			if (isset($options['rating']))
				$this->setRating($options['rating']);
		}
		
		/**
		 * @see Zend_Validate_EmailAddress
		 */
		require_once 'Zend/Validate/EmailAddress.php';
		
		$validatorEmail = new Zend_Validate_EmailAddress();
		$validatorResult = $validatorEmail->isValid($email);
		
		if ($validatorResult === false) {
			/**
			 * @see Zend_View_Exception
			 */
			require_once 'Zend/View/Exception.php';
			throw new Zend_View_Exception(current($validatorEmail->getMessages()));
		}
		
		$hashEmail = md5($email);
		$src = $this->getGravatarUrl($flag) . '/' . $hashEmail . '?s=' . $this->getImgSize() . '&d=' . $this->getDefaultImg() .
			 '&r=' . $this->getRating();
		
		$attribs['src'] = $src;
		
		$html = '<img' . $this->_htmlAttribs($attribs) . $this->getClosingBracket();
		
		return $html;
	}
	
	/**
	 * Gets img size
	 *
	 * @return int The img size
	 */
	public function getImgSize()
	{
		return $this->_options['imgSize'];
	}
	
	/**
	 * Sets img size
	 *
	 * @throws Zend_View_Exception
	 * @param int $imgSize Size of img must be between 1 and 512
	 * @return Zend_View_Helper_Gravatar
	 */
	public function setImgSize($imgSize)
	{
		/**
		 * @see Zend_Validate_Between
		 */
		require_once 'Zend/Validate/Between.php';
		$betweenValidate = new Zend_Validate_Between(1, 512);
		$result = $betweenValidate->isValid($imgSize);
		if (!$result) {
			/**
			 * @see Zend_View_Exception
			 */
			require_once 'Zend/View/Exception.php';
			throw new Zend_View_Exception(current($betweenValidate->getMessages()));
		}
		$this->_options['imgSize'] = $imgSize;
		return $this;
	}
	
	/**
	 * Gets default img
	 *
	 * @return string
	 */
	public function getDefaultImg()
	{
		return $this->_options['defaultImg'];
	}
	
	/**
	 * Sets default img
	 *
	 * @param string $defaultImg
	 * @link http://pl.gravatar.com/site/implement/url More information about default image.
	 * @return Zend_View_Helper_Gravatar
	 */
	public function setDefaultImg($defaultImg)
	{
		$this->_options['defaultImg'] = urlencode($defaultImg);
		return $this;
	}
	
	/**
	 * Sets rating value
	 *
	 * @param string $rating Value for rating. Allowed values are: g, px, r,x
	 * @link http://pl.gravatar.com/site/implement/url More information about rating.
	 * @throws Zend_View_Exception
	 */
	public function setRating($rating)
	{
		switch ($rating) {
			case self::GRAVATAR_RATING_G:
				$this->_options['rating'] = $rating;
				break;
			case self::GRAVATAR_RATING_PG:
				$this->_options['rating'] = $rating;
				break;
			case self::GRAVATAR_RATING_R:
				$this->_options['rating'] = $rating;
				break;
			case self::GRAVATAR_RATING_X:
				$this->_options['rating'] = $rating;
				break;
			default:
				/**
				 * @see Zend_View_Exception
				 */
				require_once 'Zend/View/Exception.php';
				throw new Zend_View_Exception('The value "' . $rating . '" isn\'t allowed.');
		}
	}
	
	/**
	 * Gets rating value
	 *
	 * @return string
	 */
	public function getRating()
	{
		return $this->_options['rating'];
	}
	
	/**
	 * Gets URL to gravatar's service.
	 *
	 * @param bool $flag Use HTTPS?
	 * @return string Url
	 */
	private function getGravatarUrl($flag)
	{
		return (bool) $flag === false ? self::GRAVATAR_URL : self::GRAVATAR_URL_SECURE;
	}

}
