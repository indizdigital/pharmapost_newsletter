<?php
namespace Phi\PhiNewsletter\Service;

/***
 *
 * This file is part of the "Phi HCI Information" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2018
 *
 ***/

/**
 * Session
 */
class Session implements \TYPO3\CMS\Core\SingletonInterface
{
	/**
	 * prefix
	 *
	 * @var string
	 */
	protected $prefix = 'phi_misc_amedisch';
	/**
    * Returns the object stored in the user´s PHP session
    * @return Object the stored object
    */
    public function getFromSession($key) {
    	$sessionData = '';
    	if(TYPO3_MODE == 'FE'){
  			$sessionData = $GLOBALS['TSFE']->fe_user->getKey('ses', $this->prefix . $key);
  			$sessionData = unserialize($sessionData);
  		}else{
  			$data = ($GLOBALS['BE_USER']->getSessionData($this->prefix));

  			$sessionData = $data[$key];
  		}
      return $sessionData;
    }

    /**
    * Writes an object into the PHP session
    * @param    $object any serializable object to store into the session
    * @return Session
    */
    public function setToSession($object, $key) {
    	if(TYPO3_MODE == 'FE'){
     		$sessionData = serialize($object);
      	$GLOBALS['TSFE']->fe_user->setKey('ses', $this->prefix . $key, $sessionData);
      	$GLOBALS['TSFE']->fe_user->storeSessionData();
      }else{
  			$data = ($GLOBALS['BE_USER']->getSessionData($this->prefix));

  			$data[$key] = $object;
  			$GLOBALS['BE_USER']->setAndSaveSessionData($this->prefix, $data);
  		}
      return $this;
    }

    /**
    * Empty session data
    * @return Session
    */
    public function emptySession($key) {
    	if(TYPO3_MODE == 'FE'){
        	$GLOBALS['TSFE']->fe_user->setKey('ses', $this->prefix . $key, NULL);
        	$GLOBALS['TSFE']->fe_user->storeSessionData();
        }else{
  			$GLOBALS['BE_USER']->setAndSaveSessionData($this->prefix, "");
  		}
    	return $this;
    }

    /**
    * debug session data
    * @return Session
    */
    public function debugSessionData($key = "") {
		echo "DEBUG SESSION DATA<br>";
		if(strlen($key)){
    		print_r($GLOBALS['TSFE']->fe_user->getKey('ses', $this->prefix . $key));
		}else{
    		print_r($GLOBALS['TSFE']->fe_user);
		}
    }
}
