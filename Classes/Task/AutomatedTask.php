<?php
namespace Phi\PhiNewsletter\Task;

use Phi\PhiMiscAmedisch\Service\JWT;

use \TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\StartTimeRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\EndTimeRestriction;

class AutomatedTask extends \TYPO3\CMS\Scheduler\Task\AbstractTask {
    protected $passphrase = "IK=rnM?faiYKm9I672&d@nTUG!w8n{";

    /**
     * An additional indexing config
     *
     * @var string $conf
     */
    public $conf;

    /**
     * Associated Array of Config
     *
     * @var string $conf
     */
    public $assocConf;

	public function execute() {
			//...

		$this->parseConfig();

		$userStmt = $this->getFeusers();

		$newsUids = $this->getNewsContents($this->assocConf["newsCategory"]);
    $newsUids = array_keys($newsUids);
		$headNews = $this->getNewsContents($this->assocConf["headerCategory"]);
    $headNews = array_shift($headNews);
		$this->saveEmails($userStmt,$newsUids,$headNews);


			return true;
	}

	/**
	* parse the config string into an array with key=>values
	*
	* @return void
	*/
	public function parseConfig(){

		$config = explode(PHP_EOL,trim($this->conf));

		foreach($config as $c){
			$key = trim(substr($c,0,strpos($c,"=")));
			$val = trim(substr($c,strpos($c,"=")+1));
			if(strpos($key,"#") === false){
				if(in_array($key,array("pidMap","field2LanguageLabelMapper"))){
					$keyArray = explode(",",$val);
					foreach($keyArray as $v){
						$map = explode(":",$v);
						$this->assocConf[$key][$map[0]] = $map[1];

					}
				}else{
					$this->assocConf[$key] = $val;
				}
			}
		}
	}


  /**
   * function sendEmails
   *
   * @param \Statment $userStmt
   * @param \array $contents
   * @param \array $headNews
   * @return \bool
   */
  public function saveEmails($userStmt,$contents,$headNews)
    {
    $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
    $emailsRepository= $objectManager->get('Phi\PhiNewsletter\Domain\Repository\EmailsRepository');
  	//copy from addEmails from the EmailsRepo

  	while($row = $userStmt->fetch()){
      $addUser = false;

      if(($row["automailfrequency"] == 7 && date("N") == 1) || $row["automailfrequency"] == 1){
        $additionals = $this->getAdditionals(array("idf"=>$row["customuserid"],"username"=>$row["username"],"automailfrequency"=>$row["automailfrequency"]));
        $additionals["headnews"] =$headNews;
        $additionalstring = json_encode($additionals);
        //note: addEmail($user, $contents, $config,$tosendtime,$groupid, $additionals = [],$pid = 0)
        $emailsRepository->addEmail($row["uid"],$contents,$this->assocConf["config"],0,$this->assocConf["usergroup"],$additionalstring);
      }
  	}
  }

  /**
   * function getAdditionals
   *
   * @param \array $params
   * @return \void
   */
  public function getAdditionals($params){

    $payload = ["idf"=>$params["idf"],"username"=>$params["username"],"frequency_timespan"=>$params["automailfrequency"]];

    $token = $this->genereateJWTToken($payload);
    $ret = [
      "links"=>[
        "bag_link"=>[
          "uid"=>$this->assocConf["extendedArticlePage"],
          "additionalParams"=>[
            "tx_indizextendedarticledata_articledata[token]"=>$token
          ],
          "hash"=>"tabContentItemBag"
        ],
        "oos_link"=>[
          "uid"=>$this->assocConf["extendedArticlePage"],
          "additionalParams"=>[
            "tx_indizextendedarticledata_articledata[token]"=>$token
          ],
          "hash"=>"tabContentItemOos"
        ],
        "wfp_link"=>[
          "uid"=>$this->assocConf["extendedArticlePage"],
          "additionalParams"=>[
            "tx_indizextendedarticledata_articledata[token]"=>$token
          ],
          "hash"=>"tabContentItemWvb"
        ],
        "nla_link"=>[
          "uid"=>$this->assocConf["extendedArticlePage"],
          "additionalParams"=>[
            "tx_indizextendedarticledata_articledata[token]"=>$token
          ],
          "hash"=>"tabContentItemNal"
        ]
      ]
    ];

    return $ret;
  }

  /**
   * function getFeusers
   * load em from DB
   *
   * @return \Statement
   */
  public function getFeusers()
  {
    $table = 'fe_users';
    $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
    if(!isset($this->assocConf["usergroup"])){
  	   echo "set usergroup in task config";exit;
  	}
  	$group = $this->assocConf["usergroup"];
      $statement = $queryBuilder->select('*')->from($table)->
      orWhere(
        	$queryBuilder->expr()->like('usergroup', $queryBuilder->createNamedParameter("%," . $group.",%")),
  	$queryBuilder->expr()->like('usergroup', $queryBuilder->createNamedParameter("%," . $group)),
  	$queryBuilder->expr()->like('usergroup', $queryBuilder->createNamedParameter($group . ",%")),
  	$queryBuilder->expr()->eq('usergroup', $queryBuilder->createNamedParameter($group))
      )->execute();

  	return $statement;
  }


  /**
   * function compareSingleAvailabilites
   * load em from DB
   *
   * @return \array
   */
   public function getNewsContents($cat)
  {
	if(!isset($this->assocConf["newsTable"])){
		echo "set newsTable in the Task Conf";exit;
	}
	 if(!isset($this->assocConf["newsCategory"])){
                echo "set newsCategory in the Task Conf";exit;
        }

    $table = $this->assocConf["newsTable"];
    $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);


    $statement = $queryBuilder->select('*')->from($table)->
    orWhere(
       $queryBuilder->expr()->like('category', $queryBuilder->createNamedParameter("%," . $cat.",%")),
        $queryBuilder->expr()->like('category', $queryBuilder->createNamedParameter("%," . $cat)),
        $queryBuilder->expr()->like('category', $queryBuilder->createNamedParameter($cat . ",%")),
        $queryBuilder->expr()->eq('category', $queryBuilder->createNamedParameter($cat))
    )->execute();

	$allnews = [];
	while($news = $statement->fetch()){
		$allnews[$news["uid"]] = $news;
	}
    return $allnews;
  }

  /**
   * function loadSingleAvailabilityEmails
   * load em from DB
   *
   * @return \array
   */
  public function loadSingleAvailabilityEmails()
  {
    $token = $this->genereateJWTToken(["action"=>"load"]);
    exec("php /home/www-data/interface.iamedis.ch/handle_availability_recipients.php " . $token,$output);
    $notifications = json_decode($output[0],true);
    return $notifications;
  }



  /**
   * function callIndexer
   *
   * @param \int $storageUid
   * @return \void
   */
  public function callIndexer($storageUid){
    $storage = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Scheduler\Task\FileStorageIndexingTask::class);
    $storage->storageUid = $storageUid;
    $storage->execute();
  }

  /**
  * getJWTToken
  *
  * @param \array $payload
  * @return \string
  */
  private function genereateJWTToken($payload)
  {
    $secret = "file:///home/www-data/openssl/jwt/private.pem";
    $oneday = 3600 * 24;

    $livetime = isset($payload["frequency_timespan"])?$payload["frequency_timespan"] * $oneday:$oneday;
    $jwt = new JWT($secret, 'RS256', $livetime, 10,$this->passphrase);

    return $jwt->encode($payload);//token
  }
}
