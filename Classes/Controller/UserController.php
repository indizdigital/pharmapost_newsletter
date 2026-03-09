<?php
namespace Phi\PhiNewsletter\Controller;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

 use \TYPO3\CMS\Core\Utility\GeneralUtility;
 use \TYPO3\CMS\Core\Context\Context;
 use TYPO3\CMS\Extbase\Annotation\Inject;
/**
 *
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class UserController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * emailController
	 * to make this running this controller must be set in the ext_localconf.php!!
	 *
	 * @var \Phi\PhiNewsletter\Controller\EmailsController
	 * @TYPO3\CMS\Extbase\Annotation\Inject
	 */
	protected $emailController;
	 /**
	 * userRepository
	 *
	 * @var \Phi\PhiNewsletter\Domain\Repository\UserRepository
	 * @TYPO3\CMS\Extbase\Annotation\Inject
	 */
	protected $userRepository;
	 /**
	 * usergroupRepository
	 *
	 * @var \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserGroupRepository
	 * @TYPO3\CMS\Extbase\Annotation\Inject
	 */
	protected $usergroupRepository;

  /**
   * sessionService
   *
   * @var \Phi\PhiNewsletter\Service\Session
   * @TYPO3\CMS\Extbase\Annotation\Inject
   */
  protected $sessionService = null;

  /**
   * emailController
   *
   * @param \Phi\PhiNewsletter\Controller\EmailsController
   */
  public function injectEmailController(\Phi\PhiNewsletter\Controller\EmailsController $emailController){
    $this->emailController = $emailController;
  }

  /**
   * userRepository
   *
   * @param \Phi\PhiNewsletter\Domain\Repository\UserRepository
   */
  public function injectUserRepository(\Phi\PhiNewsletter\Domain\Repository\UserRepository $userRepository){
    $this->userRepository = $userRepository;
  }

  /**
   * usergroupRepository
   *
   * @param \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserGroupRepository
   */
  public function injectUsergroupRepository(\TYPO3\CMS\Extbase\Domain\Repository\FrontendUserGroupRepository $usergroupRepository){
    $this->usergroupRepository = $usergroupRepository;
  }

  /**
   * sessionService
   *
   * @param \Phi\PhiNewsletter\Service\Session
   */
  public function injectSessionService(\Phi\PhiNewsletter\Service\Session $sessionService){
    $this->sessionService = $sessionService;
  }


	protected $varNameSpace = "tx_phinewsletter_newsletter";

	/**
	 * initialize create action
	 */
	public function initializeAction() {

		parent::initializeAction();

		$this->emailController->setSender($this->settings);
	}

	/**
	 * getUser
	 *
	 * @return void
	 */
	public function getUser()
	{
		$userAspect = $this->getUserAspect();
		$user = $userAspect->getUser();
		return $user;
	}

	/**
	 * getContext
	 *
	 * @return void
	 */
	public function getContext()
	{
		$context = GeneralUtility::makeInstance(Context::class);
		return $context;
	}

	/**
	 * getFeUserId
	 *
	 * @return void
	 */
	public function getFeUserId()
	{
		$context = GeneralUtility::makeInstance(Context::class);
		$uid = $context->getPropertyFromAspect('frontend.user','uid');
		return $uid;
	}

	/**
	 * getCSRF
	 *
	 * @param string $formName
	 * @param string $action
	 * @param string $formInstanceName
	 * @return \string
	 */
	public function getCSRF($formName, $action = '', $formInstanceName = ''){
		return \TYPO3\CMS\Core\FormProtection\FormProtectionFactory::get()->generateToken($formName, $action, $formInstanceName);
	}

	/**
	 * validateCSRF
	 *
	 * @param string $formName
	 * @param string $action
	 * @param string $formInstanceName
	 * @return void
	 */
	public function validateCSRF($formName, $action = '', $formInstanceName = ''){
		$csrf = $this->getArgument("csrf","string","POST");
		return \TYPO3\CMS\Core\FormProtection\FormProtectionFactory::get()->validateToken($csrf,$formName, $action, $formInstanceName);
	}


	/**
	 * action new
	 *
	 * @param \Phi\PhiNewsletter\Domain\Model\User $newUser
	 * @return void
	 */
	public function newAction(\Phi\PhiNewsletter\Domain\Model\User $newUser = NULL) {

 		if($newUser){
			$this->view->assign('newUser', $newUser);
		}
    //todo: fix this: functions not callable
		if($this->getFeUserId() > 0){
			if(strpos($this->getFeUsergroup(),"14") !== false){
				$idf = str_replace("ch-","",$this->getFeUsername());
				$idf = preg_replace("/-[0-9]/","",$idf);

				$this->view->assign('idf', $idf);
			}
		}
    $hash = $this->initAntiSpam("newsletter_subscription");
    $this->view->assign('hash', $hash);
		$this->view->assign('csrf',$this->getCSRF("newsletter","create",$this->getFeUserId()));
	}

	/**
	 * initAntiSpam
	 *
	 * @param \string $ctrl
	 * @return void
	 */
	public function initAntiSpam($ctrl)
	{
			$hash = md5(time() . "ohU6K_2aG)9i@E}syY39qvV!A6Z,v");
			$this->sessionService->setToSession($hash,$ctrl . "-hash");
			$this->sessionService->setToSession(time(),$ctrl ."-time");
			return $hash;
	}

	/**
	 * action activate
	 *
	 * @return void
	 */
	public function activateAction() {

		$userid = $this->getArgument("user","int","GET");

		$user = $this->userRepository->findByUid($userid);
		if($_GET[$this->varNameSpace]["hash"] == $user["doubleopthash"]){
			$this->usergroupRepository->findByUid($this->settings["not_activated_uid"]);
			$groups = explode(",",$user['usergroup']);
			$newgroups = array($this->settings["newslettergroup_uid"]);
			foreach($groups as $gid){
				if(!in_array($gid,array($this->settings["newslettergroup_uid"],$this->settings["not_activated_uid"])) && strlen($gid)){
					$newgroups[] = $gid;
				}
			}
			/*$user->setDisable(0);
			$user->setDeleted(0);
			print_r($user);exit;*/
			$user["usergroup"] = implode(",",$newgroups);
			$user["doubleopthash"] = "";
			$user["deleted"] = 0;
			$user["disable"] = 0;
			$this->userRepository->updateByUid($userid,$user);
			$subject = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("tx_phinewsletter_domain_model_user.confirmActivationSubject",$this->extensionName);

			$this->emailController->sendAction("Confirmed.html",array($user["email"]),$subject,array('settings'=>$this->settings));
		}else{
			$this->view->assign("error","1");
		}

	}

	/**
	 * action create
	 *
	 * @return void
	 */
	public function createAction() {

		if($this->getFeUserId() == 0){
			//goto root TODO: only authenticated user can fill in the form (make it configurable)
			//$this->go(68);
		}
    $hash = $this->getArgument("hash","string","POST");
    $honeypot = $this->getArgument("www","string","POST");
    $usermail = $this->getArgument("email","email","POST");
    $csrf = $this->getArgument("csrf","string","POST");
		$csrfValidation = $this->validateCSRF("newsletter","create",$this->getFeUserId());

    if($this->isSpam("newsletter_subscription",$hash,$honeypot,$usermail) || !$csrfValidation){
			error_log("Newsletter Subscription: isSpam or CSRF not valid");
      $this->view->assign("sent",0);
    }else{

			$email = $this->getArgument("email","email","POST");
			$firstname = $this->getArgument("firstname","string","POST");
			$lastname = $this->getArgument("lastname","string","POST");
			$company = $this->getArgument("company","string","POST");
			$gender = $this->getArgument("gender","string","POST","",array("m","w"));
			if(isset($this->settings["additionalFields"]) && is_array($this->settings["additionalFields"])){

				foreach($this->settings["additionalFields"] as $fieldname){
					$additionals[$fieldname] = $this->getArgument($fieldname,"string","POST");
				}
			}
			$username = $email;
			//todo csrf check!!
			$userResult = $this->userRepository->findByUsername($username);
			$usergroup = $this->settings["newslettergroup_uid"];
			if($this->settings["useDoubleOptIn"] == "1"){
				$usergroup = $this->settings["not_activated_uid"];
			}

			$hash = md5(uniqid("phi"));
	  	$userid = 0;

      $languageAspect = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Context\Context::class)->getAspect('language');
      $sys_language_uid = $languageAspect->getId();
      $idf = $this->idfService->getCurrentIdf();
      if($idf){
		    $this->accountRepository = GeneralUtility::makeInstance(\Indiz\IndizAmedisbenutzerkonto\Domain\Repository\AccountRepository::class);
        $account = $this->accountRepository->findByIdfRaw($idf);
        $sys_language_uid = $account["language"];
      }

			if($userResult->count() && $userResult->getFirst()){

				$newUser = $userResult->getFirst();

				$newUser->addUsergroup($this->usergroupRepository->findByUid($usergroup));

				//deprecated. new field: doubleopthash
				$newUser->setEmail($email);
				$newUser->setFirstName($firstname);
				$newUser->setLastName($lastname);
				$newUser->setCompany($company);
				$newUser->setGender($gender);
				$newUser->setDoubleopthash($hash);
				$newUser->setLanguage($sys_language_uid);
				$this->userRepository->update($newUser);
				$persistenceManager = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager');
				$persistenceManager->persistAll();
				$userid = $newUser->getUid();

			}else{
	    	$newUser = $this->objectManager->get('Phi\PhiNewsletter\Domain\Model\User');
				$newUser->setUsername($username);

				$newUser->setEmail($email);
				$newUser->setFirstName($firstname);
				$newUser->setLastName($lastname);
				$newUser->setGender($gender);
				$newUser->setCompany($company);
				$newUser->setDoubleopthash($hash);
				$newUser->setLanguage($sys_language_uid);
				$newUser->setCustomuserid($idf);
        //set pwd to prevent typo3 error
        $pwd = '$argon2i$v=19$m=65536,t=16,p=1$VUVTbkNiWEtxUUIxazBOZA$71i9WygK4TUI7AF09aFFAxqPz8QBtRoluaZiU4t+Ceg';
				$newUser->setPassword($pwd);

				$newUser->addUsergroup($this->usergroupRepository->findByUid($usergroup));
				$this->userRepository->add($newUser);

				$persistenceManager = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager');
				$persistenceManager->persistAll();
				$userid = $newUser->getUid();

			}

			if($this->settings["useDoubleOptIn"] == "1"){
				$subject = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("tx_phinewsletter_domain_model_user.confirmSubject",$this->extensionName);
				$activate_link = $this->settings["base"] . "index.php?id=".$this->settings["statusPage"].'&L='. $sys_language_uid . '&tx_phinewsletter_newsletter[action]=activate&tx_phinewsletter_newsletter[controller]=User&tx_phinewsletter_newsletter[user]='.$newUser->getUid().'&tx_phinewsletter_newsletter[hash]='.$newUser->getDoubleopthash();

        $variables = array('settings'=>$this->settings["base"],'activate_link'=>$activate_link,"user"=>$newUser);
				$this->emailController->sendAction("Confirm.html",array($email),$subject,$variables);
			}else{
				$this->redirect("activate",NULL,NULL,array("user"=>$userid,"hash"=>$hash));
      }

			$this->view->assign("sent",1);

      if(isset($this->settings["statusPage"])){
			   $this->redirect("status",NULL,NULL,array("user"=>$userid,"process"=>"created"),$this->settings["statusPage"]);
      }
		}



	}

	/**
	 * action status
	 *
     * @param \string $process
	 * @return void
	 */
	public function statusAction($process) {
        $this->view->assign("process",$process);
        $args = $this->request->getArguments();
        $group = $args['group'];
        $user = $args['user'];

        $this->userRepository->removeFromGroup($user,$group,$hash);
        $g = $this->usergroupRepository->findByUid($group);

        //send email for archive purposes. hardcoded elements

        $this->emailController->settings["emailsender"] = "contact@pharmapost.swiss";
        $this->emailController->settings["emailsendername"] = "Pharmapost AG";
        $this->emailController->config['configuration']["templatePath"] = "EXT:phi_newsletter/Resources/Private/Templates/Email";
        $userobject = $this->userRepository->findByUid($user);
        $this->emailController->stdFluidMarkers["user"] = $userobject;
        $this->emailController->stdFluidMarkers["group"] = $g;
		$this->emailController->sendFluidTemplate("info@pharmapost.swiss","","","Newsletter Abmeldung","Unsubscribe");

        $this->view->assign("group",$g);

	}

    /**
     * getArgument
     *
     * @param string $argument
     * @param string $type
     * @return void
     */
    public function getArgument($argument,$datatype, $type = 'POST',$default = '',$assertValues = array()){
      $value = $default;
      if($type == 'POST'){
        if(isset($_POST[$this->varNameSpace]) && isset($_POST[$this->varNameSpace][$argument])){
          $value = $_POST[$this->varNameSpace][$argument];
        }
      }else{
        if(isset($_GET[$this->varNameSpace]) && isset($_GET[$this->varNameSpace][$argument])){
          $value = $_GET[$this->varNameSpace][$argument];
        }
      }
      switch($datatype){
        case 'int':
          $value = intval($value);
          break;
        case 'string':
          if((!empty($assertValues) && in_array($value,$assertValues)) || empty($assertValues)){
          }elseif(strlen($value) == 0){
            $value = $this->notAsserted;
          }
          break;
        case 'email':
          if(strlen($value) && !filter_var($value,FILTER_VALIDATE_EMAIL)){
            $value = $this->notAsserted;
          }
          break;
        case 'bool':
          if(strlen($value) && $value != '0'){
            $value = true;
          }else{
            $value = false;
          }
          break;
        case 'intarray':
          if($value == ""){
            return [];
          }if(!is_array($value)){
            $value = $this->notAsserted;
          }else{
            foreach($value as &$v){
              if(!in_array($v,$assertValues)){
                $v = intval($v);
              }
            }
          }
          break;
        default:
          $value = $this->notAsserted;
      }

      return $value;
    }

    /**
     * action isSpam
     *
     * @param \string $ctrl
     * @param \string $hash
     * @param \string $honeypot
     * @param \string $usermail
     * @return void
     */
    public function isSpam($ctrl,$hash,$honeypot,$usermail)
    {
        $sessionHash = $this->sessionService->getFromSession($ctrl . "-hash");
        $time = $this->sessionService->getFromSession($ctrl ."-time");
        $this->sessionService->setToSession(0,$ctrl ."-time");
        $this->sessionService->setToSession("---",$ctrl ."-hash");
        $isspam = false;
        if($time == 0 || (time() - $time) < $this->settings["spamTrapTime"]){
          $isspam = true;
          //mail("philipp@indiz.digital","iamedis spam","time trap");
          error_log("IP:" . $_SERVER["REMOTE_ADDR"] . " " . $usermail . " is trying to send Spam (TimeTrap)",0);
        }
        if($hash != $sessionHash){
          $isspam = true;
          error_log("IP:" . $_SERVER["REMOTE_ADDR"] . " " . $usermail . " is trying to send Spam (HashFailed)",0);
        }
        if(strlen($honeypot)){
          $isspam = true;
          error_log("IP:" . $_SERVER["REMOTE_ADDR"] . " " . $usermail . " is trying to send Spam (HoneyPot)",0);
        }

        return $isspam;
    }


}
?>
