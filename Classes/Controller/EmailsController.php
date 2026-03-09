<?php
namespace Phi\PhiNewsletter\Controller;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2015
 *
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
use \TYPO3\CMS\Core\Core\Environment;
use \Symfony\Component\Mime\Address;
use \TYPO3\CMS\Core\Mail\FluidEmail;
use \TYPO3\CMS\Core\Mail\Mailer;
use \TYPO3\CMS\Core\Utility\GeneralUtility;
/**
 * EmailsController
 */
class EmailsController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    public $stdFluidMarkers = [];
    /**
     * emailsRepository
     *
     * @var \Phi\PhiNewsletter\Domain\Repository\EmailsRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $emailsRepository = NULL;

      /**
       * $additionalService
       *
       * @var \Phi\PhiNewsletter\Service\Additionals
       * @TYPO3\CMS\Extbase\Annotation\Inject
       */
      protected $additionalService = NULL;

    /**
     * configRepository
     *
     * @var \Phi\PhiNewsletter\Domain\Repository\ConfigRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $configRepository = NULL;

  	/**
  	 * configRepository
  	 *
  	 * @param \Phi\PhiNewsletter\Domain\Repository\ConfigRepository $configRepository
  	 */
  	public function injectConfigRepository(\Phi\PhiNewsletter\Domain\Repository\ConfigRepository $configRepository){
  		$this->configRepository = $configRepository;
  	}

    /**
     * additionalService
     *
     * @param \Phi\PhiNewsletter\Service\Additionals $additionalService
     */
    public function injectAdditionalService(\Phi\PhiNewsletter\Service\Additionals $additionalService){
      $this->additionalService = $additionalService;
    }

    /**
     * emailsRepository
     *
     * @param \Phi\PhiNewsletter\Domain\Repository\EmailsRepository $emailsRepository
     */
    public function injectEmailsRepository(\Phi\PhiNewsletter\Domain\Repository\EmailsRepository $emailsRepository){
      $this->emailsRepository = $emailsRepository;
    }

    protected $lang_keys = array("0"=>"default","1"=>"fr","2"=>"it");

    protected $hash_base = ":`OknkA.tfTXq4[P'Wb>u/@##^Hz}r";

	/*
	*	sendername
	*/
	public $sendername = 'NAME';

	/*
	*	senderemail
	*/
	public $senderemail = 'test@mail.ch';

	/**
	 * initialize create action
	 * allow creation of submodel company
	 */
	public function setSender($settings) {
		if(isset($settings['emailsendername'])){
			$this->sendername = $settings['emailsendername'];
		}
		if(isset($settings['emailsender'])){
			$this->senderemail = $settings['emailsender'];
		}
	}

	/**
	 * action send
	 *
	 * @param \string $template
	 * @param \array $recipients
	 * @param \string $subject
	 * @param \array $variables
	 * @return void
	 */
	public function sendAction($template,$recipients,$subject,$variables) {

		$emailView = $this->objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');

		$emailView->getRequest()->setControllerExtensionName($this->extensionName);

		$extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
		$templateEmailPath = array_pop($extbaseFrameworkConfiguration["view"]["templateRootPaths"])."Email/";//\TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName('EXT:phi_newsletter/Resources/Private/Templates/Email/');

		$templatePathAndFilename = $templateEmailPath.$template;
    if(is_array($extbaseFrameworkConfiguration["view"]["layoutRootPaths"])){
		    $emailView->setLayoutRootPaths($extbaseFrameworkConfiguration["view"]["layoutRootPaths"]);
    }

		$emailView->setTemplatePathAndFilename($templatePathAndFilename);
		$emailView->assignMultiple($variables);
		$emailBody = $emailView->render();
		$message = $this->objectManager->get('TYPO3\\CMS\\Core\\Mail\\MailMessage');

		$sender = array($this->senderemail=>$this->sendername);

		$message->setTo($recipients)->setFrom($sender)->setSubject($subject);

		// Possible attachments here
		//foreach ($attachments as $attachment) {
		//    $message->attach($attachment);
		//}

		// Plain text example
		//$message->setBody($emailBody, 'text/plain');

		// HTML Email

		$message->html($emailBody);

		$message->send();

	}
	/**
	 * func sendTemplate
	 *
	 * @param \array $recipient
	 * @param \string $sendermail
	 * @param \string $sendername
	 * @param \string $subject
	 * @param \string $template
	 * @return void
	 */
	public function sendTemplate($recipient,$sendermail,$sendername,$subject,$template) {




		$emailView = $this->objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');

		$emailView->getRequest()->setControllerExtensionName($this->extensionName);

		/*$extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
		$templateEmailPath = array_pop($extbaseFrameworkConfiguration["view"]["templateRootPaths"])."Email/";//\TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName('EXT:phi_newsletter/Resources/Private/Templates/Email/');

		$templatePathAndFilename = $templateEmailPath.$template;

		$emailView->setTemplatePathAndFilename($templatePathAndFilename);
		$emailView->assignMultiple($variables);
		$emailBody = $emailView->render();*/

    /*$GLOBALS['TYPO3_CONF_VARS']['MAIL']['transport'] = 'smtp';
    foreach($this->settings["smtp"] as $key=>$val){
      $GLOBALS['TYPO3_CONF_VARS']['MAIL'][$key] = $val;
    }*/

    $emailBody = $template;

    $message = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Mail\\MailMessage');

		$sender = array($this->senderemail=>$this->sendername);

		$message->setTo($recipient)->setFrom($this->settings["emailsender"],$this->settings["emailsendername"])->setSubject($subject);

		// Possible attachments here
		//foreach ($attachments as $attachment) {
		//    $message->attach($attachment);
		//}

		// Plain text example
		//$message->setBody($emailBody, 'text/plain');

		// HTML Email
		$message->html($emailBody);

		$message->send();
		return $message->isSent();


	}
	/**
	 * func sendFluidTemplate
	 *
	 * @param \array $recipient
	 * @param \string $sendermail
	 * @param \string $sendername
	 * @param \string $subject
	 * @param \string $template
	 * @return void
	 */
	public function sendFluidTemplate($recipient,$sendermail,$sendername,$subject,$template = "main") {

        //print_r($this->config['configuration']["templatePath"]);exit;

        $GLOBALS['TYPO3_CONF_VARS']['MAIL']['templateRootPaths'][700] = $this->config['configuration']["templatePath"];
        $GLOBALS['TYPO3_CONF_VARS']['MAIL']['partialRootPaths'][700] = $this->config['configuration']["templatePath"];
        //$GLOBALS['TYPO3_CONF_VARS']['MAIL']['layoutRootPaths'][700] = 'fileadmin/templates/ext/phi_newsletter/general/';

        $email = GeneralUtility::makeInstance(FluidEmail::class);
        $email
            ->to($recipient)
            ->from(new Address($this->settings["emailsender"],$this->settings["emailsendername"]))
            ->subject($subject)
            ->format(FluidEmail::FORMAT_HTML) // send HTML and plaintext mail
            ->setTemplate($template)
            ->assignMultiple($this->stdFluidMarkers);
        GeneralUtility::makeInstance(Mailer::class)->send($email);

        return true;
	}

    /**
     * action list
     *
     * @return void
     */
    public function selectitemsAction()
    {
        if(!isset($_GET['tx_phinewsletter_web_phinewsletterphinewsletter']["config"])){
          $this->redirect('list', 'Config');
        }
        $config = $this->configRepository->findByUid($_GET['tx_phinewsletter_web_phinewsletterphinewsletter']["config"]);
        $contents = $this->emailsRepository->loadContents($config);

        $this->view->assign('contents', $contents);

        if(count($contents) == 0){
            $this->redirect("selectgroups",NULL,NULL,["config"=>$config["uid"]]);
        }

        $this->additionalService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Phi\PhiNewsletter\Service\Additionals');
        $configArray = [];
        $this->additionalService->parseConfig($configArray,$config['configuration']);

        if(isset($configArray['configuration']["fixedHeadItemCategory"])){
          $this->view->assign('fixedHeadItemCategory', $configArray['configuration']["fixedHeadItemCategory"]);
        }

        $this->view->assign('config', $config);
        $this->view->assign("processLinks",["abort"=>1]);
        $this->addFlashMessage('Please select at least one Content!', 'No Newsletter sent', \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR);

    }

      /**
       * action list
       *
       * @return void
       */
      public function selectgroupsAction()
      {

          $contents = isset($_POST["tx_phinewsletter_web_phinewsletterphinewsletter"]["contents"])?
            $_POST["tx_phinewsletter_web_phinewsletterphinewsletter"]["contents"]:
            [];
          $configUid = $_POST['tx_phinewsletter_web_phinewsletterphinewsletter']["config"];
          if(!$configUid){
              $configUid = $_GET['tx_phinewsletter_web_phinewsletterphinewsletter']["config"];
          }

          $config = $this->configRepository->findByUid($configUid);
          $groups = $this->emailsRepository->loadUserGroup($config,$contents);
          $this->view->assign('groups', $groups);
          $this->view->assign('config',$configUid);
          $this->view->assign('contents', json_encode($contents));
          $this->view->assign("processLinks",["abort"=>1,"proceed"=>"1"]);
      }

    /**
     * action sentlist
     *
     * @return void
     */
    public function sentlistAction()
    {
        $emails = $this->emailsRepository->findAll();
        $this->view->assign('emails', $emails);
    }

    /**
     * action show
     *
     * @return void
     */
    public function showAction()
    {
        $args = $this->request->getArguments();
        $config = $args['config'];

        $emails = $this->emailsRepository->findByConfig($config);

        $this->view->assign('emails', $emails);
        $this->view->assign('edition', $config);
    }

    /**
     * listAction
     *
     * @return void
     */
    public function listAction()
    {
        $args = $this->request->getArguments();
        if (empty($args['groups'])){// || empty($args['contents']) || array_sum(array_values($args['contents'])) == 0) {
            $this->addFlashMessage('Please select at least one Group and one Content!', 'No Newsletter sent', \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR);
        } else {
            $confUid = $args["config"];


            $contents = json_decode($args['contents'],true);
            asort($contents);
            $sortedNewsUids = array();
            foreach ($contents as $uid => $sort) {
                if (strlen($sort) && $sort > 0) {
                    $sortedNewsUids[] = $uid;
                }
            }

            $origContents = $this->emailsRepository->findDefaultUids($sortedNewsUids);

            $newsid = implode(',', $origContents);
            
            $senttime = 0;
            if(strlen($config["tosendtimeNumeric"])){
              $senttime = $config["tosendtimeNumeric"];
            }
            if(!isset($args["sendAsDraft"])){
              $currentC = $this->configRepository->updateIssent($confUid,1);
            }

            $this->emailsRepository->addEmails($args['groups'], $newsid, $confUid,$senttime);
            $this->addFlashMessage('Newsletters werden in den nächsten Minuten verschickt > Status unter \'Verschickte Newsletter\' (oben im Dropdown)');

            $emails = $this->emailsRepository->findByConfig($confUid);

            $this->view->assign('emails', $emails);
        }
    }

    /**
     * action create
     *
     * @param \Phi\PhiNewsletter\Domain\Model\Emails $newEmails
     * @return void
     */
    public function createAction(\Phi\PhiNewsletter\Domain\Model\Emails $newEmails)
    {
        $this->emailsRepository->add($newEmails);
        $this->redirect('list');
    }

    /**
     * action archived
     *
     * @return void
     */
    public function archivedAction()
    {
		 $archivelist = $this->emailsRepository->findAllNewsletters($this->settings);
        $this->view->assign('archivelist', $archivelist);
        $this->view->assign('languageKey',$GLOBALS['TSFE']->sys_language_uid);// $GLOBALS['TSFE']->config['config']['language']);
    }

    /**
     * action sendallAction
     *
     * @return void
     */
    public function sendallAction()
    {
          $taskid = "phi".mt_rand();
          $res = $this->emailsRepository->loadEmailsToSend($taskid);

          $this->additionalService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Phi\PhiNewsletter\Service\Additionals');

            while ($row = $res->fetch()) {

                //$this->config = unserialize(($row['conf']));
              $conf_row = $this->configRepository->findByUid($row['config']);

              if(!empty($conf_row)){
                $this->config = $conf_row;
                $this->config['url'] = $this->removeLastSlash($this->config['url']);
                $this->config['url'] = $this->addProtocol($this->config['url'],"https");


                $this->additionalService->parseConfig($this->config,$this->config['configuration']);

              }
              $this->replaceFileLinks($row['language']);

              if(isset($this->config['configuration']["languageFile"])){
                    $this->getLL($this->config['configuration']["languageFile"]);
              }

              if(isset($this->config['configuration']["templatePath"])){
                    $this->config['configuration']["templatePath"] = $this->removeLastSlash($this->config['configuration']["templatePath"]);
                $this->itemFile = $basePath . $this->config['configuration']["templatePath"].'/item.html';
                $this->itemFile2 = $basePath . $this->config['configuration']["templatePath"].'/item2.html';
                $this->lastItemOdd = "";
                if(is_file($basePath . $this->config['configuration']["templatePath"].'/item-last-odd.html')){
                  $this->lastItemOdd = $basePath . $this->config['configuration']["templatePath"].'/item-last-odd.html';
                }
                $this->subheader = $basePath . $this->config['configuration']["templatePath"].'/subheader.html';
                $this->mainFile = $basePath . $this->config['configuration']["templatePath"].'/main.html';
                $this->spacerFile = $basePath . $this->config['configuration']["templatePath"].'/spacer.html';
              }


            //language all fix:
            if($row['language'] == "-1"){
              $row['language'] = 0;
            }
            //load stdMarkers
            $this->initStdMarkers($row,$row['language']);

            $item = file_get_contents($this->itemFile);
            $item2 = file_get_contents($this->itemFile2);
            if(strlen($this->lastItemOdd)){
              $lastItemOdd = file_get_contents($this->lastItemOdd);
            }
            $subheadertemplate = file_get_contents($this->subheader);
            $readmore = array(
                $this->lang["default"]["readmore"],
                $this->lang["fr"]["readmore"],
                $this->lang["it"]["readmore"]
            );
            $newsletters = $this->emailsRepository->getItems($this->config,$row['newsids'],$row["userid"],$readmore,$this->uriBuilder,$this->stdMarkers,$this->settings["statsStoragePid"]);
 
            //$personalize = strlen($row['title']) > 0?$row['title']." ".$row['last_name']:"";
            $personalize = '';
            if (strlen($row['gender']) && strlen($row['last_name'])) {
                $personalize = sprintf($this->getLocal("hello_" . $row["gender"],$row['language']),$row['last_name']);
            } else{
                $personalize = sprintf($this->getLocal("hello",$row['language']),$row['first_name'],$row['last_name']);
            }
            $subject = strip_tags($this->renderSub($row['language']));
            $this->renderHeadImage($row['language']);


            $spacertemplate = file_get_contents($this->spacerFile);
            $contents = $this->emailsRepository->generateEmailContents($newsletters,explode(",", $row['newsids']),$this->config["configuration"],$item,$item2,$subheadertemplate,$spacertemplate,$this->stdMarkers,$lastItemOdd);
             
            $content = $this->wrapInTemplate($contents[$row['language']]["content"], $row['language'], ($personalize), $row['email'],$row['user'],$row['groupid']);
            
           // die();
            $s = array('</td>', '</tr>');
            $r = array('\t', '\n\r');


              if(strpos($this->config["configuration"]["templatePath"],"general") !== false){
                  $this->initFluidMarkers($row,$row['language'],$row['user'],$row['groupid']);
                  $fluidnewsletters = $this->emailsRepository->getFluidItems($this->config,$row['newsids'],$row["userid"],$readmore,$this->uriBuilder,$this->stdMarkers,$this->settings["statsStoragePid"]);

                  $fluidContent = $this->emailsRepository->generateEmailContentMarkers($fluidnewsletters,explode(",", $row['newsids']),$this->config["configuration"],$item,$item2,$subheadertemplate,$spacertemplate,$this->stdMarkers,$lastItemOdd);
                  $this->stdFluidMarkers["items"] = $fluidContent[$row['language']];
                  

                  if (strlen($row['email']) && $this->sendFluidTemplate($row['email'],$this->config["emailfrom"],$this->config["namefrom"],$subject, "Main")) {
                    $this->emailsRepository->updateSenttime($row['uid']);

                  }
              }else{ 
                    if (strlen($row['email']) && $this->sendTemplate(array($row['email']),$this->config["emailfrom"],$this->config["namefrom"],$subject, $content)) {
                      $this->emailsRepository->updateSenttime($row['uid']);

                    }else{
                      $this->emailsRepository->updateSenttime($row['uid'],1);
                    }
              }
          }

    }
    public function replaceFileLinks($language){
      $prefixTexts = ["prefix0","prefix1","prefix2"];
      foreach($prefixTexts as $prefi){
          $prefiText = $this->config[$prefi];
          preg_match_all('/<a href="(.*?)">/',$this->config[$prefi],$matches);
          $replace = [];
          if(count($matches[0])){

            for($i = 0;$i < count($matches[0]);$i++){
              $wrappedURL = $this->config['url'] . '/?newsletter_tracker[crc]='.hash('sha1',$this->hash_base).'&amp;newsletter_tracker[language]=' . $language . '&amp;no_cache=1&amp;newsletter_tracker[config]='.$this->config['uid'].'&amp;newsletter_tracker[pid]='.$this->settings["clickratePid"].'&amp;newsletter_tracker[filelink]='.urlencode($matches[1][$i]);
              $newATag = sprintf('<a href="%s">',$wrappedURL);
              $prefiText = str_replace($matches[0][$i],$newATag,$prefiText);
            }
            $this->config[$prefi] = $prefiText;
          }
      }
    }

  	/**
  	* removeLastSlash if exists
  	*
  	* @param string $link_url
  	* @return string
  	*/
  	function removeLastSlash($link_url){
  		return (strrpos($link_url,"/") == strlen($link_url) - 1)?substr($link_url, 0, strlen($link_url) - 1):$link_url;
  	}

  	/**
  	* addProtocol to url
  	*
  	* @param string $url
  	* @param string $protocol
  	* @return void
  	*/
  	function addProtocol($url,$protocol){
  		return (strpos($url,"://") === false?$protocol."://" . $url:$url);
  	}
  	/**
  	* initStdMarkers
  	*
  	* @param array $row
  	* @param string $lang
  	* @return void
  	*/
      function initStdMarkers($row,$lang = 0) {

    		foreach($this->lang[$this->lang_keys[$lang]] as $key=>$val){
    			$this->stdMarkers["###" . strtoupper($key)."###"] = utf8_decode($val);
    		}
    		foreach($this->config as $key=>$val){
          if(!is_array($val)){
    			     $this->stdMarkers["###" . strtoupper($key)."###"] = utf8_decode($val);
     			     $this->stdMarkers["###CONFIG_" . strtoupper($key)."###"] = utf8_decode($val);
          }
    		}
    		foreach($row as $key=>$val){
    			$this->stdMarkers["###" . strtoupper($key)."###"] = utf8_decode($val);
    		}



            if(isset($this->config['configuration']["fixedHeadItemCategory"])){
              $headNews = $this->emailsRepository->getNewsContents($this->config['configuration']["fixedHeadItemCategory"],$this->config['configuration']["databaseTable"],$lang);

              $headNew = array_shift($headNews);
              if(!empty($headNew)){

                  $this->stdMarkers["###NEWS_HEAD_TITLE###"] = $headNew["title"];
                  $this->stdMarkers["###NEWS_HEAD_TEASER###"] = nl2br($headNew["teaser"]);
                  $this->stdMarkers["###NEWS_HEAD_BODY###"] = $headNew["bodytext"];
                  $headimage = ($this->emailsRepository->getSysImage($this->config,$headNew["uid"],"image",$this->config["configuration"]["databaseTable"],1200));
                  $this->stdMarkers["###NEWS_HEAD_IMAGE_SRC###"] = $headimage["###IMAGE_SRC###"];
                  $this->stdMarkers["###NEWS_HEAD_IMAGE_NAME###"] = $headimage["###IMAGE_NAME###"];
              }
            }
      }
    	/**
    	* initFluidMarkers
    	*
    	* @param array $row
    	* @param string $lang
    	* @return void
    	*/
        function initFluidMarkers($row,$lang,$user,$group) {
            //language markers
      		foreach($this->lang[$this->lang_keys[$lang]] as $key=>$val){
      			$this->stdFluidMarkers[strtoupper($key)] = utf8_decode($val);
      		}
            //config values
      		foreach($this->config as $key=>$val){
                if(!is_array($val)){
          			     $this->stdFluidMarkers[strtoupper($key)] = utf8_decode($val);
           			     $this->stdFluidMarkers["CONFIG_" . strtoupper($key)] = utf8_decode($val);
                }
      		}

            //news markers
      		foreach($row as $key=>$val){
      			$this->stdFluidMarkers[strtoupper($key)] = utf8_decode($val);
      		}

            //head news markers

          if(isset($this->config['configuration']["fixedHeadItemCategory"])){
            $headNews = $this->emailsRepository->getNewsContents($this->config['configuration']["fixedHeadItemCategory"],$this->config['configuration']["databaseTable"],$lang);

            $headNew = array_shift($headNews);
            if(!empty($headNew)){

                $this->stdFluidMarkers["NEWS_HEAD_TITLE"] = $headNew["title"];
                $this->stdFluidMarkers["NEWS_HEAD_TEASER"] = nl2br($headNew["teaser"]);
                $this->stdFluidMarkers["NEWS_HEAD_BODY"] = $headNew["bodytext"];
                $headimage = ($this->emailsRepository->getSysImage($this->config,$headNew["uid"],"image",$this->config["configuration"]["databaseTable"],1200));
                $this->stdFluidMarkers["NEWS_HEAD_IMAGE_SRC"] = $headimage["###IMAGE_SRC###"];
                $this->stdFluidMarkers["NEWS_HEAD_IMAGE_NAME"] = $headimage["###IMAGE_NAME###"];
            }
          }

          //configuration values from config
          foreach($this->config["configuration"] as $key=>$title){
            if(strpos($key,"categoryname") !== false){
              $s = explode(".",$key);
              if($s[2] == $lang){
                  $this->stdFluidMarkers["subheader"][$s[1]] = $title;
              }
            }
          }

          //additional values
          $this->stdFluidMarkers["SUBHEADER"] = $this->config["configuration"]["subheader"];
            $this->stdFluidMarkers["URL"] = $this->config['url'];
            $this->stdFluidMarkers["MONTH"] = date("m");
            $this->stdFluidMarkers["YEAR"] = date("y");
            $this->stdFluidMarkers["TEMPLATE"] = $this->config["configuration"]["templatePath"];

            $this->stdFluidMarkers["KEY_LANG"] = $lang;
            $pref = "";

          $pref = $this->config['prefix' . $lang];

            $timestamp = time();

            //encoding error from the mailserver because of encoding issues
            $pref = ($pref);


          $this->stdFluidMarkers["HEAD"] = str_replace("BRTAG", "<br>", $pref);
          $this->stdFluidMarkers["EMPTY_HEAD"] = strlen($pref) == 0?'display:none;':'';
          $this->stdFluidMarkers["URL_LANG"] = $this->lang_keys[$lang];

          $this->stdFluidMarkers["LANG_UID"] = $lang;


          $edition = $this->stdMarkers["EDITION"];

          //general markers with empty strings for the webview
          $this->stdFluidMarkers["ANREDE"] = $this->getLocal("hello_noname", $lang);
            $this->stdFluidMarkers["USER_EMAIL"] = "";
          $this->stdFluidMarkers["VIEW_HTML"] = "";
          $this->stdFluidMarkers["VIEW_HTML_LINK"] = "";
          $this->stdFluidMarkers["UNSUBSCRIBE"] = "#";
        $this->stdFluidMarkers["PID"] = $this->settings["statsStoragePid"];

            $file_html = str_replace(array_keys($this->stdFluidMarkers), array_values($this->stdFluidMarkers), $html);

              $this->saveToFile($file_html,$lang,$mailTo,$edition);

          $viewHTMLLink = $this->config['url'] . '/?newsletter_tracker[crc]'.hash('sha1',$this->hash_base).'&amp;newsletter_tracker[language]=' . $row['sys_language_uid'] . '&amp;no_cache=1&newsletter_tracker[newsid]=0&amp;newsletter_tracker[user]=0&amp;newsletter_tracker[edition]='.$edition.'&amp;newsletter_tracker[config]='.$this->config['uid'].'&amp;newsletter_tracker[newspage]=' . urlencode($this->savedToFile[$lang]);
          $html_link = '<a href="'.$viewHTMLLink.'" target="_blank" style="color:#706a60;font-size:14px;">'.$this->getLocal("viewHtml",$lang).'</a>';

          //personalized markers with empty strings for the webview
              $this->stdFluidMarkers["ANREDE"] = $hello;
            $this->stdFluidMarkers["USER_EMAIL"] = $mailTo;
          $this->stdFluidMarkers["VIEW_HTML"] = $html_link;

          $this->stdFluidMarkers["VIEW_HTML_LINK"] = $viewHTMLLink;


          $uri = $this->uriBuilder->reset()->setCreateAbsoluteUri(TRUE)->setTargetPageUid($this->config["statuspageid"]);
          $unsubscribeUrl = $uri->build();
          $this->stdFluidMarkers["UNSUBSCRIBE"] = $unsubscribeUrl . "/?tx_phinewsletter_newsletterstatus[user]=".$user . "&tx_phinewsletter_newsletterstatus[group]=".$group. "&tx_phinewsletter_newsletterstatus[process]=unsubscribe";



          $image = $this->emailsRepository->getFluidImage($this->config,$this->config['uid'],'image'.$lang,"tx_phinewsletter_domain_model_config");
          $this->stdFluidMarkers["HEAD_IMG_SRC"] = (isset($image["IMAGE_SRC"])?$image["IMAGE_SRC"]:"");
          $this->stdFluidMarkers["HEAD_IMG_NAME"] = $image["IMAGE_NAME"];

        }

  	/**
  	* getLocal
  	*
  	* @param string $key
  	* @param string $lang
  	* @return string
  	*/
      function getLocal($key, $lang) {
          $str = isset($this->lang[$this->lang_keys[$lang]][$key]) ? $this->lang[$this->lang_keys[$lang]][$key] : $this->lang["default"][$key];
          return utf8_decode($str);
      }
    	/**
    	* getLL
    	*
    	* @param string $path
    	* @return void
    	*/
        function getLL($path = "") {

            $url = getcwd() . '/' . $path;
            if (is_file($url)) {
                include($url);
                $this->lang = $LOCAL_LANG;
            }
        }

      	/**
      	* renderSub render the Subject
      	*
        * @param \int $lang
      	* @return string
      	*/
          function renderSub($lang = 0) {
              $s = array('{datum}');
              $r = array(date("d.m.Y", time()));
              $sub = str_replace($s, $r, ($this->config['subject'.$lang]));
              $this->stdMarkers["###SUBJECT###"] = $sub;
              return $sub;
          }

        	/**
        	* renderHeadImage
        	*
          * @param \int $lang
        	* @return string
        	*/
            function renderHeadImage($lang = 0) {
                $image = $this->emailsRepository->getSysImage($this->config,$this->config['uid'],'image'.$lang,"tx_phinewsletter_domain_model_config");
                $defaultImg = $this->config["configuration"]["templatePath"]. "/img/headerimage.jpg";
                $this->stdMarkers["###HEAD_IMG_SRC###"] = (isset($image["###IMAGE_SRC###"])?$image["###IMAGE_SRC###"]:"");
                $this->stdMarkers["###HEAD_IMG_NAME###"] = $image["###IMAGE_NAME###"];
            }

      	/**
      	* wrapInTemplate
      	*
      	* @param string $content
      	* @param string $lang
      	* @param string $hello
      	* @param string $mailTo
      	* @param string $user
      	* @param string $group
      	* @return string
      	*/
          function wrapInTemplate($content, $lang, $hello, $mailTo,$user,$group) {
      		 if (is_file($this->mainFile)) {
                  $html = file_get_contents($this->mainFile);
              } else {
                  return $content;
              }
              $link_url = $this->config['url'];

              $month = array(
                  array("Januar", "Februar", "M&auml;rz", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember"),
                  array("Janvier", "Fevrier", "Mars", "Avril", "Mai", "Juin", "Juillet", "Ao&ucirc;t", "Septembre", "Octobre", "Novembre", "D&eacute;cembre")
              );

      		$replace = $this->stdMarkers;

            $replace["###SUBHEADER###"] = $this->config["configuration"]["subheader"];
              $replace["###CONTENT###"] = $content;
              $replace["###URL###"] = $link_url;
              $replace["###MONTH###"] = date("m");
              $replace["###YEAR###"] = date("y");
              $replace["###TEMPLATE###"] = $this->config["configuration"]["templatePath"];

              $replace["###KEY_LANG###"] = $lang;
              $pref = "";
              //$replace = array();

          //old pref with br
      		//$pref = ((str_replace(array("<br>","<br/>"),array("BRTAG","BRTAG"),nl2br($this->config['prefix' . $lang]))));
      		$pref = $this->config['prefix' . $lang];

              $timestamp = time();

              //encoding error from the mailserver because of encoding issues
              $pref = ($pref);

              $replace["###DATELINE###"] = isset($this->config['configuration']['dateline'])?$this->config['configuration']['dateline']:"";

          	//$replace["###HEAD_IMAGE###"] = '<img src="'.$this->config["url"] . "/" . $this->getHeadImage().'" alt="head" />';

          	$replace["###HEAD###"] = str_replace("BRTAG", "<br>", $pref);
      		$replace["###EMPTY_HEAD###"] = strlen($pref) == 0?'display:none;':'';
      		$replace["###URL_LANG###"] = $this->lang_keys[$lang];
              //$replace["###EDITION###"] = $edition;

      		$replace["###LANG_UID###"] = $lang;
      		//$replace["###SUBJECT###"] = $this->config['subject'];


      		$edition = $this->stdMarkers["###EDITION###"];

      		//general markers with empty strings for the webview
      		$replace["###ANREDE###"] = $this->getLocal("hello_noname", $lang);
              $replace["###USER_EMAIL###"] = "";
      		$replace["###VIEW_HTML###"] = "";
      		$replace["###VIEW_HTML_LINK###"] = "";
      		$replace["###UNSUBSCRIBE###"] = "#";
          $replace["###PID###"] = $this->settings["statsStoragePid"];

              $file_html = str_replace(array_keys($replace), array_values($replace), $html);

            	$this->saveToFile($file_html,$lang,$mailTo,$edition);

      		$viewHTMLLink = $this->config['url'] . '/?newsletter_tracker[crc]'.hash('sha1',$this->hash_base).'&amp;newsletter_tracker[language]=' . $row['sys_language_uid'] . '&amp;no_cache=1&newsletter_tracker[newsid]=0&amp;newsletter_tracker[user]=0&amp;newsletter_tracker[edition]='.$edition.'&amp;newsletter_tracker[config]='.$this->config['uid'].'&amp;newsletter_tracker[newspage]=' . urlencode($this->savedToFile[$lang]);
      		$html_link = '<a href="'.$viewHTMLLink.'" target="_blank" style="color:#706a60;font-size:14px;">'.$this->getLocal("viewHtml",$lang).'</a>';

      		//personalized markers with empty strings for the webview
         		$replace["###ANREDE###"] = $hello;
              $replace["###USER_EMAIL###"] = $mailTo;
      		$replace["###VIEW_HTML###"] = $html_link;

      		$replace["###VIEW_HTML_LINK###"] = $viewHTMLLink;


            $uri = $this->uriBuilder->reset()->setCreateAbsoluteUri(TRUE)->setTargetPageUid($this->config["statuspageid"]);
            $unsubscribeUrl = $uri->build();
            if($this->emailsRepository->isNewslettergroup($group,$this->settings["newsletterGroupsPid"])){
              $replace["###UNSUBSCRIBE###"] = $unsubscribeUrl . "/?tx_phinewsletter_newsletterstatus[user]=".$user . "&amp;tx_phinewsletter_newsletterstatus[group]=".$group. "&amp;tx_phinewsletter_newsletterstatus[process]=unsubscribe";
            }else{
        		    $replace["###UNSUBSCRIBE_POST###"] = $replace["###UNSUBSCRIBE_PRE###"] = $replace["###UNSUBSCRIBE_TITLE###"] = "";
            }
              $mail_html = str_replace(array_keys($replace), array_values($replace), $html);
              $mail_html = str_replace(array_keys($replace), array_values($replace), $mail_html);

              return $mail_html;
          }

        	/**
        	* getHeadImage
        	*
        	* @return string
        	*/
        	function getHeadImage(){

        		$imageSql =	"SELECT identifier,name FROM sys_file LEFT JOIN sys_file_reference ON sys_file.uid = sys_file_reference.uid_local WHERE sys_file_reference.tablenames = 'tx_phinewsletter_domain_model_config' AND sys_file_reference.uid_foreign = " . $this->config["uid"] ." AND sys_file_reference.hidden = 0 AND sys_file_reference.deleted = 0 ORDER BY sys_file_reference.uid DESC LIMIT 1";

                $imageRes = $GLOBALS['TYPO3_DB']->sql_query($imageSql);
        		$resizedImage = "";
        		$imageName = "";
        		if($imageRow = $imageRes->fetch_assoc()){
        			$imageName = $this->config['filestorage'] . $imageRow["identifier"];
        		}
        		return $imageName;
        	}
      	/**
      	* saveToFile
      	*
      	* @param string $content
      	* @param string $lang
      	* @param string $mailTo
      	* @param string $edition
      	* @return string
      	*/
          function saveToFile($content, $lang, $mailTo,$edition) {

              $content = str_replace("charset=iso-8859-1", "charset=utf-8", $content);
              $content = str_replace("charset=ISO-8859-1", "charset=utf-8", $content);
              $content = str_replace('<meta http-equiv="X-UA-Compatible" content="IE=edge">', '<meta http-equiv="X-UA-Compatible" content="IE=edge"><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />', $content);
              $content = str_replace("###VIEW_HTML###", '<script type="text/javascript">if(window.location.search.indexOf(\'print\') > -1){window.print();}</script>', $content);
          		// newsletter_tracker[crc]

          		$content = preg_replace("/newsletter_tracker\[crc\]\=(.*?)\&amp;/","newsletter_tracker[crc]=". $this->hash_base ."&amp;",$content);
          		$content = preg_replace("/newsletter_tracker\[user\]\=(.*?)\&amp;/","newsletter_tracker[user]=0&amp;",$content);


              //$lKey = $lang == 1 ? 'fr' : ($lang == 2 ? 'default' : 'de');
              $basePath = Environment::getPublicPath() . '/';
              if (!is_dir($basePath . "uploads/phi_newsletter")) {
                  mkdir($basePath . "uploads/phi_newsletter", 0755);
              }
              if (!is_dir($basePath . "uploads/phi_newsletter/webview")) {
                  mkdir($basePath . "uploads/phi_newsletter/webview", 0755);
              }
          		$filename = "uploads/phi_newsletter/webview/newsletter-edition" . $edition . "-" . $lang . ".html";
              if (!isset($this->savedToFile[$lang]) && !is_file($filename)) {
                  file_put_contents($basePath . $filename, utf8_encode($content));
      	      }
              $this->savedToFile[$lang] = $this->config["url"] . "/" . $filename;
          }
}
