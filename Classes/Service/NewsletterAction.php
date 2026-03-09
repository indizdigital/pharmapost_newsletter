<?php
namespace Phi\PhiNewsletter\Service;

/***
 *
 * This file is part of the "Phi HCI Information" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019
 * process function will be triggered by the Middleware Concept if typo3. Registered in ext_localconf
 *
 ***/
 use \Psr\Http\Server\RequestHandlerInterface;
 use \Psr\Http\Message\ServerRequestInterface;
 use \Psr\Http\Message\ResponseInterface;
 use \TYPO3\CMS\Core\Utility\GeneralUtility;
 use \TYPO3\CMS\Core\Context\Context;
 use TYPO3\CMS\Core\Database\ConnectionPool;
 use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;
 use TYPO3\CMS\Core\Http\RedirectResponse;
 use TYPO3\CMS\Extbase\Annotation\Inject;
 use TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository;
 use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * NewsletterAction
 */
class NewsletterAction implements \Psr\Http\Server\MiddlewareInterface
{
    protected $user = null;

    protected $hash_base = ":`OknkA.tfTXq4[P'Wb>u/@##^Hz}r";



    /**
     * feuserRepository
     *
     * @var TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository
     * @Inject
     */
     protected $feuserRepository;

    /**
     * download Middleware
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler):ResponseInterface
    {

        $params = $request->getQueryParams();

        if(isset($params["newsletter_tracker"])){
          $gotolink = $this->processClick($params["newsletter_tracker"]);

          if(strlen($gotolink)){
            $redirect = $this->getRedirectResponse($gotolink);
            return $redirect;
          }
          $config = $this->loadConfig($params["newsletter_tracker"]);
          $newslink = isset($params["newsletter_tracker"]["newslink"])?$params["newsletter_tracker"]["newslink"]:"";

          if(strlen($newslink)){
            $redirect = $this->getRedirectResponse($newslink);
            return $redirect;
          }
        }
        if(isset($params["newsletter_openrate"])){
          $fileArray = $this->processOpenrate($params["newsletter_openrate"]);
          $this->displayImage();

        }

        return $handler->handle($request);
    }

    /**
     * displayImage
     *
     * @return \void
     */
    public function displayImage()
    {

      printf ('%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%', 71,73,70,56,57,97,1,0,1,0,128,255,0,192,192,192,0,0,0,33,249,4,1,0,0,0,0,44,0,0,0,0,1,0,1,0,0,2,2,68,1,0,59);
      // Das Bild an den Browser ausgeben
      header('Content-Type: image/gif');
      exit;
    }
    /**
     * goto
     *
     * @param \string $targetUrl
     * @return \RedirectResponse
     */
    public function getRedirectResponse($targetUrl = "")
    {
        return new RedirectResponse($targetUrl, 401);
    }
    /**
     * processOpenrate
     *
     * @param \array $openrate
     * @return \void
     */
    public function processOpenrate($openrate)
    {

        $vals = array(
        	"user"=>$openrate["user"],
        	"config"=>$openrate["config"],
        	"sys_language_uid"=>$openrate["language"],
        	"ip"=>$_SERVER['REMOTE_ADDR'],
        	"pid"=>$openrate['pid'],
        	"crdate"=>time()
        );


        $filename = '';

        $table = "tx_phinewsletter_domain_model_openrate";
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);

        $statement = $queryBuilder->insert($table)->values(
          $vals
        )->execute();

    }
    /**
     * processClick
     *
     * @param \string $newslettertracking
     * @return \void
     */
    public function processClick($tracker)
    {

      if($tracker["crc"] != hash('sha1',$this->hash_base)){
        return;
      }
      $itemid = (isset($tracker["itemid"])?:0);
      if($itemid == 0 && strpos($tracker["newslink"],"#") !== false){
        $elementFromLists = substr($tracker["newslink"],strpos($tracker["newslink"],"#"));
        switch($elementFromLists){
          case "#tabContentItemWvb":
            $itemid = 1;
            break;
          case "#tabContentItemBag":
            $itemid = 2;
            break;
          case "#tabContentItemNal":
            $itemid = 3;
            break;
          case "#tabContentItemOos":
            $itemid = 4;
            break;
        }
      }
      $vals = array(
      	"user"=>$tracker["user"],
      	"itemid"=>$tracker["newsid"],
      	"config"=>$tracker["config"],
      	"sys_language_uid"=>$tracker["language"],
      	"ip"=>$_SERVER['REMOTE_ADDR'],
      	"pid"=>$tracker['pid'],
      	"crdate"=>time()
      );
      //add file links
      $goto = "";
      if(isset($tracker["filelink"])){
        $vals["filelink"] = $tracker["filelink"];
        $goto = urldecode($tracker["filelink"]);
      }


        $filename = '';

        $table = "tx_phinewsletter_domain_model_clickrate";
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);

        $statement = $queryBuilder->insert($table)->values(
          $vals
        )->execute();

        return $goto;

    }

    /**
     * loadConfig
     *
     * @param \array $tracker
     * @return \void
     */
    public function loadConfig($tracker)
    {
        if(!isset($tracker['config']) || $tracker['config'] == 0){
          return [];
        }
        $table = "tx_phinewsletter_domain_model_config";
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);

        $res = $queryBuilder->select("*")->from($table)->where(
          $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($tracker['config']))
        )->execute();

        $row = [];
        if($row = $res->fetch()){
        	$conf = $row['configuration'];
        	unset($row['configuration']);
        	$confArray = explode(PHP_EOL,$conf);
        	foreach($confArray as $line){
        		$lineArray = explode("=",$line);
        		$key = trim($lineArray[0]);
        		$row['configuration'][$key] = trim($lineArray[1]);
        	}

        }
        return $row;

    }

}
